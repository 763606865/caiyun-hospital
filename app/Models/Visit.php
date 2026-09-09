<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['organization_id', 'branch_id', 'patient_id', 'doctor_id', 'department_id', 'registered_by_member_id', 'visit_no', 'queue_no', 'type', 'status', 'chief_complaint', 'registered_at', 'started_at', 'completed_at', 'cancelled_at', 'cancel_reason'])]
class Visit extends Model
{
    use BelongsToOrganization, SoftDeletes;

    protected function casts(): array
    {
        return ['registered_at' => 'datetime', 'started_at' => 'datetime', 'completed_at' => 'datetime', 'cancelled_at' => 'datetime'];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(HsPatient::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(HsDoctor::class, 'doctor_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(HsDepartment::class, 'department_id');
    }

    public function medicalRecord(): HasOne
    {
        return $this->hasOne(MedicalRecord::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }
}
