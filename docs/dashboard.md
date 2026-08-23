Dashboard

The Bishal Starter Kit includes a dashboard interface built with Laravel, Inertia.js, Vue 3, and Tailwind CSS.

The dashboard provides the authenticated user with an overview of the application and acts as the main entry point after login.

Dashboard Access

The dashboard is available to authenticated users.

After successful authentication, users can access the dashboard through the application's dashboard route.

The dashboard should only be accessible to authenticated users.

Example route:

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth'])->name('dashboard');

The exact route implementation may differ depending on the starter kit version.

Dashboard Technologies

The dashboard uses:

Laravel
Inertia.js
Vue 3
Tailwind CSS
Vite

The application uses Inertia.js to connect Laravel routes/controllers with Vue pages without requiring a separate API for normal dashboard navigation.

Dashboard Page

The dashboard page is implemented as a Vue component.

A typical structure is:

resources/
└── js/
    └── Pages/
        └── Dashboard.vue

The page can be accessed through the Laravel dashboard route.

Example:

<script setup>
</script>

<template>
    <div>
        <h1>Dashboard</h1>
    </div>
</template>
Dashboard Layout

The dashboard uses the application's main authenticated layout.

The layout generally contains:

Dashboard
├── Sidebar
├── Navbar / Header
├── Main Content
└── Footer

The sidebar provides navigation between application modules while the navbar provides user and application controls.

Sidebar

The dashboard includes a configurable sidebar.

The sidebar supports different positions through application settings.

Available positions:

left
right
top

The default sidebar position is:

left

The sidebar can also be configured with a custom width.

Default width:

280px
Sidebar Configuration

Sidebar settings are stored in the settings table.

Available settings include:

sidebar_position
sidebar_width
sidebar_bg_color
sidebar_text_color
sidebar_hover_color
sidebar_hover_text_color
sidebar_active_color
sidebar_icon_color
sidebar_collapsed

Example:

sidebar_position = left
sidebar_width = 280
sidebar_collapsed = false
Sidebar Colors

The sidebar supports independent color configuration.

Background
sidebar_bg_color

Default:

#0f172a
Text
sidebar_text_color

Default:

#ffffff
Hover
sidebar_hover_color

Default:

#173668
Hover Text
sidebar_hover_text_color

Default:

#ffffff
Active Item
sidebar_active_color

Default:

#3d98aa
Icons
sidebar_icon_color

Default:

#cbd5e1
Collapsible Sidebar

The sidebar supports a collapsed state.

Configuration:

sidebar_collapsed

Default:

false

When enabled, the sidebar can display a compact navigation interface.

Navbar

The dashboard contains a configurable navbar/header.

Navbar settings are stored in the settings table.

Available settings include:

navbar_bg_color
navbar_text_color
navbar_border_color
navbar_height

Default navbar height:

70px
Dashboard Theme

The dashboard supports configurable themes.

The available theme modes are:

light
dark
system

Default:

light

Configuration:

theme_mode

Example:

theme_mode = dark
Dashboard Layout Mode

The dashboard supports two layout modes:

boxed
full

The default layout mode is:

full

Configuration:

layout_mode

Example:

layout_mode = boxed
RTL Support

The dashboard supports right-to-left layouts.

Configuration:

rtl

Default:

false

Example:

rtl = true

RTL support can be useful for applications that use right-to-left languages.

Dashboard Colors

The dashboard's main theme colors are configurable through the settings table.

Available colors include:

primary_color
secondary_color
accent_color
success_color
warning_color
danger_color
info_color
background_color
text_color
border_color

Default values:

primary_color = #3d98aa
secondary_color = #2f7f8f
accent_color = #4fb6c8

success_color = #22c55e
warning_color = #f59e0b
danger_color = #ef4444
info_color = #06b6d4

background_color = #ffffff
text_color = #1e293b
border_color = #e2e8f0
Dashboard Cards

Dashboard cards can be used to display application statistics.

Examples include:

Total Users
Active Users
Organizations
Employees
Recent Activities

A basic Vue card can be implemented as:

<div class="rounded-xl bg-white p-6 shadow">
    <h3 class="text-sm text-gray-500">
        Total Users
    </h3>

    <p class="mt-2 text-3xl font-bold">
        {{ totalUsers }}
    </p>
</div>

The actual dashboard statistics depend on the features implemented by the application.

Card Configuration

Cards have configurable settings in the settings table.

Available settings:

card_border_radius
card_shadow
card_header_color
card_footer_color
card_bg_color

Default values:

card_border_radius = 16
card_shadow = true
card_header_color = #f2f2f2
card_footer_color = #f2f2f2
card_bg_color = #fff
Buttons

Dashboard buttons support configurable border radius.

Configuration:

button_border_radius

Default:

10

Example:

<button
    class="rounded-xl px-4 py-2"
>
    Save
</button>
Tables

Dashboard data tables support configurable table styling.

Available settings:

table_striped
table_bordered

Default:

table_striped = true
table_bordered = false
Typography

Dashboard typography can be configured through:

font_family
font_size

Default values:

font_family = Inter
font_size = 14
Breadcrumbs

Breadcrumbs can be enabled or disabled from the application settings.

Configuration:

enable_breadcrumbs

Default:

true

When enabled, dashboard pages can display navigation such as:

Dashboard
    /
Users
    /
Create User
Notifications

Dashboard notifications can be enabled or disabled.

Configuration:

enable_notifications

Default:

true

The starter kit also provides configurable toast notification settings.

See the configuration documentation for notification options.

Animations

Dashboard animations can be controlled using:

enable_animations

Default:

true

Set it to:

false

to disable application animations where this setting is respected.

Footer

The dashboard can display a configurable footer.

Settings:

footer_text
show_footer

Default:

show_footer = true

Example:

footer_text = © 2026 Bishal Starter Kit
Responsive Dashboard

The dashboard is designed to work across different screen sizes.

The main layout should support:

Desktop
Tablet
Mobile

Tailwind CSS responsive utilities can be used when customizing dashboard components.

Example:

<div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
    <!-- Dashboard cards -->
</div>
Adding a Dashboard Widget

A new dashboard widget can be created as a Vue component.

Example structure:

resources/js/
└── Components/
    └── Dashboard/
        └── StatisticsCard.vue

Example:

<script setup>
defineProps({
    title: String,
    value: [String, Number],
});
</script>

<template>
    <div class="rounded-xl bg-white p-6 shadow">
        <h3 class="text-sm text-gray-500">
            {{ title }}
        </h3>

        <p class="mt-2 text-3xl font-bold">
            {{ value }}
        </p>
    </div>
</template>

It can then be used from the dashboard:

<StatisticsCard
    title="Total Users"
    :value="totalUsers"
/>
Passing Data to the Dashboard

Dashboard data can be provided from Laravel through Inertia.

Example controller:

use Inertia\Inertia;

public function index()
{
    return Inertia::render('Dashboard', [
        'totalUsers' => User::count(),
    ]);
}

The Vue page can receive the value:

<script setup>
defineProps({
    totalUsers: Number,
});
</script>

Then display it:

<h2>
    {{ totalUsers }}
</h2>
Dashboard Customization

Developers can customize the dashboard by modifying:

resources/js/Pages/Dashboard.vue

and the reusable components under:

resources/js/Components/

The dashboard styling can be customized using Tailwind CSS.

Dashboard Navigation

Dashboard navigation should be defined through the application's sidebar/navigation system.

Typical navigation structure:

Dashboard
Users
Organizations
Settings
Profile
Logout

The actual navigation items depend on the features installed and enabled in the application.

Authentication

The dashboard is intended for authenticated users.

The starter kit uses Laravel Jetstream for authentication.

Authentication features include the Jetstream authentication workflow configured by the starter kit.

For more information, see:

Authentication

Dashboard Security

Dashboard routes should always use appropriate authentication and authorization middleware.

Example:

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [
        DashboardController::class,
        'index',
    ])->name('dashboard');
});

For features requiring specific permissions, additional authorization should be applied.

Example:

Route::middleware([
    'auth',
    'can:manage-users',
])->group(function () {
    // Protected routes
});
Dashboard Performance

For dashboard statistics that involve large datasets, avoid loading complete models when only counts or aggregates are required.

Prefer:

$totalUsers = User::count();

instead of:

$users = User::all();

$totalUsers = $users->count();

For complex dashboard data, use appropriate database queries and eager loading.

Dashboard Assets

Frontend assets are managed through Vite.

Typical development command:

npm run dev

For production:

npm run build

The dashboard Vue components are compiled through the Vite build process.

Dashboard Development Workflow

When customizing the dashboard:

1. Modify the Vue page
resources/js/Pages/Dashboard.vue
2. Create reusable components
resources/js/Components/Dashboard/
3. Add backend data

Use a controller or route:

return Inertia::render('Dashboard', [
    // dashboard data
]);
4. Run Vite
npm run dev
5. Build for production
npm run build
Dashboard Configuration Summary

The dashboard appearance is primarily controlled through the settings table.

Important settings include:

primary_color
secondary_color
accent_color

background_color
text_color
border_color

sidebar_position
sidebar_width
sidebar_bg_color
sidebar_text_color
sidebar_hover_color
sidebar_hover_text_color
sidebar_active_color
sidebar_icon_color
sidebar_collapsed

navbar_bg_color
navbar_text_color
navbar_border_color
navbar_height

layout_mode
theme_mode
rtl

card_border_radius
card_shadow
card_header_color
card_footer_color
card_bg_color

button_border_radius

table_striped
table_bordered

font_family
font_size

footer_text
show_footer

enable_animations
enable_breadcrumbs
enable_notifications