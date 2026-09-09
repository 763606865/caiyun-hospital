<?php

namespace App\Models\Concerns;

use App\Models\Organization;
use App\Support\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToOrganization
{
    public static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $query): void {
            $organization = app(TenantContext::class)->get();

            if ($organization instanceof Organization) {
                $query->where($query->qualifyColumn('organization_id'), $organization->getKey());
            }
        });

        static::creating(function (Model $model): void {
            if ($model->getAttribute('organization_id')) {
                return;
            }

            $organization = app(TenantContext::class)->get();
            if ($organization instanceof Organization) {
                $model->setAttribute('organization_id', $organization->getKey());
            }
        });
    }

    /** @return BelongsTo<Organization, $this> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
