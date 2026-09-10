<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use App\Support\Tenancy\TenantContext;
use Closure;
use Filament\Facades\Filament;
use Filament\Models\Contracts\HasTenants;
use Illuminate\Http\Request;

class SetTenantContext
{
    public function handle(Request $request, Closure $next): mixed
    {
        $tenant = Filament::getTenant();

        if (! $tenant instanceof Organization && $request->route()?->hasParameter('tenant')) {
            $tenantKey = $request->route()->parameter('tenant');
            abort_unless(is_string($tenantKey), 404);

            $tenant = Filament::getCurrentOrDefaultPanel()->getTenant(
                $tenantKey,
            );

            $user = Filament::auth()->user();
            abort_unless($user instanceof HasTenants && $user->canAccessTenant($tenant), 404);

            Filament::setTenant($tenant);
        }

        abort_unless($tenant instanceof Organization, 404);

        $context = app(TenantContext::class);
        $context->set($tenant);

        try {
            return $next($request);
        } finally {
            $context->clear();
        }
    }
}
