<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['organization_id', 'prescription_id', 'drug_id', 'drug_name', 'specification', 'single_dose', 'quantity', 'unit', 'unit_price', 'amount', 'processing_method', 'usage_method', 'sort'])]
class PrescriptionItem extends Model
{
    use BelongsToOrganization;

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    public function drug(): BelongsTo
    {
        return $this->belongsTo(Drug::class);
    }
}
