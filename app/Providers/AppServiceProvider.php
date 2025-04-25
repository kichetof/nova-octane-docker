<?php

namespace App\Providers;

use App\Checks\TrueCheck;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Nova;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Facades\Health;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Nova::resourcesIn(app_path('Nova'));
        Health::checks([
            TrueCheck::new(),
            // EnvironmentCheck::new(),
            // CacheCheck::new(),
            // OptimizedAppCheck::new(),
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
