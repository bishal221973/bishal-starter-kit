<?php

namespace BirtaTable;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class TableServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/table.php',
            'birta-table'
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/table.php' => config_path('birta-table.php'),
        ], 'birta-table-config');

        Inertia::share('birtaTable', function () {
            return config('birta-table');
        });
    }
}