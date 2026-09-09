<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Content;
use App\Models\Media;
use App\Models\SystemSetting;
use App\Models\Tag;
use App\Observers\OperationLogObserver;
use App\Support\Tenancy\TenantContext;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(TenantContext::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        foreach ([Content::class, Category::class, Tag::class, Media::class, SystemSetting::class] as $model) {
            $model::observe(OperationLogObserver::class);
        }
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
        if (app()->isProduction()) {
            URL::useOrigin(config('app.url'));
            if (config('app.https_enabled')) {
                URL::forceScheme('https');
            }
        }
    }
}
