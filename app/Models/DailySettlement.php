<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['organization_id', 'branch_id', 'settled_by_member_id', 'settlement_no', 'business_date', 'status', 'order_count', 'payment_count', 'refund_count', 'receivable_amount', 'discount_amount', 'received_amount', 'refunded_amount', 'net_amount', 'payment_method_summary', 'difference_details', 'remark', 'settled_at'])]
class DailySettlement extends Model
{
    use BelongsToOrganization;

    protected function casts(): array
    {
        return ['business_date' => 'date', 'payment_method_summary' => 'array', 'difference_details' => 'array', 'settled_at' => 'datetime'];
    }

    /** @return BelongsTo<Branch, $this> */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
