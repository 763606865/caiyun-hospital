<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['organization_id', 'branch_id', 'drug_id', 'inventory_batch_id', 'operator_member_id', 'source_type', 'source_id', 'movement_no', 'type', 'quantity', 'quantity_before', 'quantity_after', 'unit_cost', 'remark', 'occurred_at'])]
class InventoryMovement extends Model
{
    use BelongsToOrganization;

    protected function casts(): array
    {
        return ['occurred_at' => 'datetime'];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function drug(): BelongsTo
    {
        return $this->belongsTo(Drug::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(InventoryBatch::class, 'inventory_batch_id');
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}
