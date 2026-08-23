Password Policy

The Bishal Starter Kit provides configurable password security through the configurations table.

Password policy allows administrators to define password requirements and password expiration behavior.

Configuration Fields

The following password policy fields are available:

enable_password_policy
minimum_password_length
require_uppercase
require_lowercase
require_number
require_special_character
password_expiry_days

They are stored in the configurations table.

Enable Password Policy

Password policy is disabled by default:

$table->boolean('enable_password_policy')
    ->default(false);

Enable it with:

$configuration->update([
    'enable_password_policy' => true,
]);

When enabled, password validation should follow the configured requirements.

Minimum Password Length

The minimum_password_length field defines the minimum number of characters required.

Default:

8

Database definition:

$table->unsignedTinyInteger('minimum_password_length')
    ->default(8);

Example:

$configuration->update([
    'minimum_password_length' => 12,
]);

A password such as:

password123

would not satisfy a 12-character minimum.

Require Uppercase

The require_uppercase option determines whether a password must contain at least one uppercase character.

Default:

true

Example:

$configuration->update([
    'require_uppercase' => true,
]);

Example valid password:

Password123
Require Lowercase

The require_lowercase option requires at least one lowercase character.

Default:

true

Example:

$configuration->update([
    'require_lowercase' => true,
]);
Require Number

The require_number option requires at least one numeric character.

Default:

true

Example valid password:

Password123
Require Special Character

The require_special_character option determines whether a password must contain a special character.

Default:

false

Example:

$configuration->update([
    'require_special_character' => true,
]);

With this enabled, a password could require a character such as:

!
@
#
$
%
&
*

Example:

Password123!
Password Expiration

The password_expiry_days field determines how long a password remains valid.

Default:

90

Database definition:

$table->string('password_expiry_days')
    ->default(90);

For example:

password_expiry_days = 90

means the password can expire after 90 days.

Password Creation Date

The users table stores:

password_created_at

Migration:

$table->date('password_created_at')
    ->default(now());

This date can be used to determine when the password was last changed.

Password Expiration Date

Users also have:

password_expired_at

Migration:

$table->date('password_expired_at')
    ->nullable();

When a password is changed, the application can calculate the next expiration date.

Example:

$user->password_created_at = now();

$user->password_expired_at = now()->addDays(
    (int) $configuration->password_expiry_days
);

$user->save();
Checking Password Expiration

The application can check whether a user's password has expired:

if (
    $user->password_expired_at &&
    now()->greaterThanOrEqualTo(
        $user->password_expired_at
    )
) {
    // Password expired
}

A user can then be redirected to the password change page.

Password Validation

Password rules can be dynamically constructed from the configuration.

Example:

$rules = [
    'required',
    'string',
    'min:' . $configuration->minimum_password_length,
];

Add uppercase validation:

if ($configuration->require_uppercase) {
    $rules[] = 'regex:/[A-Z]/';
}

Add lowercase validation:

if ($configuration->require_lowercase) {
    $rules[] = 'regex:/[a-z]/';
}

Add number validation:

if ($configuration->require_number) {
    $rules[] = 'regex:/[0-9]/';
}

Add special character validation:

if ($configuration->require_special_character) {
    $rules[] = 'regex:/[^A-Za-z0-9]/';
}

The final rules can then be used in a Laravel validation request.

Complete Password Validation Example
$rules = [
    'required',
    'string',
    'min:' . $configuration->minimum_password_length,
];

if ($configuration->require_uppercase) {
    $rules[] = 'regex:/[A-Z]/';
}

if ($configuration->require_lowercase) {
    $rules[] = 'regex:/[a-z]/';
}

if ($configuration->require_number) {
    $rules[] = 'regex:/[0-9]/';
}

if ($configuration->require_special_character) {
    $rules[] = 'regex:/[^A-Za-z0-9]/';
}

$request->validate([
    'password' => $rules,
]);
Example Configuration

A strong password policy could be:

{
    "enable_password_policy": true,
    "minimum_password_length": 12,
    "require_uppercase": true,
    "require_lowercase": true,
    "require_number": true,
    "require_special_character": true,
    "password_expiry_days": 90
}

This requires passwords to:

Have at least 12 characters
Contain an uppercase character
Contain a lowercase character
Contain a number
Contain a special character
Expire after 90 days
Password Change

When a user changes their password, the application should update the password metadata.

Example:

use Illuminate\Support\Facades\Hash;

$user->update([
    'password' => Hash::make($request->password),
    'password_created_at' => now(),
    'password_expired_at' => now()->addDays(
        (int) $configuration->password_expiry_days
    ),
]);

The password should always be stored using Laravel's password hashing functionality.

Password Policy and Authentication

Password policy works together with the starter kit's authentication system.

The authentication system is based on Laravel Jetstream.

The password policy adds additional configurable requirements on top of the authentication functionality.

Laravel Jetstream
       ↓
Authentication
       ↓
Password Policy
       ↓
Password Validation
       ↓
User Account
Password Expiration Flow

When a user logs in:

Login
  ↓
Authentication successful
  ↓
Check password expiration
  ↓
Expired?
 ↙       ↘
Yes       No
 ↓         ↓
Change    Dashboard
Password

If the password has expired, the application can require the user to update it before continuing.

Configuration Model Casts

The boolean fields should be cast to boolean values in the Configuration model.

Example:

protected function casts(): array
{
    return [
        'enable_password_policy' => 'boolean',
        'require_uppercase' => 'boolean',
        'require_lowercase' => 'boolean',
        'require_number' => 'boolean',
        'require_special_character' => 'boolean',
    ];
}

The expiration value can be converted to an integer when used:

$expiryDays = (int) $configuration->password_expiry_days;
Recommended Password Policy

A recommended configuration is:

Password Policy:       Enabled
Minimum Length:        12
Uppercase:             Required
Lowercase:             Required
Number:                Required
Special Character:     Required
Expiration:            90 days

However, the actual policy should be selected according to the application's security requirements.

Configuration Fields
Field	Type	Default	Description
enable_password_policy	Boolean	false	Enables password policy
minimum_password_length	Tiny Integer	8	Minimum password length
require_uppercase	Boolean	true	Requires uppercase character
require_lowercase	Boolean	true	Requires lowercase character
require_number	Boolean	true	Requires a number
require_special_character	Boolean	false	Requires special character
password_expiry_days	String	90	Password expiration period
Summary

The Bishal Starter Kit provides configurable password security through the application configuration system.

Configuration
      ↓
Password Policy
      ↓
┌──────────────────────────┐
│ Minimum Length            │
│ Uppercase                 │
│ Lowercase                 │
│ Number                    │
│ Special Character         │
│ Password Expiration       │
└──────────────────────────┘
      ↓
User Password

The policy can be enabled or disabled and customized per the application's configuration requirements.