<?php

namespace App\Admin\Resources\HsDoctors\Pages;

use App\Admin\Resources\HsDoctors\Concerns\SyncsPrimaryDepartment;
use App\Admin\Resources\HsDoctors\HsDoctorResource;
use App\Models\HsDoctor;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

/**
 * @property-read HsDoctor $record
 */
class EditHsDoctor extends EditRecord
{
    use SyncsPrimaryDepartment;

    protected static string $resource = HsDoctorResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['primary_department_id'] = $this->record->departments()
            ->wherePivot('is_primary', true)
            ->value('hs_departments.id');

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->extractPrimaryDepartmentId($data);
    }

    protected function afterSave(): void
    {
        $this->syncPrimaryDepartment();
    }

    public function getTitle(): string|Htmlable
    {
        $recordTitle = $this->getRecordTitle();

        return '配置 · '.($recordTitle instanceof Htmlable ? $recordTitle->toHtml() : $recordTitle);
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return '基本信息';
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            RestoreAction::make(),
            ForceDeleteAction::make(),
        ];
    }
}
