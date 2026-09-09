<?php

namespace App\Admin\Support;

use App\Support\Tenancy\TenantContext;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

abstract class TenantResource extends Resource
{
    protected static bool $isScopedToTenant = true;

    public static function getEloquentQuery(): Builder
    {
        $tenant = app(TenantContext::class)->get();
        $query = parent::getEloquentQuery();

        return $tenant
            ? $query->where($query->qualifyColumn('organization_id'), $tenant->getKey())
            : $query->whereRaw('1 = 0');
    }
}
