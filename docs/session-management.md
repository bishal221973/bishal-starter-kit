Session Management

The Bishal Starter Kit uses Laravel's session system to manage authenticated user sessions.

Session management works together with:

Laravel
Laravel Jetstream
Authentication
Auto Logout
Password security
Login security
Single-device login
Session invalidation
Session Storage

The starter kit uses Laravel's database session driver.

The sessions table contains:

Schema::create('sessions', function (Blueprint $table) {
    $table->string('id')->primary();

    $table->foreignId('user_id')
        ->nullable()
        ->index();

    $table->string('ip_address', 45)
        ->nullable();

    $table->text('user_agent')
        ->nullable();

    $table->longText('payload');

    $table->integer('last_activity')
        ->index();
});

Each session can therefore be associated with a user and contains information about the client's IP address, browser, and last activity.

Session Fields
Field	Description
id	Unique session identifier
user_id	Authenticated user's ID
ip_address	Client IP address
user_agent	Browser/client information
payload	Serialized session data
last_activity	Last session activity timestamp
Session Configuration

Laravel's session configuration is controlled through the application's session configuration.

Important settings include:

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

The exact values depend on the application's .env configuration.

For production, use a secure session configuration appropriate for your deployment environment.

Session Lifetime

The starter kit also has an application-level configuration:

session_timeout

The default value in the configurations table is:

$table->string('session_timeout')->default(2);

The value represents the application's configured session timeout in days.

For example:

$configuration->update([
    'session_timeout' => 2,
]);

This configuration can be used by the application when determining how long authenticated sessions should remain valid.

Laravel Session Lifetime vs Application Session Timeout

There are two separate concepts:

Laravel session lifetime

Laravel's session configuration controls the normal lifetime of the session.

For example:

SESSION_LIFETIME=120

This value is generally expressed in minutes.

Application session timeout

The starter kit also stores:

session_timeout

in the configurations table.

This allows the starter kit to have application-specific session security rules.

Do not assume these two values automatically control each other. If the application configuration should dynamically change Laravel's session lifetime, that behavior must be implemented explicitly.

Accessing the Current Session

Laravel provides access to the current session through the session() helper.

Example:

$value = session('key');

Store a value:

session([
    'key' => 'value',
]);

Or:

session()->put('key', 'value');

Retrieve it:

$value = session()->get('key');
Removing Session Data

Remove one session value:

session()->forget('key');

Remove multiple values:

session()->forget([
    'key',
    'another_key',
]);
Regenerate Session ID

After authentication or other sensitive operations, regenerate the session ID.

Laravel supports:

$request->session()->regenerate();

This helps protect against session fixation.

Destroy Current Session

To completely invalidate the current session:

$request->session()->invalidate();

It is common to regenerate the CSRF token afterward:

$request->session()->invalidate();

$request->session()->regenerateToken();
Logout

Laravel authentication should invalidate the authenticated session during logout.

A typical logout flow is:

User
 ↓
Logout
 ↓
Authentication Logout
 ↓
Session Invalidate
 ↓
CSRF Token Regenerate
 ↓
Login Page

Example:

Auth::logout();

$request->session()->invalidate();

$request->session()->regenerateToken();
Current User

The currently authenticated user can be retrieved with:

$user = auth()->user();

Or:

$user = $request->user();

Check whether the user is authenticated:

if (auth()->check()) {
    // User is authenticated
}
Session User ID

The current session can contain the authenticated user's ID.

Laravel provides:

$userId = auth()->id();

The corresponding database session can contain this ID in:

sessions.user_id
Active Sessions

Because the application uses database sessions, active sessions can be inspected from the sessions table.

Example:

$sessions = DB::table('sessions')
    ->where('user_id', $user->id)
    ->get();

You can use this to build an "Active Sessions" page.

Display User Sessions

A controller can retrieve the user's active sessions:

use Illuminate\Support\Facades\DB;

public function sessions()
{
    $sessions = DB::table('sessions')
        ->where('user_id', auth()->id())
        ->orderByDesc('last_activity')
        ->get();

    return inertia('Profile/Sessions', [
        'sessions' => $sessions,
    ]);
}
Session IP Address

The session table stores the IP address:

ip_address

Example:

$session->ip_address;

This can be displayed to the user:

IP Address: 192.168.1.10

The field supports IPv4 and IPv6 because it is defined as:

$table->string('ip_address', 45)->nullable();
User Agent

The session stores browser/client information:

user_agent

Example:

$session->user_agent;

This can be used to show information such as:

Chrome
Firefox
Safari
Mobile Browser

The exact parsing of the user agent depends on the application.

Last Activity

The last_activity field stores the last activity timestamp as a Unix timestamp.

Example:

$session->last_activity;

It can be converted into a date/time using Carbon:

Carbon\Carbon::createFromTimestamp(
    $session->last_activity
);
Detecting Current Session

Laravel provides the current session ID:

$currentSessionId = session()->getId();

You can compare it against sessions stored in the database.

Example:

$currentSessionId = session()->getId();

$sessions = DB::table('sessions')
    ->where('user_id', auth()->id())
    ->get();

foreach ($sessions as $session) {
    $session->is_current =
        $session->id === $currentSessionId;
}

This is useful for an active sessions management page.

Logout Other Sessions

The starter kit configuration contains:

invalidate_other_sessions

Default:

$table->boolean('invalidate_other_sessions')
    ->default(true);

When enabled, password changes or security actions can invalidate other sessions belonging to the user.

Conceptually:

User
 ├── Device A ← Current
 ├── Device B
 └── Device C

Password Changed
       ↓
Invalidate Other Sessions

User
 ├── Device A ← Current
 ├── Device B ← Logged out
 └── Device C ← Logged out

The exact implementation should be handled by the application's authentication/session logic.

Force Logout on Password Change

The configuration includes:

force_logout_on_password_change

Default:

$table->boolean('force_logout_on_password_change')
    ->default(true);

Enable:

$configuration->update([
    'force_logout_on_password_change' => true,
]);

This setting allows the application to enforce session invalidation after a password change.

Single Device Login

The starter kit supports:

force_single_device_login

Default:

$table->boolean('force_single_device_login')
    ->default(false);

Enable:

$configuration->update([
    'force_single_device_login' => true,
]);

When enabled, the application can invalidate previous sessions whenever a new session is created.

Example concept:

Device A
   ↓
Login
   ↓
Active

Device B
   ↓
Login
   ↓
Invalidate Device A
   ↓
Device B becomes active
Invalidating a Specific Session

Because sessions are stored in the database, a specific session can be deleted.

Example:

DB::table('sessions')
    ->where('id', $sessionId)
    ->where('user_id', auth()->id())
    ->delete();

Always ensure that the session belongs to the currently authenticated user before deleting it.

Logout All Other Sessions

To remove all sessions except the current session:

$currentSessionId = session()->getId();

DB::table('sessions')
    ->where('user_id', auth()->id())
    ->where('id', '!=', $currentSessionId)
    ->delete();

This is useful for a Logout Other Devices feature.

Logout All Sessions

To invalidate all database sessions belonging to a user:

DB::table('sessions')
    ->where('user_id', auth()->id())
    ->delete();

Be careful with this operation because it also removes the current session.

The user will need to authenticate again.

Auto Logout

The starter kit supports automatic logout through:

enable_auto_logout
auto_logout_time
show_logout_warning
logout_warning_time

Example:

$configuration->update([
    'enable_auto_logout' => true,
    'auto_logout_time' => 30,
    'show_logout_warning' => true,
    'logout_warning_time' => 1,
]);

See autologout.md for the complete auto logout implementation.

Session Security and Password Changes

A secure password change flow should look like:

Password Change
      ↓
Validate Current Password
      ↓
Validate New Password
      ↓
Update Password
      ↓
Update Password Dates
      ↓
Invalidate Other Sessions
      ↓
Optional Current Session Logout

Example password metadata:

$user->update([
    'password' => Hash::make($password),
    'password_created_at' => now(),
    'password_expired_at' => now()->addDays(
        (int) $configuration->password_expiry_days
    ),
]);
Session Security Middleware

Sensitive routes should be protected with authentication middleware:

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class);
});

Additional authorization can be applied:

Route::middleware([
    'auth',
    'permission:users.view',
])->group(function () {
    // Protected routes
});
Session Management Page

A session management page can display:

Device
IP Address
Browser
Last Activity
Current Session
Logout

Example response structure:

[
    [
        'id' => '...',
        'ip_address' => '192.168.1.10',
        'user_agent' => 'Mozilla/5.0...',
        'last_activity' => 1787500000,
        'is_current' => true,
    ],
]

The Vue/Inertia frontend can then display these sessions.

Recommended Session Security

For production:

Use HTTPS
Use secure cookies
Use HttpOnly cookies
Regenerate session after authentication
Invalidate session during logout
Protect authenticated routes
Invalidate sessions after sensitive security events
Use reasonable session lifetime
Avoid storing sensitive information directly in session data
Session Flow

The overall session lifecycle is:

                    Login
                      ↓
              Session Created
                      ↓
             Session ID Generated
                      ↓
              User Authenticated
                      ↓
            ┌───────────────────┐
            │ Active Application │
            └─────────┬─────────┘
                      ↓
                User Activity
                      ↓
              Update Activity
                      ↓
             Session Continues
                      │
          ┌───────────┴───────────┐
          ↓                       ↓
     Auto Logout             Manual Logout
          ↓                       ↓
   Session Invalidated       Session Invalidated
          ↓                       ↓
       Login Page               Login Page
Related Security Settings

Session management works with the following configuration values:

session_timeout
force_logout_on_password_change
invalidate_other_sessions
force_single_device_login
enable_auto_logout
auto_logout_time
show_logout_warning
logout_warning_time

These settings allow the application to implement different session security policies without changing the database structure.

Summary

The Bishal Starter Kit uses Laravel database sessions and provides the foundation for:

Authenticated sessions
Session timeout
Active session tracking
IP address tracking
User-agent tracking
Last activity tracking
Logout
Logout other devices
Session invalidation
Password-change session invalidation
Single-device login
Automatic logout
Session-based security

The sessions table provides the persistent session information, while Laravel's authentication and session APIs handle the current authenticated session.