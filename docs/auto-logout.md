# Automatic Logout

The Bishal Starter Kit provides an automatic logout feature that can log out authenticated users after a configured period of inactivity or after the configured logout condition is reached.

Automatic logout is controlled through the `configurations` table.

## Configuration

The following fields control automatic logout:

```text
enable_auto_logout
auto_logout_time
show_logout_warning
logout_warning_time
```

## Enable Automatic Logout

Automatic logout is enabled using:

```text
enable_auto_logout
```

The default value is:

```text
false
```

To enable automatic logout, set:

```text
enable_auto_logout = true
```

When enabled, the application can automatically log out the authenticated user after the configured timeout.

## Auto Logout Time

The `auto_logout_time` field defines the automatic logout period in minutes.

```text
auto_logout_time
```

The default value is:

```text
30
```

Therefore, the default automatic logout period is **30 minutes**.

For example:

```text
auto_logout_time = 60
```

configures the automatic logout period to 60 minutes.

## Logout Warning

The starter kit can display a warning before automatically logging out the user.

This behavior is controlled by:

```text
show_logout_warning
```

The default value is:

```text
true
```

When enabled, users can be warned before their session is automatically terminated.

## Logout Warning Time

The `logout_warning_time` field specifies how many minutes before automatic logout the warning should be displayed.

```text
logout_warning_time
```

The default value is:

```text
1
```

This means the user can receive a warning **1 minute before automatic logout**.

For example:

```text
auto_logout_time = 30
logout_warning_time = 5
```

means:

* Automatic logout occurs after 30 minutes.
* The warning can appear 5 minutes before logout.
* The user therefore receives the warning around the 25-minute mark.

## Default Configuration

The default automatic logout configuration is:

```text
enable_auto_logout = false
auto_logout_time = 30
show_logout_warning = true
logout_warning_time = 1
```

This means automatic logout is disabled by default.

If automatic logout is enabled without changing the other values, the application uses a 30-minute timeout and displays a warning 1 minute before logout.

## Configuration Example

A configuration record could contain:

```php
[
    'enable_auto_logout' => true,
    'auto_logout_time' => 30,
    'show_logout_warning' => true,
    'logout_warning_time' => 5,
]
```

This configuration enables automatic logout after 30 minutes and displays a warning 5 minutes before logout.

## Difference Between Session Timeout and Auto Logout

The starter kit contains both:

```text
session_timeout
```

and:

```text
enable_auto_logout
auto_logout_time
```

These settings serve different purposes.

### Session Timeout

The `session_timeout` setting controls the application's session lifetime.

```text
session_timeout
```

The default value is:

```text
2
```

The value is documented as being in days.

### Automatic Logout

Automatic logout is an application-level feature controlled by:

```text
enable_auto_logout
auto_logout_time
```

The `auto_logout_time` value is measured in minutes.

For example:

```text
session_timeout = 2
```

and:

```text
auto_logout_time = 30
```

can be used together, where the application session can remain valid for up to the configured session lifetime while the frontend/application automatically logs the user out after the configured inactivity period.

## Logout Warning Flow

When automatic logout and the warning are enabled, the expected flow is:

```text
User is authenticated
        ↓
Automatic logout timer starts
        ↓
Warning period is reached
        ↓
Logout warning is displayed
        ↓
User activity / application handling
        ↓
Automatic logout
        ↓
User is logged out
```

The exact handling of user activity and warning dismissal depends on the starter kit's frontend implementation.

## Disabling Automatic Logout

To disable automatic logout:

```text
enable_auto_logout = false
```

The application should no longer automatically log users out using the automatic logout feature.

## Changing the Timeout

To change the automatic logout period, update:

```text
auto_logout_time
```

For example, to automatically log out after 1 hour:

```text
auto_logout_time = 60
```

For 2 hours:

```text
auto_logout_time = 120
```

## Changing the Warning Period

To show the warning 5 minutes before logout:

```text
show_logout_warning = true
logout_warning_time = 5
```

For a 10-minute warning:

```text
show_logout_warning = true
logout_warning_time = 10
```

## Recommended Configuration

For applications that require automatic session protection, an example configuration is:

```text
enable_auto_logout = true
auto_logout_time = 30
show_logout_warning = true
logout_warning_time = 5
```

This provides a 30-minute automatic logout period with a warning beginning 5 minutes before logout.

## Troubleshooting

### Automatic Logout Is Not Working

Check:

```text
enable_auto_logout
```

It must be:

```text
true
```

Then verify:

```text
auto_logout_time
```

contains the expected value in minutes.

### Warning Is Not Displayed

Check:

```text
show_logout_warning
```

It must be:

```text
true
```

Also verify:

```text
logout_warning_time
```

is smaller than:

```text
auto_logout_time
```

For example:

```text
auto_logout_time = 30
logout_warning_time = 5
```

is valid.

### User Is Logged Out Earlier Than Expected

Check both:

```text
session_timeout
```

and:

```text
auto_logout_time
```

The session lifetime and automatic logout mechanism are separate configuration options and can affect authentication behavior differently.

## Summary

Automatic logout is controlled by four configuration fields:

```text
enable_auto_logout
auto_logout_time
show_logout_warning
logout_warning_time
```

Default values:

```text
enable_auto_logout = false
auto_logout_time = 30
show_logout_warning = true
logout_warning_time = 1
```

The timeout is measured in **minutes**, while `session_timeout` is configured separately and is documented as being measured in **days**.
