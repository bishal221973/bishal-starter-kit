Screen Saver

The Bishal Starter Kit includes a configurable screen saver system. Screen saver settings are stored in the configurations table and can be customized by the application administrator.

The screen saver can display:

Images
Image sliders
Videos
Clock
Date
Configuration Fields

The screen saver configuration contains the following fields:

enable_screen_saver
screen_saver_timeout
screen_saver_type
screen_saver_images
screen_saver_video
screen_saver_show_clock
screen_saver_show_date
Enable Screen Saver

The screen saver is disabled by default.

$table->boolean('enable_screen_saver')
    ->default(false);

Enable it:

$configuration->update([
    'enable_screen_saver' => true,
]);

Disable it:

$configuration->update([
    'enable_screen_saver' => false,
]);
Screen Saver Timeout

The screen_saver_timeout field determines how long the user must remain inactive before the screen saver appears.

The value is stored in seconds.

Default:

300 seconds

This is equal to:

5 minutes

Database definition:

$table->unsignedInteger('screen_saver_timeout')
    ->default(300);

For example, to enable the screen saver after 10 minutes:

$configuration->update([
    'screen_saver_timeout' => 600,
]);

Common values:

Time	Seconds
1 minute	60
5 minutes	300
10 minutes	600
15 minutes	900
30 minutes	1800
Screen Saver Type

The starter kit supports three screen saver types:

image
slider
video

Database definition:

$table->enum('screen_saver_type', [
    'image',
    'slider',
    'video',
])->default('image');
Image Screen Saver

The image type displays a single screen saver image.

$configuration->update([
    'screen_saver_type' => 'image',
]);

The image configuration is stored in:

screen_saver_images
Slider Screen Saver

The slider type allows multiple images to be displayed as a slideshow.

$configuration->update([
    'screen_saver_type' => 'slider',
]);

Images are stored in:

screen_saver_images

The field is JSON:

$table->json('screen_saver_images')
    ->nullable();

Example value:

[
    "screensaver/image1.jpg",
    "screensaver/image2.jpg",
    "screensaver/image3.jpg"
]
Screen Saver Images

Multiple images can be stored as JSON.

Example:

$configuration->update([
    'screen_saver_images' => [
        'screensaver/image1.jpg',
        'screensaver/image2.jpg',
        'screensaver/image3.jpg',
    ],
]);

The Configuration model should cast this field to an array:

protected function casts(): array
{
    return [
        'screen_saver_images' => 'array',
    ];
}

Then the images can be accessed as:

$configuration->screen_saver_images;
Video Screen Saver

The video type uses the screen_saver_video field.

$configuration->update([
    'screen_saver_type' => 'video',
    'screen_saver_video' => 'screensaver/background.mp4',
]);

Database definition:

$table->string('screen_saver_video')
    ->nullable();
Show Clock

The screen saver can display a clock.

The configuration field is:

screen_saver_show_clock

It is enabled by default:

$table->boolean('screen_saver_show_clock')
    ->default(true);

Disable the clock:

$configuration->update([
    'screen_saver_show_clock' => false,
]);

Enable it:

$configuration->update([
    'screen_saver_show_clock' => true,
]);
Show Date

The screen saver can also display the current date.

Configuration:

screen_saver_show_date

It is enabled by default:

$table->boolean('screen_saver_show_date')
    ->default(true);

Disable the date:

$configuration->update([
    'screen_saver_show_date' => false,
]);
Complete Configuration Example

A typical screen saver configuration could be:

$configuration->update([
    'enable_screen_saver' => true,

    'screen_saver_timeout' => 300,

    'screen_saver_type' => 'slider',

    'screen_saver_images' => [
        'screensaver/image1.jpg',
        'screensaver/image2.jpg',
        'screensaver/image3.jpg',
    ],

    'screen_saver_video' => null,

    'screen_saver_show_clock' => true,

    'screen_saver_show_date' => true,
]);
Vue / Inertia Implementation

Because the starter kit uses Vue 3 and Inertia.js, the screen saver can be implemented on the frontend.

The configuration can be shared with Vue through Inertia props.

For example:

return [
    'screenSaver' => [
        'enabled' => $configuration->enable_screen_saver,
        'timeout' => $configuration->screen_saver_timeout,
        'type' => $configuration->screen_saver_type,
        'images' => $configuration->screen_saver_images,
        'video' => $configuration->screen_saver_video,
        'showClock' => $configuration->screen_saver_show_clock,
        'showDate' => $configuration->screen_saver_show_date,
    ],
];

Vue can then access the configuration through the Inertia page props.

Detecting User Inactivity

The screen saver should start after the configured inactivity period.

Typical browser events to monitor include:

mousemove
mousedown
keydown
scroll
touchstart

Whenever one of these events occurs, the inactivity timer should be reset.

Conceptually:

User Activity
     ↓
Reset Timer
     ↓
No Activity
     ↓
Timeout Reached
     ↓
Show Screen Saver
Screen Saver Lifecycle

The typical lifecycle is:

Application Loaded
       ↓
Read Configuration
       ↓
Is Screen Saver Enabled?
       ↓
     Yes
       ↓
Start Inactivity Timer
       ↓
User Active?
   ↙          ↘
 Yes          No
  ↓            ↓
Reset       Continue
Timer        Timer
               ↓
          Timeout Reached
               ↓
        Display Screen Saver
Closing the Screen Saver

When the user interacts with the application while the screen saver is active, the screen saver can be closed and the inactivity timer restarted.

Example flow:

Screen Saver
     ↓
User clicks / presses key
     ↓
Hide Screen Saver
     ↓
Reset inactivity timer
     ↓
Application available
Image Slider

When using:

screen_saver_type = slider

the frontend can iterate through:

$configuration->screen_saver_images

Example Vue data:

const images = screenSaver.images ?? [];

A slider can then change the active image at a fixed interval.

The exact slider interval is a frontend implementation detail and is not stored in the current configuration schema.

Video

When:

screen_saver_type = video

the frontend should use:

screen_saver_video

Example:

const video = screenSaver.video;

The video can then be rendered using an HTML <video> element.

Screen Saver Display

A typical screen saver should cover the application viewport:

.screen-saver {
    position: fixed;
    inset: 0;
    z-index: 9999;
}

It should appear above the normal application interface.

The exact visual design can be customized using the starter kit's Vue and Tailwind CSS setup.

Configuration Model

The relevant configuration casts should include:

protected function casts(): array
{
    return [
        'enable_screen_saver' => 'boolean',

        'screen_saver_images' => 'array',

        'screen_saver_show_clock' => 'boolean',

        'screen_saver_show_date' => 'boolean',
    ];
}
Database Structure

The screen saver fields are defined as:

$table->boolean('enable_screen_saver')
    ->default(false);

$table->unsignedInteger('screen_saver_timeout')
    ->default(300);

$table->enum('screen_saver_type', [
    'image',
    'slider',
    'video',
])->default('image');

$table->json('screen_saver_images')
    ->nullable();

$table->string('screen_saver_video')
    ->nullable();

$table->boolean('screen_saver_show_clock')
    ->default(true);

$table->boolean('screen_saver_show_date')
    ->default(true);
Configuration Reference
Field	Type	Default	Description
enable_screen_saver	Boolean	false	Enables the screen saver
screen_saver_timeout	Integer	300	Inactivity timeout in seconds
screen_saver_type	Enum	image	image, slider, or video
screen_saver_images	JSON	null	Screen saver image paths
screen_saver_video	String	null	Screen saver video path
screen_saver_show_clock	Boolean	true	Displays the clock
screen_saver_show_date	Boolean	true	Displays the date
Example Settings

For a 5-minute image slider:

Enabled:        Yes
Timeout:        300 seconds
Type:           slider
Images:         Multiple images
Clock:          Yes
Date:            Yes

For a video screen saver:

Enabled:        Yes
Timeout:        600 seconds
Type:           video
Video:          background.mp4
Clock:          Yes
Date:            Yes
Summary

The Bishal Starter Kit's screen saver is configurable through the application configuration.

Configuration
      ↓
Screen Saver Enabled
      ↓
User Inactivity
      ↓
Configured Timeout
      ↓
┌──────────────────────┐
│ Image                │
│ Slider               │
│ Video                │
└──────────────────────┘
      ↓
Clock / Date

The system supports image, slider, and video screen savers, with configurable inactivity timeout and optional clock/date display.