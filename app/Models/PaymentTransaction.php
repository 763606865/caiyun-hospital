<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['organization_id', 'branch_id', 'charge_order_id', 'operator_member_id', 'transaction_no', 'type', 'method', 'channel', 'status', 'amount', 'external_transaction_no', 'related_transaction_id', 'channel_payload', 'completed_at'])]
class PaymentTransaction extends Model
{
    use BelongsToOrganization;

    protected function casts(): array
    {
        return ['channel_payload' => 'array', 'completed_at' => 'datetime'];
    }

    /** @return BelongsTo<Branch, $this> */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /** @return BelongsTo<ChargeOrder, $this> */
    public function chargeOrder(): BelongsTo
    {
        return $this->belongsTo(ChargeOrder::class);
    }

    /** @return BelongsTo<self, $this> */
    public function relatedTransaction(): BelongsTo
    {
        return $this->belongsTo(self::class, 'related_transaction_id');
    }
}
