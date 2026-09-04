<?php

namespace App\Admin\Imports\Concerns;

use App\Admin\Imports\CsvImporter;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * @template TImporter of CsvImporter
 */
trait HasCsvImportActions
{
    /**
     * @return class-string<TImporter>
     */
    abstract protected function csvImporterClass(): string;

    abstract protected function csvImportPermission(): string;

    /**
     * @return array<int, Action>
     */
    protected function getCsvImportHeaderActions(): array
    {
        $importerClass = $this->csvImporterClass();
        $canImport = fn (): bool => (bool) auth('admin')->user()?->can($this->csvImportPermission());

        return [
            Action::make('downloadImportTemplate')
                ->label('下载导入模板')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->visible($canImport)
                ->action(fn () => $importerClass::downloadTemplate()),
            Action::make('importCsv')
                ->label('批量导入')
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->visible($canImport)
                ->form([
                    FileUpload::make('file')
                        ->label('Excel 文件')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                            'text/csv',
                            'text/plain',
                            'application/csv',
                        ])
                        ->disk('local')
                        ->directory('imports')
                        ->visibility('private')
                        ->required()
                        ->helperText('请先下载 Excel 模板，通过下拉选择院区/科室等字段后上传（.xlsx）。'),
                ])
                ->action(function (array $data) use ($importerClass): void {
                    /** @var TemporaryUploadedFile|string $file */
                    $file = $data['file'];
                    $result = (new $importerClass)->import($file);

                    $notification = Notification::make()
                        ->title('导入完成')
                        ->body("成功 {$result['success']} 条，失败 {$result['failed']} 条。");

                    if ($result['failed'] > 0) {
                        $notification
                            ->warning()
                            ->body(
                                "成功 {$result['success']} 条，失败 {$result['failed']} 条。\n"
                                .implode("\n", $result['errors'])
                            );
                    } else {
                        $notification->success();
                    }

                    $notification->persistent()->send();
                }),
        ];
    }
}
