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

    /** @return BelongsTo<Branch, $this> */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /** @return BelongsTo<HsPatient, $this> */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(HsPatient::class, 'patient_id');
    }

    /** @return BelongsTo<HsDoctor, $this> */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(HsDoctor::class, 'doctor_id');
    }

    /** @return BelongsTo<HsDepartment, $this> */
    public function department(): BelongsTo
    {
        return $this->belongsTo(HsDepartment::class, 'department_id');
    }

    /** @return HasOne<MedicalRecord, $this> */
    public function medicalRecord(): HasOne
    {
        return $this->hasOne(MedicalRecord::class);
    }

    /** @return HasMany<Prescription, $this> */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }
}
