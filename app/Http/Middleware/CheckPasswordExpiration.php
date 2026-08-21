<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPasswordExpiration
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (
            $user &&
            $user->passwordExpired() &&
            !$request->routeIs('password.change')
        ) {
            return redirect()->route('password.change');
        }

        return $next($request);
    }
}
