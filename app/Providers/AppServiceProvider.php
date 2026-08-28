<?php

namespace App\Providers;

use App\Models\Configuration;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // $timezone = Configuration::value('timezone');

        // if (
        //     $timezone &&
        //     in_array($timezone, timezone_identifiers_list(), true)
        // ) {
        //     Config::set('app.timezone', $timezone);

        //     date_default_timezone_set($timezone);
        // }
    }
}
