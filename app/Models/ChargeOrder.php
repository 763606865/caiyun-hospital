<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['organization_id', 'branch_id', 'visit_id', 'patient_id', 'created_by_member_id', 'order_no', 'status', 'original_amount', 'discount_amount', 'receivable_amount', 'paid_amount', 'refunded_amount', 'paid_at', 'cancelled_at', 'remark'])]
class ChargeOrder extends Model
{
    use BelongsToOrganization, SoftDeletes;

    protected function casts(): array
    {
        return ['paid_at' => 'datetime', 'cancelled_at' => 'datetime'];
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

    /** @return HasMany<ChargeItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(ChargeItem::class);
    }

    /** @return HasMany<PaymentTransaction, $this> */
    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }
}
