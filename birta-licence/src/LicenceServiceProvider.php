<?php

namespace Birta\Licence;

use Birta\Licence\Contracts\LicenceServiceInterface;
use Birta\Licence\Services\LicenceGenerator;
use Birta\Licence\Services\LicenceService;
use Illuminate\Support\ServiceProvider;

class LicenceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Configuration
        |--------------------------------------------------------------------------
        */

        $this->mergeConfigFrom(
            __DIR__ . '/../config/licence.php',
            'licence'
        );

        /*
        |--------------------------------------------------------------------------
        | Licence Service
        |--------------------------------------------------------------------------
        */

        $this->app->singleton(
            LicenceServiceInterface::class,
            function () {
                return new LicenceService(
                    new LicenceGenerator()
                );
            }
        );

        $this->app->alias(
            LicenceServiceInterface::class,
            'licence'
        );
    }

    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Config
        |--------------------------------------------------------------------------
        */

        $this->publishes([
            __DIR__ . '/../config/licence.php'
                => config_path('licence.php'),
        ], 'licence-config');

        /*
        |--------------------------------------------------------------------------
        | Migrations
        |--------------------------------------------------------------------------
        */

        $this->publishes([
            __DIR__ .
                '/../database/migrations/create_licences_table.php'
                => database_path(
                    'migrations/' .
                    date('Y_m_d_His') .
                    '_create_licences_table.php'
                ),
        ], 'licence-migrations');

        /*
        |--------------------------------------------------------------------------
        | Load Migrations
        |--------------------------------------------------------------------------
        */

        $this->loadMigrationsFrom(
            __DIR__ . '/../database/migrations'
        );
    }
}