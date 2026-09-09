<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['organization_id', 'name', 'code', 'phone', 'address', 'latitude', 'longitude', 'status', 'is_default', 'business_hours'])]
class Branch extends Model
{
    use BelongsToOrganization, SoftDeletes;

    protected function casts(): array
    {
        return ['is_default' => 'boolean', 'business_hours' => 'array'];
    }

    /** @return BelongsToMany<OrganizationMember, $this> */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(OrganizationMember::class, 'branch_members')->withPivot('is_primary')->withTimestamps();
    }

    /** @return HasMany<Visit, $this> */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }
}
