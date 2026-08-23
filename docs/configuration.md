Configuration

The Bishal Starter Kit provides a centralized configuration system for controlling application behavior, authentication, security, localization, screen saver, automatic logout, backup, password policies, user management, licensing, and notifications.

Configuration records are stored in the configurations table.

Each configuration can optionally belong to an organization through organization_id.

Configuration Model

The configuration table contains:

configurations

Each configuration record is associated with an organization when organization_id is provided.

$configuration->organization_id;

The following sections describe the available configuration options.

Application Settings
Application Version
application_version

Defines the current application version.

Default:

1.0.0

Example:

application_version = 1.0.0
Default Language
default_language

Defines the default application language.

The starter kit supports:

en
np

Default:

en

Example:

default_language = np

Use np to configure Nepali as the default language.

Timezone
timezone

Defines the application's timezone.

The current migration has en as its default value. If this field is used for timezone configuration, a valid PHP timezone such as Asia/Kathmandu should be stored.

Example:

timezone = Asia/Kathmandu
Decimal Places
decimal_places

Defines the number of decimal places used when displaying numeric values.

Default:

2

Example:

decimal_places = 2
Maintenance Mode
maintenance_mode

Enables or disables application maintenance mode.

Default:

false

Example:

maintenance_mode = true
Maintenance Mode Allowed IPs
maintenance_mode_allowed_ips

Contains IP addresses that are allowed to access the application while maintenance mode is enabled.

The value is stored as JSON.

Example:

[
    "127.0.0.1",
    "192.168.1.100"
]
Session Timeout
session_timeout

Defines the configured session timeout.

The migration documents this value as being measured in days.

Default:

2

Example:

session_timeout = 2

Automatic logout is configured separately using enable_auto_logout and auto_logout_time.

Data Table Source
data_table_source

Defines the application's data table processing source.

Default:

server

Example:

data_table_source = server

The intended values are:

server
client
Default Pagination Size
default_pagination_size

Defines the default number of records displayed per page.

Default:

20

Example:

default_pagination_size = 20
Registration Settings
Enable Registration
enable_registration

Controls whether users can register through the application's authentication system.

Default:

false
Email Verification
enable_email_verification

Controls whether email verification is enabled.

Default:

false
Two-Factor Authentication
enable_2fa

Controls whether two-factor authentication is enabled.

Default:

false

The starter kit uses Laravel Jetstream for authentication.

Multiple Branches
enable_multiple_branch

Controls whether multiple organization branches are enabled.

Default:

false
Password Change Security
Force Logout on Password Change
force_logout_on_password_change

Controls whether a user should be logged out after changing their password.

Default:

true
Invalidate Other Sessions
invalidate_other_sessions

Controls whether other authenticated sessions should be invalidated after a password change.

Default:

true
Cache
Cache Lifetime
cache_lifetime

Defines the configured cache lifetime used by the application.

Default:

20

The unit depends on how this configuration is consumed by the application.

Date and Time
Date Type
date_type

Defines the date system used by the application.

Supported values:

ad
bs

Default:

ad

Where:

ad = Gregorian/AD date
bs = Bikram Sambat date

Example:

date_type = bs
Date Format
date_format

Defines the application's date format.

Default:

Y-m-d

The value follows PHP date formatting syntax.

Example:

date_format = d/m/Y
Time Format
time_format

Defines the application's time display format.

Default:

24hour
Screen Saver

The starter kit provides configurable screen saver functionality.

Enable Screen Saver
enable_screen_saver

Default:

false
Screen Saver Timeout
screen_saver_timeout

Defines how long the application waits before activating the screen saver.

The value is measured in seconds.

Default:

300

This represents 5 minutes.

Screen Saver Type
screen_saver_type

Supported values:

image
slider
video

Default:

image
Screen Saver Images
screen_saver_images

Stores screen saver image information as JSON.

Example:

[
    "/storage/screensaver/image-1.jpg",
    "/storage/screensaver/image-2.jpg"
]
Screen Saver Video
screen_saver_video

Stores the configured screen saver video path.

Show Clock
screen_saver_show_clock

Controls whether the clock is displayed.

Default:

true
Show Date
screen_saver_show_date

Controls whether the date is displayed.

Default:

true
Automatic Logout

Automatic logout is controlled using:

enable_auto_logout
auto_logout_time
show_logout_warning
logout_warning_time

Default:

enable_auto_logout = false
auto_logout_time = 30
show_logout_warning = true
logout_warning_time = 1

auto_logout_time and logout_warning_time are measured in minutes.

For detailed information, see Automatic Logout.

IP Security
Enable IP Blacklist
enable_ip_blacklist

Enables or disables IP blacklist protection.

Default:

false
Blacklisted IPs
blacklisted_ips

Stores blocked IP addresses as JSON.

Example:

[
    "192.168.1.100",
    "10.0.0.20"
]
Log Blocked IP Attempts
log_blocked_ip_attempts

Controls whether blocked IP attempts should be logged.

Default:

true
Login Security
Enable Login Attempt Limit
enable_login_attempt_limit

Enables login attempt protection.

Default:

true
Maximum Login Attempts
max_login_attempts

Defines the maximum number of login attempts.

Default:

5
Login Lockout Duration
login_lockout_duration

Defines how long a user remains locked after exceeding the configured login attempt limit.

The value is measured in minutes.

Default:

15
Footer
Footer Text
footer_text

Contains custom footer text displayed by the application.

Default:

null

Example:

footer_text = © 2026 Bishal Starter Kit
Backup

Backup functionality is provided through Spatie Laravel Backup.

The following settings control automatic backup behavior:

enable_auto_backup
backup_frequency
backup_retention_days

Default:

enable_auto_backup = false
backup_frequency = daily
backup_retention_days = 30

For complete information, see Backup.

Password Policy
Enable Password Policy
enable_password_policy

Enables the application's password policy.

Default:

false
Minimum Password Length
minimum_password_length

Defines the minimum password length.

Default:

8
Require Uppercase
require_uppercase

Requires uppercase characters in passwords.

Default:

true
Require Lowercase
require_lowercase

Requires lowercase characters in passwords.

Default:

true
Require Number
require_number

Requires numbers in passwords.

Default:

true
Require Special Character
require_special_character

Requires a special character in passwords.

Default:

false
Password Expiry
password_expiry_days

Defines the configured password expiration period.

Default:

90
User Management
Automatically Disable Inactive Users
auto_disable_inactive_users

Controls whether inactive users should automatically be disabled.

Default:

false
Inactive User Days
inactive_user_days

Defines the number of inactive days before a user can be disabled.

Default:

90
Enable Account Deletion
enable_delete_account

Controls whether users can delete their accounts.

Default:

false
Force Single Device Login
force_single_device_login

Controls whether users are restricted to a single active device/session.

Default:

false
License
License Key
license_key

Stores the application's license key.

Default:

null

The license key can be associated with an organization through the configuration's organization_id.

Toast Notifications

The starter kit provides configurable toast notification settings.

Position
position

Supported values:

top-right
top-center
top-left
bottom-right
bottom-center
bottom-left

Default:

top-right
Timeout
timeout

Defines how long a toast notification remains visible.

Default:

5000

The value is typically interpreted as milliseconds by the frontend notification system.

Close on Click
closeOnClick

Controls whether the notification can be closed by clicking it.

Default:

true
Pause on Focus Loss
pauseOnFocusLoss

Controls whether the notification timer pauses when the browser loses focus.

Default:

true
Pause on Hover
pauseOnHover

Controls whether the notification timer pauses when the user hovers over the notification.

Default:

true
Close Button
closeButton

Controls whether the notification displays a close button.

Default:

true
Hide Progress Bar
hideProgressBar

Controls whether the toast progress bar is hidden.

Default:

false
RTL
rtl

Controls right-to-left toast notification layout.

Default:

true
Toast Icon
icon

The migration defines the following supported values:

true
false
fas fa-rocket
MyIconComponent
material

Default:

true
Organization-Specific Configuration

The configuration table contains:

organization_id

The field is nullable and references the organizations table.

$table->foreignId('organization_id')
    ->nullable()
    ->constrained();

This allows configuration to be associated with a particular organization.

Example:

$configuration = Configuration::where(
    'organization_id',
    $organizationId
)->first();
Configuration Groups

For easier management, the configuration options can be grouped as follows:

Group	Main Settings
Application	Version, language, timezone, decimals
Maintenance	Maintenance mode, allowed IPs
Pagination	Data source, pagination size
Registration	Registration, email verification, 2FA
Security	Password change, sessions, login attempts
Date & Time	Date type, format, time format
Screen Saver	Timeout, type, images, video
Auto Logout	Timeout and logout warning
IP Security	Blacklist and logging
Backup	Frequency and retention
Password Policy	Length and character requirements
Users	Inactive users, account deletion, device login
License	License key
Notifications	Toast position, timeout, icons
Default Values

The most important default configuration values are:

application_version = 1.0.0
default_language = en
timezone = en
decimal_places = 2

maintenance_mode = false
session_timeout = 2
data_table_source = server
default_pagination_size = 20

enable_registration = false
enable_email_verification = false
enable_2fa = false
enable_multiple_branch = false

force_logout_on_password_change = true
invalidate_other_sessions = true
cache_lifetime = 20

date_type = ad
date_format = Y-m-d
time_format = 24hour

enable_screen_saver = false
screen_saver_timeout = 300
screen_saver_type = image

enable_auto_logout = false
auto_logout_time = 30
show_logout_warning = true
logout_warning_time = 1

enable_ip_blacklist = false
log_blocked_ip_attempts = true

enable_login_attempt_limit = true
max_login_attempts = 5
login_lockout_duration = 15

enable_auto_backup = false
backup_frequency = daily
backup_retention_days = 30

enable_password_policy = false
minimum_password_length = 8
require_uppercase = true
require_lowercase = true
require_number = true
require_special_character = false
password_expiry_days = 90

auto_disable_inactive_users = false
inactive_user_days = 90
enable_delete_account = false
force_single_device_login = false

license_key = null

position = top-right
timeout = 5000
closeOnClick = true
pauseOnFocusLoss = true
pauseOnHover = true
closeButton = true
hideProgressBar = false
rtl = true
icon = true