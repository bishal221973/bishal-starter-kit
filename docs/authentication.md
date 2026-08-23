# Authentication

The Bishal Starter Kit uses **Laravel Jetstream** for authentication and user account management.

Jetstream provides the core authentication functionality while the starter kit extends it with organization, employee, security, and application-specific configuration.

## Authentication Features

The authentication system is based on Laravel Jetstream and provides:

* Login
* Logout
* User registration
* Password confirmation
* Password reset
* Email verification
* Password update
* Profile management
* Session management
* Authentication middleware
* Remember me functionality

Additional authentication behavior can be controlled through the starter kit's configuration settings.

## Jetstream

Laravel Jetstream provides the authentication foundation of the starter kit.

The authentication functionality should be managed through Jetstream rather than implementing a separate authentication system.

The starter kit can customize Jetstream's authentication behavior and integrate it with the application's organization and security features.

## User Model

Users are stored in the `users` table.

The user table contains:

```text
id
name
email
email_verified_at
password
remember_token
current_team_id
profile_photo_path
password_created_at
password_expired_at
login_attempts
locked_until
deleted_at
created_at
updated_at
```

The `User` model is located at:

```text
app/Models/User.php
```

The user model uses Laravel's authentication system and supports soft deletion.

## Login

Users can authenticate through the Jetstream login functionality.

After successful authentication, Laravel creates an authenticated session for the user.

The starter kit also tracks login attempts using:

```text
login_attempts
locked_until
```

These fields are used by the application's login security configuration.

## Logout

Authenticated users can log out through Jetstream's logout functionality.

Logging out terminates the current authenticated session.

The application can also force users to log out under certain security conditions, such as password changes.

## Registration

User registration is controlled by the starter kit configuration.

The `configurations` table contains:

```text
enable_registration
```

When registration is enabled, users can register through the Jetstream authentication interface.

When registration is disabled, new users should be created through the application's user management functionality.

## Email Verification

Email verification can be controlled using:

```text
enable_email_verification
```

When enabled, users may be required to verify their email address before accessing features that require verified accounts.

The user's verification status is stored in:

```text
email_verified_at
```

## Two-Factor Authentication

The starter kit provides a configuration option for two-factor authentication:

```text
enable_2fa
```

When enabled, two-factor authentication can be used as an additional security layer for user authentication.

The exact two-factor authentication behavior is handled through the Jetstream authentication system and the application's configuration.

## Password Management

Users can update their passwords through the authentication/profile functionality provided by Jetstream.

The application also tracks password creation and expiration:

```text
password_created_at
password_expired_at
```

These fields allow the starter kit to implement password expiration policies.

## Password Policy

Password requirements can be configured through the following configuration fields:

```text
enable_password_policy
minimum_password_length
require_uppercase
require_lowercase
require_number
require_special_character
password_expiry_days
```

For example, the default minimum password length is:

```text
8 characters
```

The default password policy requires:

```text
Uppercase: enabled
Lowercase: enabled
Number: enabled
Special character: disabled
```

Password expiration is configured using:

```text
password_expiry_days
```

The default value is:

```text
90 days
```

## Password Change Security

The application provides additional security settings for password changes.

### Force Logout

The following setting controls whether the user should be logged out after changing their password:

```text
force_logout_on_password_change
```

The default value is:

```text
true
```

### Invalidate Other Sessions

The following setting controls whether other authenticated sessions should be invalidated after a password change:

```text
invalidate_other_sessions
```

The default value is:

```text
true
```

This helps prevent previously authenticated devices from continuing to access the account after a password change.

## Login Attempt Security

The starter kit tracks failed login attempts using:

```text
login_attempts
locked_until
```

Login attempt protection can be configured with:

```text
enable_login_attempt_limit
max_login_attempts
login_lockout_duration
```

The default configuration is:

```text
enable_login_attempt_limit = true
max_login_attempts = 5
login_lockout_duration = 15 minutes
```

This means that the application can lock a user after the configured number of failed login attempts.

## Session Management

Authenticated sessions are stored in the `sessions` table when the application uses the database session driver.

The session table contains:

```text
id
user_id
ip_address
user_agent
payload
last_activity
```

The starter kit also provides:

```text
session_timeout
```

which can be used to configure the application's session timeout behavior.

## Organization and Authentication

Users can belong to organizations through the `organization_user` table.

The relationship allows a user to have organization-specific information such as:

```text
organization_id
employee_code
gender
date_of_birth
personal_email
personal_phone
address
city
state
country
postal_code
employee_type
department
designation
joined_at
probation_ends_at
employment_ends_at
salary
salary_type
is_active
can_login
```

The organization-user relationship is unique for each:

```text
organization_id + user_id
```

This allows the application to associate users with organizations while keeping organization-specific employee information separate from the main user account.

## Employee Login Permission

The `organization_user` table contains:

```text
can_login
```

This field determines whether an organization employee is allowed to log into the application.

The default value is:

```text
true
```

The employee can also be disabled using:

```text
is_active
```

## Inactive Users

The starter kit provides automatic inactive-user management.

The following configuration options control this behavior:

```text
auto_disable_inactive_users
inactive_user_days
```

When enabled, users who have been inactive for the configured number of days can be disabled.

## Single Device Login

The application provides an optional single-device login feature:

```text
force_single_device_login
```

When enabled, the application can restrict a user from maintaining multiple active device sessions.

## Protected Routes

Authenticated application routes should use Laravel's `auth` middleware.

Example:

```php
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        // Authenticated content.
    })->name('dashboard');
});
```

Only authenticated users can access routes protected by this middleware.

## Checking Authentication

Laravel's authentication helpers can be used to check the current authentication state.

```php
if (auth()->check()) {
    // User is authenticated.
}
```

Retrieve the authenticated user:

```php
$user = auth()->user();
```

## Authentication Configuration

Authentication-related behavior is primarily controlled through the `configurations` table.

Important authentication settings include:

```text
enable_registration
enable_email_verification
enable_2fa
force_logout_on_password_change
invalidate_other_sessions
enable_password_policy
minimum_password_length
require_uppercase
require_lowercase
require_number
require_special_character
password_expiry_days
auto_disable_inactive_users
inactive_user_days
force_single_device_login
enable_login_attempt_limit
max_login_attempts
login_lockout_duration
session_timeout
```

## Troubleshooting

### Login Page Is Not Available

Verify that Jetstream is installed and configured correctly.

Check the registered routes:

```bash
php artisan route:list
```

You should see the authentication routes provided by Jetstream.

### User Cannot Log In

Check:

* The user exists in the `users` table.
* The email address is correct.
* The password is correct.
* The user has not been disabled.
* The user is allowed to log in through `organization_user.can_login`.
* The account is not locked.
* The session configuration is correct.

### User Account Is Locked

Check the following fields:

```text
login_attempts
locked_until
```

Also check:

```text
enable_login_attempt_limit
max_login_attempts
login_lockout_duration
```

### Email Verification Is Required

Check:

```text
enable_email_verification
```

and the user's:

```text
email_verified_at
```

If email verification is enabled and `email_verified_at` is `NULL`, the user's email has not been verified.

## Summary

The Bishal Starter Kit uses **Laravel Jetstream as its authentication foundation**.

Jetstream handles the core authentication functionality, while the starter kit adds application-specific security and organization features such as:

* Login attempt limits
* Account lockout
* Password policies
* Password expiration
* Two-factor authentication configuration
* Session security
* Organization-based login permissions
* Inactive-user management
* Single-device login
* Password-change session invalidation

Authentication settings are primarily managed through the application's `configurations` table.
