<?php

namespace App\Admin\Imports;

use App\Models\HsCampus;
use App\Models\HsDepartment;
use App\Models\HsDepartmentCategory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

abstract class CsvImporter
{
    protected const TEMPLATE_DATA_ROWS = 200;

    /**
     * @return list<string>
     */
    abstract public static function headers(): array;

    /**
     * @return list<list<string|int|float|null>>
     */
    abstract public static function exampleRows(): array;

    abstract public static function templateFilename(): string;

    /**
     * 可下拉列配置：表头 => 选项列表。
     *
     * @return array<string, list<string>>
     */
    abstract public static function dropdownOptions(): array;

    /**
     * @param  array<string, string>  $row
     * @return list<string>
     */
    abstract protected function importRow(array $row, int $line): array;

    public static function downloadTemplate(): StreamedResponse
    {
        $filename = static::templateFilename();

        return response()->streamDownload(function (): void {
            $spreadsheet = static::buildTemplateSpreadsheet();
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public static function buildTemplateSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('导入数据');

        $headers = static::headers();
        foreach ($headers as $index => $header) {
            $sheet->setCellValue([$index + 1, 1], $header);
        }

        foreach (static::exampleRows() as $rowIndex => $row) {
            foreach ($row as $columnIndex => $value) {
                $sheet->setCellValue([$columnIndex + 1, $rowIndex + 2], $value);
            }
        }

        $optionsSheet = $spreadsheet->createSheet();
        $optionsSheet->setTitle('下拉选项');
        static::writeDropdownOptionsSheet($optionsSheet, static::dropdownOptions());
        static::applyDropdownValidations($sheet, $headers, static::dropdownOptions());

        foreach (range(1, count($headers)) as $column) {
            $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    /**
     * @return array{success: int, failed: int, errors: list<string>}
     */
    public function import(TemporaryUploadedFile|UploadedFile|string $file): array
    {
        $path = $this->resolveLocalPath($file);
        $extension = Str::lower(pathinfo($path, PATHINFO_EXTENSION));

        $rows = match ($extension) {
            'xlsx', 'xls' => $this->readSpreadsheetRows($path),
            'csv', 'txt' => $this->readCsvRows($path),
            default => throw new RuntimeException('仅支持上传 .xlsx / .csv 文件，请先下载最新导入模板。'),
        };

        if ($rows === []) {
            throw new RuntimeException('导入文件为空或缺少表头。');
        }

        $headers = array_map(fn ($header) => $this->normalizeHeader((string) $header), $rows[0]);
        $expected = array_map(fn (string $header) => $this->normalizeHeader($header), static::headers());

        foreach ($expected as $column) {
            if (! in_array($column, $headers, true)) {
                throw new RuntimeException("缺少必要列：{$column}。请先下载最新导入模板。");
            }
        }

        $success = 0;
        $failed = 0;
        $errors = [];

        foreach (array_slice($rows, 1) as $offset => $values) {
            $line = $offset + 2;

            if ($this->isEmptyRow($values)) {
                continue;
            }

            $row = [];
            foreach ($headers as $index => $header) {
                $row[$header] = trim((string) ($values[$index] ?? ''));
            }

            $rowErrors = $this->importRow($row, $line);

            if ($rowErrors !== []) {
                $failed++;
                array_push($errors, ...$rowErrors);

                if (count($errors) >= 50) {
                    $errors[] = '错误过多，已停止继续列出，请修正后重新导入。';
                    break;
                }

                continue;
            }

            $success++;
        }

        return [
            'success' => $success,
            'failed' => $failed,
            'errors' => $errors,
        ];
    }

    /**
     * @param  array<string, list<string>>  $dropdownOptions
     */
    protected static function writeDropdownOptionsSheet(Worksheet $sheet, array $dropdownOptions): void
    {
        $column = 1;

        foreach ($dropdownOptions as $title => $options) {
            $sheet->setCellValue([$column, 1], $title);

            foreach (array_values($options) as $rowIndex => $option) {
                $sheet->setCellValue([$column, $rowIndex + 2], $option);
            }

            $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
            $column++;
        }

        if ($dropdownOptions === []) {
            $sheet->setCellValue([1, 1], '无可下拉选项');
        }
    }

    /**
     * @param  list<string>  $headers
     * @param  array<string, list<string>>  $dropdownOptions
     */
    protected static function applyDropdownValidations(Worksheet $sheet, array $headers, array $dropdownOptions): void
    {
        $optionColumnIndex = 1;

        foreach ($dropdownOptions as $header => $options) {
            if ($options === []) {
                $optionColumnIndex++;

                continue;
            }

            $headerIndex = array_search($header, $headers, true);
            if ($headerIndex === false) {
                $optionColumnIndex++;

                continue;
            }

            $columnLetter = Coordinate::stringFromColumnIndex($headerIndex + 1);
            $optionColumnLetter = Coordinate::stringFromColumnIndex($optionColumnIndex);
            $lastOptionRow = count($options) + 1;
            $formula = sprintf("'下拉选项'!$%s$2:$%s$%d", $optionColumnLetter, $optionColumnLetter, $lastOptionRow);

            for ($row = 2; $row <= self::TEMPLATE_DATA_ROWS + 1; $row++) {
                $validation = $sheet->getCell("{$columnLetter}{$row}")->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_STOP);
                $validation->setAllowBlank(true);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('输入错误');
                $validation->setError('请从下拉列表中选择有效选项。');
                $validation->setPromptTitle($header);
                $validation->setPrompt('请从下拉列表选择');
                $validation->setFormula1($formula);
            }

            $optionColumnIndex++;
        }
    }

    /**
     * @return list<list<string>>
     */
    protected function readSpreadsheetRows(string $path): array
    {
        try {
            $spreadsheet = IOFactory::load($path);
        } catch (Throwable $exception) {
            report($exception);

            throw new RuntimeException('无法读取 Excel 文件，请确认文件未损坏且为 .xlsx 格式。');
        }

        $sheet = $spreadsheet->getSheetByName('导入数据') ?? $spreadsheet->getActiveSheet();
        $rows = [];

        foreach ($sheet->toArray(null, true, true, false) as $row) {
            /** @var list<mixed> $row */
            $rows[] = array_map(static fn ($value): string => trim((string) ($value ?? '')), $row);
        }

        $spreadsheet->disconnectWorksheets();

        return $rows;
    }

    /**
     * @return list<list<string>>
     */
    protected function readCsvRows(string $path): array
    {
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new RuntimeException('无法读取导入文件。');
        }

        try {
            $rows = [];

            while (($values = fgetcsv($handle, length: null, separator: ',', enclosure: '"', escape: '\\')) !== false) {
                $rows[] = array_map(static fn ($value): string => trim((string) ($value ?? '')), $values);
            }

            return $rows;
        } finally {
            fclose($handle);
        }
    }

    protected function normalizeHeader(string $header): string
    {
        $header = preg_replace('/^\xEF\xBB\xBF/', '', $header) ?? $header;

        return trim($header);
    }

    /**
     * @param  list<string|null>|false  $values
     */
    protected function isEmptyRow(array|false $values): bool
    {
        if ($values === false) {
            return true;
        }

        foreach ($values as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    protected function parseBoolean(string $value, bool $default = true): bool
    {
        if ($value === '') {
            return $default;
        }

        $normalized = Str::lower(trim($value));

        return in_array($normalized, ['1', 'true', 'yes', 'y', '是', '启用', '开启'], true);
    }

    protected function parseInt(string $value, int $default = 0): int
    {
        if ($value === '' || ! is_numeric($value)) {
            return $default;
        }

        return (int) $value;
    }

    /**
     * @return list<string>
     */
    protected static function booleanOptions(): array
    {
        return ['是', '否'];
    }

    /**
     * @return list<string>
     */
    protected static function doctorTitleOptions(): array
    {
        return ['主任医师', '副主任医师', '主治医师', '住院医师', '医师'];
    }

    /**
     * 从「名称 [slug]」或纯 slug 中解析 slug。
     */
    protected function extractSlug(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        if (preg_match('/\[([a-z0-9]+(?:-[a-z0-9]+)*)\]\s*$/i', $value, $matches) === 1) {
            return Str::lower($matches[1]);
        }

        return $value;
    }

    /**
     * @return list<string>
     */
    protected static function campusDropdownLabels(): array
    {
        return HsCampus::query()
            ->orderBy('sort')
            ->get(['name', 'slug'])
            ->map(fn ($campus): string => "{$campus->name} [{$campus->slug}]")
            ->all();
    }

    /**
     * @return list<string>
     */
    protected static function departmentDropdownLabels(): array
    {
        return HsDepartment::query()
            ->orderBy('sort')
            ->get(['name', 'slug'])
            ->map(fn ($department): string => "{$department->name} [{$department->slug}]")
            ->all();
    }

    /**
     * @return list<string>
     */
    protected static function departmentCategoryDropdownLabels(): array
    {
        return HsDepartmentCategory::query()
            ->enabled()
            ->orderBy('sort')
            ->orderBy('id')
            ->get(['name', 'slug'])
            ->map(fn (HsDepartmentCategory $category): string => "{$category->name} [{$category->slug}]")
            ->all();
    }

    protected function resolveLocalPath(TemporaryUploadedFile|UploadedFile|string $file): string
    {
        if (is_string($file)) {
            $path = Storage::disk('local')->path($file);

            if (! is_file($path)) {
                throw new RuntimeException('导入文件不存在或已过期，请重新上传。');
            }

            return $path;
        }

        if ($file instanceof TemporaryUploadedFile) {
            return $file->getRealPath();
        }

        return $file->getRealPath();
    }
}
