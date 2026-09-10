<?php

namespace App\Admin;

use App\Admin\Resources\AdminRoles\AdminRoleResource;
use App\Admin\Resources\AdminUsers\AdminUserResource;
use App\Admin\Resources\Categories\CategoryResource;
use App\Admin\Resources\ConsultationRequests\ConsultationRequestResource;
use App\Admin\Resources\Contents\ContentResource;
use App\Admin\Resources\Media\MediaResource;
use App\Admin\Resources\OperationLogs\OperationLogResource;
use App\Admin\Resources\Organizations\OrganizationResource;
use App\Admin\Resources\SystemSettings\SystemSettingResource;
use App\Admin\Resources\Tags\TagResource;
use App\Admin\Resources\Users\UserResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile()
            ->authGuard('admin')
            ->authPasswordBroker('admin_users')
            ->brandName(config('app.name').' SaaS 运营管理端')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->resources([
                OrganizationResource::class,
                ConsultationRequestResource::class,
                AdminUserResource::class,
                AdminRoleResource::class,
                UserResource::class,
                ContentResource::class,
                CategoryResource::class,
                TagResource::class,
                MediaResource::class,
                SystemSettingResource::class,
                OperationLogResource::class,
            ])
            ->discoverPages(in: app_path('Admin/Pages'), for: 'App\\Admin\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Admin/Widgets'), for: 'App\\Admin\\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
