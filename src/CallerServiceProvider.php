<?php

namespace Alisons\Caller;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class CallerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //

        Route::prefix('caller')
            ->as('caller.')
            ->middleware('web')
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../resources/assets/Phone' => public_path('caller/assets'),
            ], 'assets');

            $this->publishes([
                __DIR__ . '/../resources/css' => public_path('caller/css'),
            ], 'assets');

            $this->publishes([
                __DIR__ . '/../resources/js' => public_path('caller/js'),
            ], 'assets');
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'caller');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
