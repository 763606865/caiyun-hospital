<?php

namespace App\Admin\Resources\HsDoctors\Concerns;

use App\Models\HsDoctor;

/**
 * @property-read HsDoctor $record
 */
trait SyncsPrimaryDepartment
{
    protected ?int $primaryDepartmentId = null;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function extractPrimaryDepartmentId(array $data): array
    {
        $primaryId = $data['primary_department_id'] ?? null;
        $this->primaryDepartmentId = is_numeric($primaryId) ? (int) $primaryId : null;
        unset($data['primary_department_id']);

        return $data;
    }

    protected function syncPrimaryDepartment(): void
    {
        $departmentIds = $this->record->departments()->pluck('hs_departments.id')->all();

        if ($departmentIds === []) {
            return;
        }

        $primaryId = $this->primaryDepartmentId;
        if ($primaryId === null || ! in_array($primaryId, array_map('intval', $departmentIds), true)) {
            $primaryId = (int) $departmentIds[0];
        }

        $sync = [];
        foreach ($departmentIds as $departmentId) {
            $sync[(int) $departmentId] = [
                'is_primary' => (int) $departmentId === $primaryId,
            ];
        }

        $this->record->departments()->sync($sync);
    }
}
