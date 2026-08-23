Notifications

The Bishal Starter Kit provides notification configuration through the configurations table and supports configurable toast notifications for the application UI.

Toast Notification Configuration

The notification settings are stored in the configurations table.

The following fields are available:

position
timeout
closeOnClick
pauseOnFocusLoss
pauseOnHover
closeButton
hideProgressBar
rtl
icon
Notification Position

The position field controls where toast notifications appear.

Available positions:

top-right
top-center
top-left
bottom-right
bottom-center
bottom-left

The default is:

top-right

Database definition:

$table->enum('position', [
    'top-right',
    'top-center',
    'top-left',
    'bottom-right',
    'bottom-center',
    'bottom-left'
])->default('top-right');

Example:

$configuration->update([
    'position' => 'bottom-right',
]);
Notification Timeout

The timeout field determines how long a notification remains visible.

The default value is:

5000

This represents milliseconds.

Therefore:

5000 ms = 5 seconds

Example:

$configuration->update([
    'timeout' => 5000,
]);
Close on Click

The closeOnClick option determines whether the user can close a notification by clicking it.

Default:

true

Database definition:

$table->boolean('closeOnClick')->default(true);

Example:

$configuration->update([
    'closeOnClick' => true,
]);
Pause on Focus Loss

The pauseOnFocusLoss option controls whether the notification timer pauses when the browser loses focus.

Default:

true

Example:

$configuration->update([
    'pauseOnFocusLoss' => true,
]);
Pause on Hover

The pauseOnHover option pauses the notification timer while the user hovers over the notification.

Default:

true

Example:

$configuration->update([
    'pauseOnHover' => true,
]);
Close Button

The closeButton option controls whether a close button is displayed.

Default:

true

Example:

$configuration->update([
    'closeButton' => true,
]);
Hide Progress Bar

The hideProgressBar option controls whether the notification progress indicator is displayed.

Default:

false

When false, the progress bar is displayed.

When true, it is hidden.

Example:

$configuration->update([
    'hideProgressBar' => false,
]);
RTL

The rtl option controls right-to-left notification behavior.

Default:

true

Database definition:

$table->boolean('rtl')->default(true);

Example:

$configuration->update([
    'rtl' => true,
]);
Notification Icon

The icon field controls the notification icon configuration.

Available values in the database:

true
false
fas fa-rocket
MyIconComponent
material

Database definition:

$table->enum('icon', [
    'true',
    'false',
    'fas fa-rocket',
    'MyIconComponent',
    'material'
])->default('true');

For example:

$configuration->update([
    'icon' => 'fas fa-rocket',
]);
Complete Configuration Example

A complete notification configuration could be:

$configuration->update([
    'position' => 'top-right',
    'timeout' => 5000,
    'closeOnClick' => true,
    'pauseOnFocusLoss' => true,
    'pauseOnHover' => true,
    'closeButton' => true,
    'hideProgressBar' => false,
    'rtl' => true,
    'icon' => 'true',
]);
Notification Configuration in Vue

The backend configuration can be passed to Vue/Inertia as part of the application's shared data.

For example:

return [
    'notifications' => [
        'position' => $configuration->position,
        'timeout' => $configuration->timeout,
        'closeOnClick' => $configuration->closeOnClick,
        'pauseOnFocusLoss' => $configuration->pauseOnFocusLoss,
        'pauseOnHover' => $configuration->pauseOnHover,
        'closeButton' => $configuration->closeButton,
        'hideProgressBar' => $configuration->hideProgressBar,
        'rtl' => $configuration->rtl,
        'icon' => $configuration->icon,
    ],
];

The frontend can then use these values to configure the toast notification library.

Notification Types

The application can use notifications for common events such as:

Success
Error
Warning
Info

Example usage:

toast.success('User created successfully');
toast.error('Something went wrong');
toast.warning('Please check the information');
toast.info('Your session will expire soon');

The exact frontend implementation depends on the notification library used by the application.

Recommended Configuration

A typical configuration is:

Position:           top-right
Timeout:            5000
Close on click:     enabled
Pause on focus loss: enabled
Pause on hover:     enabled
Close button:       enabled
Progress bar:       enabled
RTL:                enabled
Icon:               true
Configuration Table
Field	Type	Default	Description
position	Enum	top-right	Toast position
timeout	Integer	5000	Display duration in milliseconds
closeOnClick	Boolean	true	Close notification when clicked
pauseOnFocusLoss	Boolean	true	Pause timer when browser loses focus
pauseOnHover	Boolean	true	Pause timer while hovering
closeButton	Boolean	true	Show close button
hideProgressBar	Boolean	false	Hide progress bar
rtl	Boolean	true	Enable RTL notification behavior
icon	Enum	true	Notification icon configuration
Updating Notification Settings

An administrator can update notification settings from the application configuration page.

Example:

$configuration->update([
    'position' => $request->position,
    'timeout' => $request->timeout,
    'closeOnClick' => $request->boolean('closeOnClick'),
    'pauseOnFocusLoss' => $request->boolean('pauseOnFocusLoss'),
    'pauseOnHover' => $request->boolean('pauseOnHover'),
    'closeButton' => $request->boolean('closeButton'),
    'hideProgressBar' => $request->boolean('hideProgressBar'),
    'rtl' => $request->boolean('rtl'),
    'icon' => $request->icon,
]);
Validation

Notification settings can be validated using:

$request->validate([
    'position' => [
        'required',
        'in:top-right,top-center,top-left,bottom-right,bottom-center,bottom-left',
    ],

    'timeout' => [
        'required',
        'integer',
        'min:0',
    ],

    'icon' => [
        'required',
        'in:true,false,fas fa-rocket,MyIconComponent,material',
    ],
]);

Boolean values can be handled with:

$request->boolean('closeOnClick');
$request->boolean('pauseOnFocusLoss');
$request->boolean('pauseOnHover');
$request->boolean('closeButton');
$request->boolean('hideProgressBar');
$request->boolean('rtl');
Summary

The Bishal Starter Kit provides centralized toast notification configuration through the configurations table.

Configuration
      ↓
Notification Settings
      ↓
Inertia Shared Data
      ↓
Vue Application
      ↓
Toast Notification

Administrators can control the position, duration, behavior, close button, progress bar, RTL mode, and icon without changing the frontend notification configuration directly.