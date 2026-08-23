Security

The Bishal Starter Kit includes several configurable security features for protecting user accounts, authentication, sessions, access control, and application data.

The security system works together with:

Laravel
Laravel Jetstream
Spatie Laravel Permission
Application configurations
Session management
Password policy
IP security
Login attempt protection
Auto logout
Organization-level access
Security Features

The starter kit provides configuration for:

Authentication
Password Policy
Login Attempt Limits
Account Lockout
Session Security
Auto Logout
IP Blacklist
Password Expiration
Role & Permission Authorization
Two-Factor Authentication
Email Verification
Single Device Login
Force Logout on Password Change
Session Invalidation
Authentication

The starter kit uses Laravel Jetstream for authentication.

Authentication provides the basic user login and account functionality.

Typical authentication flow:

User
 ↓
Login
 ↓
Authentication
 ↓
Security Checks
 ↓
Authorization
 ↓
Dashboard

Protected application routes should use:

Route::middleware('auth')->group(function () {
    // Protected routes
});
Email Verification

Email verification can be enabled through configuration:

enable_email_verification

The default value is:

$table->boolean('enable_email_verification')
    ->default(false);

Enable it:

$configuration->update([
    'enable_email_verification' => true,
]);

When enabled, users can be required to verify their email address before accessing protected functionality.

Two-Factor Authentication

The configuration includes:

enable_2fa

Default:

$table->boolean('enable_2fa')
    ->default(false);

Enable:

$configuration->update([
    'enable_2fa' => true,
]);

The starter kit uses Jetstream's authentication ecosystem, while the exact 2FA flow depends on how two-factor authentication is enabled and configured in the application.

Password Policy

Password security is controlled through the password policy configuration.

Available options include:

enable_password_policy
minimum_password_length
require_uppercase
require_lowercase
require_number
require_special_character
password_expiry_days

Example:

$configuration->update([
    'enable_password_policy' => true,
    'minimum_password_length' => 12,
    'require_uppercase' => true,
    'require_lowercase' => true,
    'require_number' => true,
    'require_special_character' => true,
    'password_expiry_days' => 90,
]);

See passwordpolicy.md for the complete password policy documentation.

Password Expiration

Users contain:

password_created_at
password_expired_at

When a password is changed, the expiration date can be recalculated:

$user->update([
    'password_created_at' => now(),
    'password_expired_at' => now()->addDays(
        (int) $configuration->password_expiry_days
    ),
]);

Before allowing normal application access, the application can check:

if (
    $user->password_expired_at &&
    now()->greaterThanOrEqualTo(
        $user->password_expired_at
    )
) {
    // Require password change
}
Login Attempt Limit

The starter kit supports login attempt protection.

Configuration:

enable_login_attempt_limit
max_login_attempts
login_lockout_duration

Defaults:

enable_login_attempt_limit = true
max_login_attempts = 5
login_lockout_duration = 15 minutes

Database definition:

$table->boolean('enable_login_attempt_limit')
    ->default(true);

$table->unsignedTinyInteger('max_login_attempts')
    ->default(5);

$table->unsignedInteger('login_lockout_duration')
    ->default(15);
Account Lockout

The users table contains:

login_attempts
locked_until

Database fields:

$table->unsignedInteger('login_attempts')
    ->default(0);

$table->timestamp('locked_until')
    ->nullable();

The application can use these fields to lock an account after repeated failed login attempts.

Example:

if (
    $user->locked_until &&
    now()->lessThan($user->locked_until)
) {
    // Account is locked
}
Login Attempt Flow

A typical login security flow is:

Login Attempt
     ↓
Check Account Lock
     ↓
Locked?
 ↙          ↘
Yes          No
 ↓            ↓
Reject       Authenticate
              ↓
          Successful?
          ↙       ↘
        Yes        No
         ↓          ↓
Reset       Increment Attempts
Attempts          ↓
              Limit Reached?
                ↓
              Lock Account
Reset Login Attempts

After successful authentication, failed attempts should be reset.

Example:

$user->update([
    'login_attempts' => 0,
    'locked_until' => null,
]);
Session Security

The starter kit stores user sessions in Laravel's sessions table.

The table contains:

id
user_id
ip_address
user_agent
payload
last_activity

This allows the application to track authenticated sessions.

Session Timeout

The configuration contains:

session_timeout

The default value is:

2 days

The application can use this value when configuring or managing session lifetime.

Force Logout on Password Change

The configuration includes:

force_logout_on_password_change

Default:

$table->boolean('force_logout_on_password_change')
    ->default(true);

When enabled, changing a user's password can invalidate the current authentication sessions.

Example:

$configuration->update([
    'force_logout_on_password_change' => true,
]);

This is useful if an administrator suspects that a user's password has been compromised.

Invalidate Other Sessions

The configuration also contains:

invalidate_other_sessions

Default:

$table->boolean('invalidate_other_sessions')
    ->default(true);

When enabled, password changes can invalidate sessions on other devices.

Conceptually:

Device A ──┐
Device B ──┼── User
Device C ──┘
              ↓
       Password Changed
              ↓
     Invalidate Sessions
              ↓
       Other devices
          logged out
Single Device Login

The configuration includes:

force_single_device_login

Default:

$table->boolean('force_single_device_login')
    ->default(false);

When enabled, the application can restrict a user to one active device/session.

Example:

$configuration->update([
    'force_single_device_login' => true,
]);

The actual session invalidation behavior must be implemented by the application.

Auto Logout

Automatic logout is configurable through:

enable_auto_logout
auto_logout_time
show_logout_warning
logout_warning_time

Defaults:

enable_auto_logout = false
auto_logout_time = 30 minutes
show_logout_warning = true
logout_warning_time = 1 minute

Example:

$configuration->update([
    'enable_auto_logout' => true,
    'auto_logout_time' => 30,
    'show_logout_warning' => true,
    'logout_warning_time' => 1,
]);

See autologout.md for the complete auto logout documentation.

IP Security

The starter kit provides IP blacklist functionality.

Configuration:

enable_ip_blacklist
blacklisted_ips
log_blocked_ip_attempts

Defaults:

enable_ip_blacklist = false
log_blocked_ip_attempts = true
Enable IP Blacklist
$configuration->update([
    'enable_ip_blacklist' => true,
]);
Blacklisted IP Addresses

The IP list is stored as JSON:

$table->json('blacklisted_ips')
    ->nullable();

Example:

$configuration->update([
    'blacklisted_ips' => [
        '192.168.1.100',
        '10.0.0.50',
    ],
]);

The Configuration model should cast it as an array:

protected function casts(): array
{
    return [
        'blacklisted_ips' => 'array',
    ];
}
Checking an IP

The application can check the current request IP:

$ip = request()->ip();

if (
    $configuration->enable_ip_blacklist &&
    in_array(
        $ip,
        $configuration->blacklisted_ips ?? []
    )
) {
    abort(403, 'Access denied.');
}

For a reusable implementation, this check can be placed in middleware.

IP Security Middleware

A middleware can perform the IP check before the request reaches the application.

Conceptually:

Request
   ↓
IP Security Middleware
   ↓
IP Blacklisted?
  ↙       ↘
Yes       No
 ↓         ↓
403       Continue

Example:

public function handle($request, Closure $next)
{
    $configuration = Configuration::first();

    $ip = $request->ip();

    if (
        $configuration?->enable_ip_blacklist &&
        in_array(
            $ip,
            $configuration->blacklisted_ips ?? []
        )
    ) {
        abort(403, 'Your IP address is blocked.');
    }

    return $next($request);
}
Blocked IP Logging

The configuration contains:

log_blocked_ip_attempts

Default:

$table->boolean('log_blocked_ip_attempts')
    ->default(true);

If enabled, blocked access attempts can be logged for security monitoring.

Example:

Log::warning('Blocked IP access attempt', [
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'url' => $request->fullUrl(),
]);
Roles and Permissions

The starter kit uses Spatie Laravel Permission for authorization.

Authorization should be applied to protected routes:

Route::middleware([
    'auth',
    'permission:employees.view',
])->get('/employees', [
    EmployeeController::class,
    'index',
]);

The security model is:

Authentication
      ↓
User
      ↓
Role
      ↓
Permission
      ↓
Authorized Action

See rolepermission.md for detailed role and permission documentation.

Backend Authorization

Frontend checks are not sufficient for security.

For example, Vue may hide a button:

<button v-if="can('users.delete')">
    Delete
</button>

But the backend must still protect the endpoint:

Route::middleware([
    'auth',
    'permission:users.delete',
])->delete(
    '/users/{user}',
    [UserController::class, 'destroy']
);

This prevents users from bypassing frontend restrictions.

Organization Security

The starter kit supports organization-based access.

Organizations contain:

is_active
trial_ends_at
subscription_status
subscription_ends_at

Before performing organization-specific operations, the application can verify that the organization is active.

if (! $organization->is_active) {
    abort(403, 'Organization is inactive.');
}

Subscription checks can similarly be applied where required.

Security Configuration

Security-related configuration is stored in the configurations table.

Important fields include:

enable_registration
enable_email_verification
enable_2fa

force_logout_on_password_change
invalidate_other_sessions

enable_auto_logout
auto_logout_time
show_logout_warning
logout_warning_time

enable_ip_blacklist
blacklisted_ips
log_blocked_ip_attempts

enable_login_attempt_limit
max_login_attempts
login_lockout_duration

enable_password_policy
minimum_password_length
require_uppercase
require_lowercase
require_number
require_special_character
password_expiry_days

enable_delete_account
force_single_device_login
Recommended Production Configuration

A production application can use settings similar to:

Registration
-------------------------
enable_registration = false
enable_email_verification = true
enable_2fa = true

Login Security
-------------------------
enable_login_attempt_limit = true
max_login_attempts = 5
login_lockout_duration = 15

Password Policy
-------------------------
enable_password_policy = true
minimum_password_length = 12
require_uppercase = true
require_lowercase = true
require_number = true
require_special_character = true
password_expiry_days = 90

Session Security
-------------------------
force_logout_on_password_change = true
invalidate_other_sessions = true
force_single_device_login = false

Auto Logout
-------------------------
enable_auto_logout = true
auto_logout_time = 30
show_logout_warning = true

IP Security
-------------------------
enable_ip_blacklist = false
log_blocked_ip_attempts = true

These are recommended starting points; the appropriate values depend on the application's requirements.

User Account Security Fields

The users table contains security-related fields:

password
password_created_at
password_expired_at
login_attempts
locked_until
deleted_at

Example migration:

$table->string('password');

$table->date('password_created_at')
    ->default(now());

$table->date('password_expired_at')
    ->nullable();

$table->unsignedInteger('login_attempts')
    ->default(0);

$table->timestamp('locked_until')
    ->nullable();

$table->softDeletes();
Security Request Flow

A protected request can follow this structure:

Browser
   ↓
Laravel Request
   ↓
IP Security
   ↓
Authentication
   ↓
Session Security
   ↓
Account Lock Check
   ↓
Role / Permission
   ↓
Controller
   ↓
Business Logic
   ↓
Response
Security Best Practices
Always validate input

Use Laravel validation:

$request->validate([
    'email' => ['required', 'email'],
    'password' => ['required', 'string'],
]);
Use authorization middleware
Route::middleware('permission:users.update')
    ->put('/users/{user}', ...);
Never trust frontend authorization

Vue visibility checks should not replace backend authorization.

Hash passwords

Use Laravel's hashing functionality:

Hash::make($password);

Never store plain-text passwords.

Protect sensitive routes

Use:

Route::middleware('auth')->group(function () {
    // Protected routes
});
Use HTTPS in production

Authentication credentials, session cookies, and application data should be transmitted over HTTPS.

Keep dependencies updated

Regularly update Laravel, Jetstream, Spatie Permission, and other application dependencies according to compatibility requirements.

Security Architecture

The Bishal Starter Kit security architecture can be summarized as:

                    User
                      │
                      ↓
              ┌───────────────┐
              │ Authentication │
              │   Jetstream    │
              └───────┬───────┘
                      ↓
              ┌───────────────┐
              │    Session    │
              │    Security   │
              └───────┬───────┘
                      ↓
              ┌───────────────┐
              │ IP Security   │
              └───────┬───────┘
                      ↓
              ┌───────────────┐
              │ Role/Permission│
              │     Spatie    │
              └───────┬───────┘
                      ↓
              ┌───────────────┐
              │   Controller  │
              └───────┬───────┘
                      ↓
              Application Logic
Summary

The Bishal Starter Kit provides a configurable security layer covering:

Laravel Jetstream authentication
Email verification
Two-factor authentication configuration
Password policies
Password expiration
Login attempt limits
Account lockout
Session management
Automatic logout
Single-device login configuration
IP blacklist
Blocked IP logging
Spatie roles and permissions
Organization access control
Password-change session invalidation

Security should be enforced primarily on the Laravel backend. Frontend checks in Vue/Inertia should only complement backend authorization, not replace it.