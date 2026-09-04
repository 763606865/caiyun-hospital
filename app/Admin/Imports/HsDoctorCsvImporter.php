<?php

namespace App\Admin\Imports;

use App\Models\HsDepartment;
use App\Models\HsDoctor;
use Illuminate\Support\Facades\DB;
use Throwable;

class HsDoctorCsvImporter extends CsvImporter
{
    public static function headers(): array
    {
        return [
            '医生姓名',
            'Slug',
            '职称',
            '擅长',
            '简介',
            '详细介绍',
            '挂号费',
            '排序',
            '是否启用',
            '科室1',
            '科室2',
            '科室3',
            '主科室',
        ];
    }

    public static function exampleRows(): array
    {
        $departments = static::departmentDropdownLabels();
        $first = $departments[0] ?? '心内科 [cardiology]';
        $second = $departments[1] ?? $first;

        return [
            ['张伟', 'zhang-wei', '主任医师', '冠心病介入治疗', '从医20年', '医生详细介绍…', 50, 10, '是', $first, '', '', $first],
            ['李娜', 'li-na', '副主任医师', '脑血管病', '擅长脑卒中急救', '医生详细介绍…', 30, 20, '是', $first, $second, '', $first],
        ];
    }

    public static function templateFilename(): string
    {
        return '医生导入模板.xlsx';
    }

    public static function dropdownOptions(): array
    {
        $departments = static::departmentDropdownLabels();
        $departmentOptions = $departments !== [] ? $departments : ['请先在后台创建科室'];

        return [
            '职称' => static::doctorTitleOptions(),
            '是否启用' => static::booleanOptions(),
            '科室1' => $departmentOptions,
            '科室2' => $departmentOptions,
            '科室3' => $departmentOptions,
            '主科室' => $departmentOptions,
        ];
    }

    /**
     * @param  array<string, string>  $row
     * @return list<string>
     */
    protected function importRow(array $row, int $line): array
    {
        $name = $row['医生姓名'] ?? '';
        $slug = $row['Slug'] ?? '';

        $departmentSlugs = collect([
            $this->extractSlug($row['科室1'] ?? ''),
            $this->extractSlug($row['科室2'] ?? ''),
            $this->extractSlug($row['科室3'] ?? ''),
        ])->filter()->unique()->values();

        // 兼容旧模板「科室Slug列表」
        if ($departmentSlugs->isEmpty() && filled($row['科室Slug列表'] ?? null)) {
            $departmentSlugs = collect(preg_split('/[,，|｜]+/u', (string) $row['科室Slug列表']) ?: [])
                ->map(fn (string $value): string => $this->extractSlug($value))
                ->filter()
                ->unique()
                ->values();
        }

        if ($name === '' || $slug === '' || $departmentSlugs->isEmpty()) {
            return ["第 {$line} 行：医生姓名、Slug、科室1 为必填项。"];
        }

        if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
            return ["第 {$line} 行：Slug 仅允许小写字母、数字和连字符。"];
        }

        $departments = HsDepartment::query()
            ->whereIn('slug', $departmentSlugs->all())
            ->get()
            ->keyBy('slug');

        $missing = $departmentSlugs->reject(fn (string $item): bool => $departments->has($item))->values();

        if ($missing->isNotEmpty()) {
            return ["第 {$line} 行：科室不存在：".$missing->implode('、')];
        }

        $primarySlug = $this->extractSlug($row['主科室'] ?? ($row['主科室Slug'] ?? ''));
        if ($primarySlug === '') {
            $primarySlug = (string) $departmentSlugs->first();
        }

        if (! $departmentSlugs->contains($primarySlug)) {
            return ["第 {$line} 行：主科室必须是已选科室之一。"];
        }

        $fee = $row['挂号费'] ?? '';
        if ($fee !== '' && ! is_numeric($fee)) {
            return ["第 {$line} 行：挂号费必须是数字。"];
        }

        try {
            DB::transaction(function () use ($row, $name, $slug, $departments, $departmentSlugs, $primarySlug, $fee): void {
                $doctor = HsDoctor::withTrashed()->firstOrNew(['slug' => $slug]);

                if ($doctor->trashed()) {
                    $doctor->restore();
                }

                $doctor->fill([
                    'name' => $name,
                    'title' => ($row['职称'] ?? '') !== '' ? $row['职称'] : null,
                    'specialties' => ($row['擅长'] ?? '') !== '' ? $row['擅长'] : null,
                    'summary' => ($row['简介'] ?? '') !== '' ? $row['简介'] : null,
                    'body' => ($row['详细介绍'] ?? '') !== '' ? $row['详细介绍'] : null,
                    'fee' => $fee !== '' ? $fee : 0,
                    'sort' => $this->parseInt($row['排序'] ?? '', 0),
                    'is_enabled' => $this->parseBoolean($row['是否启用'] ?? '', true),
                ]);
                $doctor->save();

                $sync = [];
                foreach ($departmentSlugs as $departmentSlug) {
                    $departmentId = $departments->get($departmentSlug)->id;
                    $sync[$departmentId] = [
                        'is_primary' => $departmentSlug === $primarySlug,
                    ];
                }

                $doctor->departments()->sync($sync);
            });
        } catch (Throwable $exception) {
            report($exception);

            return ["第 {$line} 行：保存失败（{$exception->getMessage()}）。"];
        }

        return [];
    }
}
