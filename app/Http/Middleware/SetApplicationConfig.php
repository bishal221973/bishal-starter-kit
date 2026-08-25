<?php

namespace App\Http\Middleware;

use App\Models\Configuration;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class SetApplicationConfig
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $config = Configuration::first();

        if ($config) {

            /*
            |--------------------------------------------------------------------------
            | Session Lifetime
            |--------------------------------------------------------------------------
            |
            | Value is stored in minutes.
            |
            */

            if ($config->session_timeout !== null) {
                Config::set(
                    'session.lifetime',
                    max((int) $config->session_timeout, 1)
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Maintenance Mode
            |--------------------------------------------------------------------------
            |
            | Maintenance mode itself should normally be handled through
            | Laravel's maintenance middleware / maintenance command.
            |
            */

        }

        return $next($request);
    }
}