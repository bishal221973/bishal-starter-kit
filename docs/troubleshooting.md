Sidebar

The Bishal Starter Kit includes a configurable application sidebar built with Vue 3, Inertia.js, and Tailwind CSS.

The sidebar supports configurable positioning, width, colors, icons, collapsed state, and active navigation styling.

Sidebar Configuration

Sidebar settings are stored in the settings table.

The main sidebar configuration fields are:

sidebar_position
sidebar_width
sidebar_bg_color
sidebar_text_color
sidebar_hover_color
sidebar_hover_text_color
sidebar_active_color
sidebar_icon_color
sidebar_collapsed
Sidebar Position

The sidebar can be positioned in three locations:

left
right
top

Database definition:

$table->enum('sidebar_position', [
    'left',
    'right',
    'top',
])->default('left');

The default position is:

left

Update the position:

$settings->update([
    'sidebar_position' => 'right',
]);

Available values:

left
right
top
Sidebar Width

The default sidebar width is:

280px

Database definition:

$table->integer('sidebar_width')
    ->default(280);

Example:

$settings->update([
    'sidebar_width' => 300,
]);

The frontend can use this value dynamically:

<aside
    :style="{
        width: `${settings.sidebar_width}px`
    }"
>
    ...
</aside>
Sidebar Background Color

The sidebar background is controlled by:

sidebar_bg_color

Default:

#0f172a

Example:

$settings->update([
    'sidebar_bg_color' => '#111827',
]);
Sidebar Text Color

The main sidebar text color is controlled by:

sidebar_text_color

Default:

#ffffff

Example:

$settings->update([
    'sidebar_text_color' => '#e2e8f0',
]);
Sidebar Hover Color

When the user moves the mouse over a navigation item, the sidebar can use:

sidebar_hover_color

Default:

#173668

Example:

$settings->update([
    'sidebar_hover_color' => '#1e40af',
]);
Sidebar Hover Text Color

The text color while hovering is controlled by:

sidebar_hover_text_color

Default:

#ffffff

Example:

$settings->update([
    'sidebar_hover_text_color' => '#ffffff',
]);
Sidebar Active Color

The currently active navigation item uses:

sidebar_active_color

Default:

#3d98aa

Example:

$settings->update([
    'sidebar_active_color' => '#348797',
]);
Sidebar Icon Color

Navigation icons use:

sidebar_icon_color

Default:

#cbd5e1

Example:

$settings->update([
    'sidebar_icon_color' => '#94a3b8',
]);
Collapsed Sidebar

The sidebar supports a collapsed state.

Configuration:

sidebar_collapsed

Default:

$table->boolean('sidebar_collapsed')
    ->default(false);

Enable:

$settings->update([
    'sidebar_collapsed' => true,
]);

Disable:

$settings->update([
    'sidebar_collapsed' => false,
]);

A collapsed sidebar can reduce the navigation width and display only icons.

Sidebar Navigation

A typical sidebar contains navigation items such as:

Dashboard
Users
Organizations
Employees
Settings
Roles & Permissions

Navigation items should point to named Laravel routes.

Example:

<Link :href="route('dashboard')">
    Dashboard
</Link>
Active Navigation Item

The sidebar should indicate the current route.

With Inertia, the current route can be detected using the Ziggy route() helper or the application's existing route helper.

Example:

<Link
    :href="route('dashboard')"
    :class="{
        'active': route().current('dashboard')
    }"
>
    Dashboard
</Link>

The active item should use:

sidebar_active_color
Navigation Item Structure

A navigation item can contain:

Icon
Label
URL
Permission
Active State

Example:

{
    label: 'Employees',
    route: 'employees.index',
    icon: 'users',
    permission: 'employees.view'
}

The exact structure depends on the sidebar component implementation.

Permission-Based Navigation

The sidebar should only display navigation items the authenticated user is allowed to access.

Since the starter kit uses Spatie Laravel Permission, navigation can be permission-aware.

For example:

employees.view
employees.create
employees.update
employees.delete

A user without:

employees.view

should not see the Employees navigation item.

However, hiding a navigation item is not a security mechanism.

The backend route must also enforce the permission.

Example:

Route::middleware([
    'auth',
    'permission:employees.view',
])->group(function () {
    // Employee routes
});
Sidebar and Organizations

The starter kit supports organizations and branches.

Organization-specific navigation can therefore be displayed according to the currently selected organization.

For example:

Organization
    ↓
Dashboard
Employees
Documents
Settings

The backend should always verify that the authenticated user has access to the requested organization.

Sidebar Settings Model

The settings model should cast boolean values correctly.

Example:

protected function casts(): array
{
    return [
        'sidebar_collapsed' => 'boolean',
    ];
}
Complete Sidebar Configuration

The default sidebar configuration is:

[
    'sidebar_position' => 'left',
    'sidebar_width' => 280,

    'sidebar_bg_color' => '#0f172a',
    'sidebar_text_color' => '#ffffff',

    'sidebar_hover_color' => '#173668',
    'sidebar_hover_text_color' => '#ffffff',

    'sidebar_active_color' => '#3d98aa',
    'sidebar_icon_color' => '#cbd5e1',

    'sidebar_collapsed' => false,
]
Updating Sidebar Settings

For example, to create a custom sidebar:

$settings->update([
    'sidebar_position' => 'left',
    'sidebar_width' => 260,

    'sidebar_bg_color' => '#111827',
    'sidebar_text_color' => '#ffffff',

    'sidebar_hover_color' => '#1f2937',
    'sidebar_hover_text_color' => '#ffffff',

    'sidebar_active_color' => '#348797',
    'sidebar_icon_color' => '#cbd5e1',

    'sidebar_collapsed' => false,
]);
Vue Implementation

The sidebar can receive settings through Inertia shared props.

Example:

return Inertia::render('Dashboard', [
    'settings' => $settings,
]);

Or globally through Inertia middleware:

Inertia::share([
    'settings' => $settings,
]);

Then Vue can access:

<script setup>
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const settings = page.props.settings;
</script>
Dynamic Sidebar Styling

The sidebar can use CSS variables for dynamic colors.

Example:

<aside
    :style="{
        '--sidebar-bg': settings.sidebar_bg_color,
        '--sidebar-text': settings.sidebar_text_color,
        '--sidebar-hover': settings.sidebar_hover_color,
        '--sidebar-active': settings.sidebar_active_color,
        '--sidebar-icon': settings.sidebar_icon_color,
    }"
>
    ...
</aside>

CSS:

.sidebar {
    background-color: var(--sidebar-bg);
    color: var(--sidebar-text);
}

.sidebar-item:hover {
    background-color: var(--sidebar-hover);
}

.sidebar-item.active {
    background-color: var(--sidebar-active);
}
Tailwind CSS

The starter kit uses Tailwind CSS for the frontend.

For static colors, Tailwind classes can be used:

<aside class="bg-slate-900 text-white">

For colors configured dynamically from the database, CSS variables or inline styles are preferable because Tailwind cannot generate arbitrary runtime classes from database values.

Example:

<div
    :style="{
        backgroundColor: settings.sidebar_bg_color
    }"
>
Responsive Sidebar

The sidebar should adapt to different screen sizes.

A common responsive structure is:

Desktop
┌──────────┬──────────────────────┐
│ Sidebar  │ Main Content         │
│          │                      │
│          │                      │
└──────────┴──────────────────────┘

Mobile
┌───────────────────────────────┐
│ Header / Menu                 │
├───────────────────────────────┤
│                               │
│ Main Content                  │
│                               │
└───────────────────────────────┘

On mobile devices, the sidebar can be converted into an off-canvas navigation panel.

Sidebar Collapse Flow

When the user clicks the sidebar toggle:

Expanded
   ↓
Toggle
   ↓
Collapsed
   ↓
Toggle
   ↓
Expanded

The frontend can manage temporary UI state:

const collapsed = ref(false);

const toggleSidebar = () => {
    collapsed.value = !collapsed.value;
};

If the collapsed state should persist globally, it can be saved through the application settings.

Sidebar Layout Modes

The starter kit also has general layout configuration:

layout_mode
theme_mode
rtl

The sidebar should respect these settings.

For example:

layout_mode = full

allows the application to use the full viewport.

layout_mode = boxed

can constrain the main application layout.

RTL Support

The application has an RTL configuration:

rtl

When RTL is enabled, the sidebar layout may need to switch from the left side to the right side.

Example:

<aside
    :class="{
        'sidebar-left': !settings.rtl,
        'sidebar-right': settings.rtl
    }"
>

The exact implementation depends on the application's layout component.

Sidebar Database Structure

The sidebar-related database fields are:

$table->enum('sidebar_position', [
    'left',
    'right',
    'top',
])->default('left');

$table->integer('sidebar_width')
    ->default(280);

$table->string('sidebar_bg_color')
    ->default('#0f172a');

$table->string('sidebar_text_color')
    ->default('#ffffff');

$table->string('sidebar_hover_color')
    ->default('#173668');

$table->string('sidebar_hover_text_color')
    ->default('#ffffff');

$table->string('sidebar_active_color')
    ->default('#3d98aa');

$table->string('sidebar_icon_color')
    ->default('#cbd5e1');

$table->boolean('sidebar_collapsed')
    ->default(false);
Sidebar Configuration Reference
Field	Type	Default	Description
sidebar_position	Enum	left	Sidebar position
sidebar_width	Integer	280	Sidebar width
sidebar_bg_color	String	#0f172a	Background color
sidebar_text_color	String	#ffffff	Text color
sidebar_hover_color	String	#173668	Hover background
sidebar_hover_text_color	String	#ffffff	Hover text color
sidebar_active_color	String	#3d98aa	Active item color
sidebar_icon_color	String	#cbd5e1	Icon color
sidebar_collapsed	Boolean	false	Collapsed state
Summary

The Bishal Starter Kit sidebar is configurable through the settings table and supports:

Left, right, and top positioning
Custom width
Background color
Text color
Hover colors
Active navigation color
Icon color
Collapsed state
Responsive layouts
RTL support
Permission-aware navigation
Vue 3
Inertia.js
Tailwind CSS

The sidebar controls the application's primary navigation, while Laravel routes and Spatie permissions remain responsible for actual backend access control.