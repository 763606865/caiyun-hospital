<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable(['name', 'code', 'status', 'contact_name', 'contact_phone', 'timezone', 'settings'])]
class Organization extends Model
{
    use SoftDeletes;

    protected static function booted(): void
    {
        static::creating(fn (self $organization) => $organization->uuid ??= (string) Str::uuid7());
    }

    protected function casts(): array
    {
        return ['settings' => 'array'];
    }

    /** @return HasMany<Branch, $this> */
    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    /** @return HasMany<OrganizationMember, $this> */
    public function organizationMembers(): HasMany
    {
        return $this->hasMany(OrganizationMember::class);
    }

    /** @return BelongsToMany<AdminUser, $this> */
    public function adminUsers(): BelongsToMany
    {
        return $this->belongsToMany(AdminUser::class, 'organization_members')->withTimestamps();
    }

    public function hsCampuses(): HasMany
    {
        return $this->hasMany(HsCampus::class);
    }

    public function hsDepartments(): HasMany
    {
        return $this->hasMany(HsDepartment::class);
    }

    public function hsDepartmentCategories(): HasMany
    {
        return $this->hasMany(HsDepartmentCategory::class);
    }

    public function hsDoctors(): HasMany
    {
        return $this->hasMany(HsDoctor::class);
    }

    public function hsPatients(): HasMany
    {
        return $this->hasMany(HsPatient::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function drugs(): HasMany
    {
        return $this->hasMany(Drug::class);
    }

    public function inventoryBatches(): HasMany
    {
        return $this->hasMany(InventoryBatch::class);
    }

    public function inventoryStocks(): HasMany
    {
        return $this->hasMany(InventoryStock::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function chargeOrders(): HasMany
    {
        return $this->hasMany(ChargeOrder::class);
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function dailySettlements(): HasMany
    {
        return $this->hasMany(DailySettlement::class);
    }
}
