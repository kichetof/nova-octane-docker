<?php

namespace App\Providers;

use App\Models\User;
use App\Nova\Dashboards\Main;
use App\Services\FrankenPhpMetrics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Features;
use Laravel\Nova\Dashboard;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Menu\MenuGroup;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\Tool;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        Nova::withBreadcrumbs();

        Nova::mainMenu(function (Request $request): array {
            return [
                MenuSection::make('Admin', [
                    MenuGroup::make('User Base', [
                        MenuItem::resource(\App\Nova\User::class), // ->icon(svg('phosphor-users')->toHtml()),
                    ]),
                ])->icon('cog-8-tooth'),
            ];
        });

        Nova::serving(static function (ServingNova $event) {
            if (($lang = $event->app->getLocale()) !== 'fr' || Nova::$translations['Create :resource'] !== 'Créer :resource') {
                Log::debug("locale switch from french => $lang", ['translations' => Nova::$translations]);
            }
        });

        Nova::footer(function () {
            return Blade::render('
            <p class="text-center">Powered by <a class="link-default" href="https://nova.laravel.com">Laravel Nova</a> · v{!! $version !!}</p>
            <p class="text-center">&copy; {!! $year !!} Laravel Holdings Inc.</p>
            <p class="text-center">php {!! $php !!} | {!! $frankenphp !!}</p>
            <p class="text-center">Total Requests {{ $requestsTotal }} | Workers Restarts {{ $workersRestarts}}</p>
        ', [
                'version' => Nova::version(),
                'year' => date('Y'),
                'php' => PHP_VERSION,
                'frankenphp' => ($v = phpversion('frankenphp')) ? 'frankenphp '.$v : 'Please start FrankenPHP',
                'workersRestarts' => app(FrankenPhpMetrics::class)->getWorkersRestarts(),
                'requestsTotal' => app(FrankenPhpMetrics::class)->getRequestsTotal(),
            ]);
        });
    }

    /**
     * Register the configurations for Laravel Fortify.
     */
    protected function fortify(): void
    {
        Nova::fortify()
            ->features([
                Features::updatePasswords(),
                // Features::emailVerification(),
                // Features::twoFactorAuthentication(['confirm' => true, 'confirmPassword' => true]),
            ])
            ->register();
    }

    /**
     * Register the Nova routes.
     */
    protected function routes(): void
    {
        Nova::routes()
            ->withAuthenticationRoutes(default: true)
            ->withPasswordResetRoutes()
            ->withoutEmailVerificationRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewNova', function (User $user) {
            return in_array($user->email, [
                //
            ]);
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array<int, Dashboard>
     */
    protected function dashboards(): array
    {
        return [
            new Main,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array<int, Tool>
     */
    public function tools(): array
    {
        return [];
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        parent::register();

        //
    }
}
