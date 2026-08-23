IP Security

The Bishal Starter Kit provides IP security configuration through the configurations table.

IP security is designed to control access based on client IP addresses and optionally log blocked access attempts.

Configuration

The following fields are available:

enable_ip_blacklist
blacklisted_ips
log_blocked_ip_attempts

They are stored in the configurations table.

$table->boolean('enable_ip_blacklist')->default(false);
$table->json('blacklisted_ips')->nullable();
$table->boolean('log_blocked_ip_attempts')->default(true);
Enable IP Blacklist

IP blacklist functionality is disabled by default.

enable_ip_blacklist = false

To enable it:

$configuration->update([
    'enable_ip_blacklist' => true,
]);

Or:

$configuration->enable_ip_blacklist = true;
$configuration->save();

When enabled, the application can check the incoming request IP against the configured blacklist.

Blacklisted IP Addresses

Blocked IP addresses are stored as JSON in:

blacklisted_ips

Example:

[
    "192.168.1.100",
    "10.0.0.50",
    "203.0.113.25"
]

The configuration can be updated with:

$configuration->update([
    'blacklisted_ips' => [
        '192.168.1.100',
        '10.0.0.50',
        '203.0.113.25',
    ],
]);

Because the database column is JSON, Laravel can automatically encode the array when the model casts the field to an array.

Model Cast

The Configuration model should cast the JSON field:

protected function casts(): array
{
    return [
        'enable_ip_blacklist' => 'boolean',
        'blacklisted_ips' => 'array',
        'log_blocked_ip_attempts' => 'boolean',
    ];
}

Then the blacklist can be accessed directly:

$configuration->blacklisted_ips;

Example result:

[
    '192.168.1.100',
    '10.0.0.50',
]
Getting the Client IP

Laravel provides the current request IP through:

$request->ip();

Example:

$ip = request()->ip();
Checking a Blacklisted IP

A basic check can be implemented as:

$ip = request()->ip();

if (
    $configuration->enable_ip_blacklist &&
    in_array($ip, $configuration->blacklisted_ips ?? [], true)
) {
    abort(403, 'Your IP address has been blocked.');
}
Middleware

IP security is best implemented using Laravel middleware so that the check can be applied consistently.

Example:

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIpBlacklist
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $configuration = app('configuration');

        if (! $configuration?->enable_ip_blacklist) {
            return $next($request);
        }

        $ip = $request->ip();

        $blacklistedIps = $configuration->blacklisted_ips ?? [];

        if (in_array($ip, $blacklistedIps, true)) {
            abort(403, 'Your IP address has been blocked.');
        }

        return $next($request);
    }
}

The exact method used to retrieve the current configuration depends on how configuration is loaded in the application.

Logging Blocked Attempts

The configuration contains:

log_blocked_ip_attempts

It defaults to:

true

If enabled, the application can log information whenever a blacklisted IP attempts to access the application.

Example:

if (
    $configuration->log_blocked_ip_attempts
) {
    Log::warning('Blocked IP attempted access.', [
        'ip' => $ip,
        'url' => $request->fullUrl(),
        'user_agent' => $request->userAgent(),
    ]);
}

Then deny the request:

abort(403, 'Your IP address has been blocked.');
Middleware Example with Logging

A more complete implementation:

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckIpBlacklist
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $configuration = app('configuration');

        if (! $configuration?->enable_ip_blacklist) {
            return $next($request);
        }

        $ip = $request->ip();

        $blacklistedIps = $configuration->blacklisted_ips ?? [];

        if (in_array($ip, $blacklistedIps, true)) {

            if ($configuration->log_blocked_ip_attempts) {
                Log::warning(
                    'Blocked IP attempted to access the application.',
                    [
                        'ip' => $ip,
                        'url' => $request->fullUrl(),
                        'method' => $request->method(),
                        'user_agent' => $request->userAgent(),
                    ]
                );
            }

            abort(403, 'Your IP address has been blocked.');
        }

        return $next($request);
    }
}
Adding an IP to the Blacklist

You can append an IP address to the existing list:

$ips = $configuration->blacklisted_ips ?? [];

$ips[] = $request->ip();

$configuration->update([
    'blacklisted_ips' => array_values(
        array_unique($ips)
    ),
]);

Using array_unique() prevents duplicate entries.

Removing an IP

To remove an IP:

$ips = $configuration->blacklisted_ips ?? [];

$ips = array_values(
    array_filter(
        $ips,
        fn ($ip) => $ip !== '192.168.1.100'
    )
);

$configuration->update([
    'blacklisted_ips' => $ips,
]);
Checking Before Authentication

If IP blocking should prevent even unauthenticated users from accessing the application, place the middleware before protected application routes.

Conceptually:

Request
   ↓
IP Security
   ↓
Authentication
   ↓
Authorization
   ↓
Controller

This allows blacklisted IPs to be rejected before reaching application functionality.

Configuration Example

A configuration record might contain:

{
    "enable_ip_blacklist": true,
    "blacklisted_ips": [
        "192.168.1.100",
        "203.0.113.25"
    ],
    "log_blocked_ip_attempts": true
}

This means:

IP blacklist is enabled.
192.168.1.100 is blocked.
203.0.113.25 is blocked.
Blocked attempts should be logged.
Important Proxy Consideration

When the application runs behind a reverse proxy, load balancer, Cloudflare, or another proxy, the IP returned by:

$request->ip();

depends on Laravel's trusted-proxy configuration.

Make sure trusted proxies are configured correctly before relying on IP-based security.

Otherwise, the application may see the proxy's IP instead of the actual client IP.

Security Recommendations

IP blacklist should be treated as an additional security layer rather than the only security mechanism.

The application should still use:

Authentication
Authorization
Rate limiting
Login attempt protection
HTTPS
Strong passwords
Session security

The starter kit also provides login security configuration through:

enable_login_attempt_limit
max_login_attempts
login_lockout_duration

These settings are separate from IP security.

Configuration Fields
Field	Type	Default	Purpose
enable_ip_blacklist	boolean	false	Enables IP blacklist checking
blacklisted_ips	JSON	null	Stores blocked IP addresses
log_blocked_ip_attempts	boolean	true	Logs blocked access attempts
Summary

The IP security feature provides a configurable IP blacklist:

Configuration
     ↓
enable_ip_blacklist
     ↓
blacklisted_ips
     ↓
Incoming Request IP
     ↓
Match?
   ↙     ↘
 Yes      No
 ↓         ↓
Block     Continue
 ↓
Optional Log

IP security configuration is stored in the configurations table and can be managed from the application's configuration system.