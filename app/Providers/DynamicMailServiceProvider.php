<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DynamicMailService;

class DynamicMailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('mailFacade', function ($app) {
            return new DynamicMailService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
