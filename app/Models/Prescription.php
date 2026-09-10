<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['organization_id', 'branch_id', 'visit_id', 'medical_record_id', 'patient_id', 'doctor_id', 'prescription_no', 'type', 'status', 'dose_count', 'administration', 'frequency', 'usage_instructions', 'remark', 'issued_at', 'dispensed_at'])]
class Prescription extends Model
{
    use BelongsToOrganization, SoftDeletes;

    protected function casts(): array
    {
        return ['issued_at' => 'datetime', 'dispensed_at' => 'datetime'];
    }

    /** @return BelongsTo<Branch, $this> */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /** @return BelongsTo<Visit, $this> */
    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
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

    /** @return HasMany<PrescriptionItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}
