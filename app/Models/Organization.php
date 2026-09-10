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

    /** @return HasMany<HsCampus, $this> */
    public function hsCampuses(): HasMany
    {
        return $this->hasMany(HsCampus::class);
    }

    /** @return HasMany<HsDepartment, $this> */
    public function hsDepartments(): HasMany
    {
        return $this->hasMany(HsDepartment::class);
    }

    /** @return HasMany<HsDepartmentCategory, $this> */
    public function hsDepartmentCategories(): HasMany
    {
        return $this->hasMany(HsDepartmentCategory::class);
    }

    /** @return HasMany<HsDoctor, $this> */
    public function hsDoctors(): HasMany
    {
        return $this->hasMany(HsDoctor::class);
    }

    /** @return HasMany<HsPatient, $this> */
    public function hsPatients(): HasMany
    {
        return $this->hasMany(HsPatient::class);
    }

    /** @return HasMany<Visit, $this> */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    /** @return HasMany<MedicalRecord, $this> */
    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    /** @return HasMany<Prescription, $this> */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    /** @return HasMany<Drug, $this> */
    public function drugs(): HasMany
    {
        return $this->hasMany(Drug::class);
    }

    /** @return HasMany<InventoryBatch, $this> */
    public function inventoryBatches(): HasMany
    {
        return $this->hasMany(InventoryBatch::class);
    }

    /** @return HasMany<InventoryStock, $this> */
    public function inventoryStocks(): HasMany
    {
        return $this->hasMany(InventoryStock::class);
    }

    /** @return HasMany<InventoryMovement, $this> */
    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /** @return HasMany<ChargeOrder, $this> */
    public function chargeOrders(): HasMany
    {
        return $this->hasMany(ChargeOrder::class);
    }

    /** @return HasMany<PaymentTransaction, $this> */
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /** @return HasMany<DailySettlement, $this> */
    public function dailySettlements(): HasMany
    {
        return $this->hasMany(DailySettlement::class);
    }
}
