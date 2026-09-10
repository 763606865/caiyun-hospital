<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['organization_id', 'branch_id', 'drug_id', 'batch_no', 'produced_at', 'expires_at', 'purchase_price', 'quantity', 'locked_quantity'])]
class InventoryBatch extends Model
{
    use BelongsToOrganization;

    protected function casts(): array
    {
        return ['produced_at' => 'date', 'expires_at' => 'date'];
    }

    /** @return BelongsTo<Branch, $this> */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /** @return BelongsTo<Drug, $this> */
    public function drug(): BelongsTo
    {
        return $this->belongsTo(Drug::class);
    }
}
