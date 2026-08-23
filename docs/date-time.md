Date and Time

The Bishal Starter Kit provides configurable date and time handling.

Date and time behavior can be controlled through the application configuration, including:

AD (Gregorian) dates
BS (Bikram Sambat) dates
Date formatting
Time formatting
Application timezone
Date display
Time display
Date Type

The application configuration contains:

date_type

This determines which calendar system the application uses when displaying dates.

Supported values:

ad
bs

Default:

ad

Where:

ad = Gregorian / Anno Domini
bs = Bikram Sambat

Example:

date_type = ad

For Nepali calendar dates:

date_type = bs
Date Format

The date format is configured using:

date_format

Default:

Y-m-d

The format uses PHP date formatting characters.

Example:

date_format = Y-m-d

Output:

2026-08-24

Another example:

date_format = d/m/Y

Output:

24/08/2026
Common Date Formats
Format	Example	Description
Y-m-d	2026-08-24	ISO-style date
d/m/Y	24/08/2026	Day/month/year
m/d/Y	08/24/2026	Month/day/year
d-m-Y	24-08-2026	Day-month-year
M d, Y	Aug 24, 2026	Short month
F d, Y	August 24, 2026	Full month
Timezone

The application configuration contains:

timezone

The timezone should contain a valid PHP timezone identifier.

For Nepal:

Asia/Kathmandu

Example:

timezone = Asia/Kathmandu

Nepal uses UTC+05:45.

The current migration defines en as the default value for timezone. For actual timezone configuration, use a valid timezone such as Asia/Kathmandu.

Time Format

The application configuration contains:

time_format

The default value is:

24hour

This indicates that the application is configured to use a 24-hour time format.

Example:

time_format = 24hour

Example output:

14:30
24-Hour Format

The 24-hour format represents time from:

00:00

through:

23:59

Examples:

00:30
08:15
12:00
14:45
23:59
Laravel Timezone Configuration

Laravel's application timezone can be configured in:

config/app.php

Example:

'timezone' => 'Asia/Kathmandu',

After changing the configuration, clear the cached configuration:

php artisan config:clear

If configuration is cached in production:

php artisan config:cache
Carbon

Laravel uses Carbon for date and time manipulation.

Example:

use Carbon\Carbon;

$date = Carbon::now();

To explicitly use Nepal time:

$date = Carbon::now('Asia/Kathmandu');
Current Date and Time

Get the current date and time:

use Carbon\Carbon;

$now = Carbon::now();

Get the current Nepal date and time:

$now = Carbon::now('Asia/Kathmandu');
Formatting Dates

Carbon can format dates using PHP date format characters.

Example:

$date = Carbon::now();

echo $date->format('Y-m-d');

Output:

2026-08-24

Another example:

echo $date->format('d/m/Y');

Output:

24/08/2026
Formatting Time

Example:

$time = Carbon::now();

echo $time->format('H:i');

Output:

14:30

Including seconds:

echo $time->format('H:i:s');

Output:

14:30:25
Date and Time Together

Example:

$dateTime = Carbon::now();

echo $dateTime->format('Y-m-d H:i:s');

Output:

2026-08-24 14:30:25
Converting Timezones

Carbon can convert a date/time to another timezone.

Example:

$date = Carbon::now('UTC');

$nepalTime = $date->setTimezone('Asia/Kathmandu');

The original instant remains the same while the displayed timezone changes.

Database Dates

Laravel automatically casts common timestamp fields such as:

created_at
updated_at

to Carbon instances when using Eloquent models.

Example:

$user->created_at;

Format the value:

$user->created_at->format('Y-m-d');
Model Date Casting

For custom date fields, use Eloquent casts.

Example:

protected function casts(): array
{
    return [
        'date_of_birth' => 'date',
        'joined_at' => 'date',
        'trial_ends_at' => 'datetime',
    ];
}

Then the values can be handled using Carbon methods:

$user->date_of_birth->format('Y-m-d');
Date of Birth

The organization_user table contains:

date_of_birth

This field stores the employee's date of birth.

Example:

$employee->date_of_birth;

Format it:

$employee->date_of_birth->format('Y-m-d');
Employment Dates

The organization employee information supports:

joined_at
probation_ends_at
employment_ends_at

These fields are stored as dates.

Example:

$employee->joined_at->format('Y-m-d');
Organization Dates

Organizations contain subscription and trial dates:

trial_ends_at
subscription_ends_at

These are datetime fields.

Example:

$organization->trial_ends_at;

Check whether a trial has expired:

if ($organization->trial_ends_at?->isPast()) {
    // Trial expired
}
Password Dates

The users table contains:

password_created_at
password_expired_at

password_created_at records when the password was created.

password_expired_at stores the password expiration date when password expiration is enabled.

Example:

$user->password_expired_at;

Check expiration:

if (
    $user->password_expired_at &&
    $user->password_expired_at->isPast()
) {
    // Password expired
}
Login Lockout Time

The users table contains:

locked_until

This field stores the date and time until which a user is locked.

Example:

if (
    $user->locked_until &&
    $user->locked_until->isFuture()
) {
    // User is locked
}
Automatic Logout Time

Automatic logout is configured using:

enable_auto_logout
auto_logout_time
show_logout_warning
logout_warning_time

The timeout values are measured in minutes.

Example:

enable_auto_logout = true
auto_logout_time = 30
show_logout_warning = true
logout_warning_time = 1

This means the application can automatically log out the user after 30 minutes and display a warning 1 minute before logout.

See:

Automatic Logout

Date Type in Application Configuration

The complete date/time configuration is stored in the configurations table.

Relevant fields:

date_type
date_format
time_format
timezone

Example:

date_type = bs
date_format = Y-m-d
time_format = 24hour
timezone = Asia/Kathmandu
Nepali Date / Bikram Sambat

The starter kit supports the configuration value:

date_type = bs

When BS mode is enabled, application date presentation can use the Bikram Sambat calendar.

Example configuration:

date_type = bs
timezone = Asia/Kathmandu

The exact BS conversion implementation should be handled by the application's date conversion functionality rather than assuming that a Gregorian date can simply be reformatted.

AD / Gregorian Date

To use the Gregorian calendar:

date_type = ad

This is the default configuration.

Example:

date_type = ad
date_format = Y-m-d
Frontend Date Formatting

Vue components can receive date values from Laravel through Inertia.

Example:

return Inertia::render('Users/Index', [
    'user' => $user,
]);

A date can then be displayed in Vue.

For simple display:

<template>
    <span>
        {{ user.created_at }}
    </span>
</template>

For custom formatting, use a dedicated frontend date formatting utility rather than duplicating date conversion logic throughout components.

Recommended Date Handling

When working with dates in the starter kit:

Store dates using appropriate database date/datetime columns.
Use Carbon for server-side date manipulation.
Configure the application timezone correctly.
Use the configured date_type for AD/BS presentation.
Use date_format for display formatting.
Avoid manually manipulating date strings.
Keep date conversion logic centralized.
Configuration Example

A typical Nepal-based configuration can be:

default_language = np
timezone = Asia/Kathmandu

date_type = bs
date_format = Y-m-d
time_format = 24hour

A typical English/AD configuration can be:

default_language = en
timezone = Asia/Kathmandu

date_type = ad
date_format = Y-m-d
time_format = 24hour