<?php

use App\Http\Middleware\BlockIp;
use App\Http\Middleware\CheckPasswordExpiration;
use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\EnsureUserHasOrganization;
use App\Http\Middleware\SetApplicationConfig;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Birta\Licence\Middleware\CheckLicence;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);
        $middleware->append([
            BlockIp::class,
            SetApplicationConfig::class
        ]);

        $middleware->alias([
            'password.expired' => CheckPasswordExpiration::class,
            'conditional.verified' => EnsureEmailIsVerified::class,
            'has.organization' => EnsureUserHasOrganization::class,
            'licence' => CheckLicence::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
