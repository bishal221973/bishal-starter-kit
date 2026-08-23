Routes

The Bishal Starter Kit uses Laravel routing to define application URLs, controllers, middleware, names, and authorization rules.

Routes are generally defined in:

routes/
├── web.php
├── api.php
├── console.php
└── channels.php

The exact route files available depend on the application configuration.

Basic Route

A basic Laravel route can be defined in routes/web.php:

use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('dashboard');
});
Named Routes

The starter kit uses named routes so that URLs can be referenced without hardcoding them.

Route::get('/employees', [
    EmployeeController::class,
    'index',
])->name('employees.index');

Use the route name in Laravel:

route('employees.index');

In Vue/Inertia applications, route names can also be generated through Ziggy.

Example:

route('employees.index')
HTTP Methods

Laravel supports the common HTTP methods:

Route::get('/users', ...);

Route::post('/users', ...);

Route::put('/users/{user}', ...);

Route::patch('/users/{user}', ...);

Route::delete('/users/{user}', ...);

A resource can therefore follow the standard CRUD structure:

GET       /users
POST      /users
GET       /users/{user}
PUT       /users/{user}
DELETE    /users/{user}
Controllers

Routes can point to controller methods.

use App\Http\Controllers\EmployeeController;

Route::get('/employees', [
    EmployeeController::class,
    'index',
])->name('employees.index');

Create:

Route::post('/employees', [
    EmployeeController::class,
    'store',
])->name('employees.store');

Update:

Route::put('/employees/{employee}', [
    EmployeeController::class,
    'update',
])->name('employees.update');

Delete:

Route::delete('/employees/{employee}', [
    EmployeeController::class,
    'destroy',
])->name('employees.destroy');
Resource Routes

Laravel resource routes can be used for CRUD functionality.

Route::resource(
    'employees',
    EmployeeController::class
);

This automatically creates routes such as:

GET       /employees
GET       /employees/create
POST      /employees
GET       /employees/{employee}
GET       /employees/{employee}/edit
PUT/PATCH /employees/{employee}
DELETE    /employees/{employee}
Authentication Middleware

Authenticated pages should be protected using Laravel's auth middleware.

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [
        DashboardController::class,
        'index',
    ])->name('dashboard');

});

Only authenticated users can access these routes.

Guest Middleware

Pages that should only be available to unauthenticated users can use the guest middleware.

Route::middleware('guest')->group(function () {

    Route::get('/login', [
        LoginController::class,
        'create',
    ]);

});

The starter kit uses Laravel Jetstream for authentication.

Permission Middleware

The starter kit uses Spatie Laravel Permission for authorization.

A route can require a permission:

Route::middleware(['permission:employees.view'])
    ->get('/employees', [
        EmployeeController::class,
        'index',
    ])
    ->name('employees.index');

Another example:

Route::middleware(['permission:employees.create'])
    ->post('/employees', [
        EmployeeController::class,
        'store',
    ])
    ->name('employees.store');
Role Middleware

Routes can also require a specific role.

Route::middleware(['role:Admin'])
    ->group(function () {

        Route::get('/admin', [
            AdminController::class,
            'index',
        ])->name('admin.index');

    });

Multiple roles can be allowed:

Route::middleware([
    'role:Admin|Manager'
])->group(function () {

    // Routes

});
Authentication + Permission

For protected application functionality, both authentication and permission middleware can be used.

Route::middleware([
    'auth',
    'permission:employees.view',
])->group(function () {

    Route::get('/employees', [
        EmployeeController::class,
        'index',
    ])->name('employees.index');

});

The request must satisfy both:

Authenticated
     +
Permission
     ↓
Access Granted
Route Groups

Related routes can be grouped together.

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [
        DashboardController::class,
        'index',
    ])->name('dashboard');

    Route::resource(
        'employees',
        EmployeeController::class
    );

});

This prevents repeating the same middleware for every route.

Prefix

Routes can be grouped using a URL prefix.

Route::prefix('admin')
    ->middleware('auth')
    ->group(function () {

        Route::get('/dashboard', [
            AdminController::class,
            'dashboard',
        ])->name('admin.dashboard');

    });

The resulting URL is:

/admin/dashboard
Name Prefix

Route names can also be grouped.

Route::name('admin.')
    ->prefix('admin')
    ->middleware('auth')
    ->group(function () {

        Route::get('/dashboard', [
            AdminController::class,
            'dashboard',
        ])->name('dashboard');

    });

The resulting route name is:

admin.dashboard
Route Model Binding

Laravel supports automatic model binding.

Route::get('/employees/{employee}', [
    EmployeeController::class,
    'show',
])->name('employees.show');

Controller:

public function show(Employee $employee)
{
    return Inertia::render(
        'Employees/Show',
        [
            'employee' => $employee,
        ]
    );
}

Laravel automatically resolves the {employee} parameter into an Employee model.

Inertia Routes

The starter kit uses Inertia.js with Vue 3.

A controller can return an Inertia page:

use Inertia\Inertia;

public function index()
{
    return Inertia::render(
        'Employees/Index'
    );
}

Route:

Route::get('/employees', [
    EmployeeController::class,
    'index',
])->name('employees.index');

The Vue page is typically located under:

resources/js/Pages/

For example:

resources/js/Pages/Employees/Index.vue
Route Parameters

Routes can contain parameters:

Route::get('/employees/{employee}', [
    EmployeeController::class,
    'show',
]);

The {employee} value is available to the controller.

For example:

/employees/10

The value is:

employee = 10
Optional Parameters

Laravel supports optional parameters:

Route::get('/users/{user?}', [
    UserController::class,
    'show',
]);

The parameter can be omitted.

Route Constraints

Parameters can be constrained.

Example:

Route::get('/users/{id}', [
    UserController::class,
    'show',
])->whereNumber('id');

The id parameter must contain a number.

Another example:

Route::get('/users/{id}', [
    UserController::class,
    'show',
])->where('id', '[0-9]+');
Redirect Routes

Laravel can redirect one URL to another.

Route::redirect(
    '/home',
    '/dashboard'
);

Permanent redirect:

Route::redirect(
    '/old-page',
    '/new-page',
    301
);
Dashboard Route

The dashboard can be protected with authentication:

Route::middleware('auth')
    ->get('/dashboard', [
        DashboardController::class,
        'index',
    ])
    ->name('dashboard');

The dashboard can then be accessed using:

route('dashboard');
Route Authorization

A route should not rely only on frontend visibility.

For example, hiding a button in Vue:

<button v-if="can('employees.delete')">
    Delete
</button>

does not provide backend security.

The Laravel route should also protect the operation:

Route::middleware([
    'auth',
    'permission:employees.delete',
])->delete(
    '/employees/{employee}',
    [EmployeeController::class, 'destroy']
)->name('employees.destroy');

This prevents unauthorized users from directly calling the endpoint.

Route List

Laravel provides the Artisan command:

php artisan route:list

For more detailed information:

php artisan route:list -v

To show only application routes matching a specific URI:

php artisan route:list --path=employees

To filter by name:

php artisan route:list --name=employees
Searching Routes

The starter kit's AI support system includes a SearchRoutes tool for inspecting application routes.

It can search routes by:

URI
Route name
Controller/action
Feature

For example, a search for:

employees

can find routes such as:

GET       employees
POST      employees
GET       employees/{employee}
PUT       employees/{employee}
DELETE    employees/{employee}

This is useful when determining which route handles a particular feature.

Route Cache

For production environments, Laravel routes can be cached:

php artisan route:cache

To clear the route cache:

php artisan route:clear

After changing routes in production, rebuild the route cache if your deployment process uses route caching.

Common Route Structure

A typical starter kit route structure can look like:

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [
        DashboardController::class,
        'index',
    ])->name('dashboard');

    // Employees
    Route::middleware('permission:employees.view')
        ->get('/employees', [
            EmployeeController::class,
            'index',
        ])
        ->name('employees.index');

    // Organizations
    Route::middleware('permission:organizations.view')
        ->get('/organizations', [
            OrganizationController::class,
            'index',
        ])
        ->name('organizations.index');

});
Recommended Route Organization

For larger applications, keep routes organized by feature.

Example:

routes/
├── web.php
├── api.php
├── console.php
└── channels.php

Application routes can be grouped by:

Authentication
Dashboard
Users
Employees
Organizations
Roles
Permissions
Settings
Configurations
Notifications
Reports
Route Naming Convention

Use consistent resource-based names:

employees.index
employees.create
employees.store
employees.show
employees.edit
employees.update
employees.destroy

For organizations:

organizations.index
organizations.create
organizations.store
organizations.show
organizations.edit
organizations.update
organizations.destroy

This makes route references predictable throughout the Laravel and Vue/Inertia application.

Summary

The Bishal Starter Kit uses Laravel's routing system together with Jetstream, Inertia.js, Vue 3, and Spatie Laravel Permission.

The recommended request flow is:

Browser
   ↓
Laravel Route
   ↓
Authentication Middleware
   ↓
Permission / Role Middleware
   ↓
Controller
   ↓
Business Logic
   ↓
Inertia Response
   ↓
Vue Page

For protected features, always enforce authorization on the Laravel backend:

Route::middleware([
    'auth',
    'permission:employees.view',
])->get('/employees', [
    EmployeeController::class,
    'index',
]);

Frontend permission checks should be treated as a UI convenience, while Laravel middleware remains the actual security boundary.