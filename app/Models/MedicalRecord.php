<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['organization_id', 'branch_id', 'visit_id', 'patient_id', 'doctor_id', 'present_illness', 'past_history', 'allergy_history', 'personal_history', 'family_history', 'tcm_inspection', 'tcm_auscultation_olfaction', 'tcm_inquiry', 'tcm_palpation', 'tcm_syndrome', 'tcm_treatment_principle', 'diagnoses', 'vital_signs', 'treatment_plan', 'medical_advice', 'signed_at', 'signed_by_member_id'])]
class MedicalRecord extends Model
{
    use BelongsToOrganization;

    protected function casts(): array
    {
        return ['diagnoses' => 'array', 'vital_signs' => 'array', 'signed_at' => 'datetime'];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(HsPatient::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(HsDoctor::class, 'doctor_id');
    }
}
