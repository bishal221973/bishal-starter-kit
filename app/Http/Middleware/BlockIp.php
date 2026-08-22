<?php

namespace App\Http\Middleware;

use App\Facades\AppConfig;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockIp
{
    // protected array $blockedIps = AppConfig::ipSecurity()->blocked_ips;

    public function handle(Request $request, Closure $next): Response
    {
        $config = AppConfig::ipSecurity();
        $blockedIps=$config['blocked_ips'];
        if (in_array($request->ip(), $blockedIps)) {
            abort(403, 'Your IP address has been blocked.');
        }

        return $next($request);
    }
}
