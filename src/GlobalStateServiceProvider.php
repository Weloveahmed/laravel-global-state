<?php

namespace AhmedZaky\GlobalState;

use Illuminate\Support\ServiceProvider;
use AhmedZaky\GlobalState\Contracts\GlobalStateInterface;

class GlobalStateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/global-state.php',
            'global-state'
        );

        $this->app->singleton(GlobalStateInterface::class, function ($app) {
            return new GlobalState();
        });

        $this->app->alias(GlobalStateInterface::class, 'global-state');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/global-state.php' => config_path('global-state.php'),
            ], 'global-state-config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'global-state-migrations');
        }
    }
}
