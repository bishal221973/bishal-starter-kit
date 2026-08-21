<?php

namespace App\Http\Middleware;

use App\Facades\AppConfig;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $config = AppConfig::registration();
        // dd($config);

        /*
        |--------------------------------------------------------------------------
        | Registration / Email Verification Disabled
        |--------------------------------------------------------------------------
        */

        if (
            !$config ||
            !($config['email_verification'] ?? false)
        ) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | User Not Authenticated
        |--------------------------------------------------------------------------
        */

        if (!$request->user()) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | User Already Verified
        |--------------------------------------------------------------------------
        */

        if ($request->user()->hasVerifiedEmail()) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Allow Verification Routes
        |--------------------------------------------------------------------------
        */

        if (
            $request->routeIs('verification.notice') ||
            $request->routeIs('verification.verify') ||
            $request->routeIs('verification.send') ||
            $request->routeIs('logout')
        ) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Redirect Unverified User
        |--------------------------------------------------------------------------
        */

        return redirect()->route('verification.notice');
    }
}
