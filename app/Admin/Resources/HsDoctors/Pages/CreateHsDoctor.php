<?php

namespace App\Admin\Resources\HsDoctors\Pages;

use App\Admin\Resources\HsDoctors\Concerns\SyncsPrimaryDepartment;
use App\Admin\Resources\HsDoctors\HsDoctorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHsDoctor extends CreateRecord
{
    use SyncsPrimaryDepartment;

    protected static string $resource = HsDoctorResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->extractPrimaryDepartmentId($data);
    }

    protected function afterCreate(): void
    {
        $this->syncPrimaryDepartment();
    }
}
