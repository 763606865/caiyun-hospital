<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['organization_id', 'charge_order_id', 'source_type', 'source_id', 'item_type', 'item_code', 'item_name', 'quantity', 'unit', 'unit_price', 'discount_amount', 'amount'])]
class ChargeItem extends Model
{
    use BelongsToOrganization;

    public function chargeOrder(): BelongsTo
    {
        return $this->belongsTo(ChargeOrder::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}
