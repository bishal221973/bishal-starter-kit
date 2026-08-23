Roles & Permissions

The Bishal Starter Kit uses Spatie Laravel Permission for managing roles and permissions.

Roles and permissions control what authenticated users are allowed to access or perform within the application.

Package

The starter kit uses:

spatie/laravel-permission

The package provides:

Roles
Permissions
User-role assignment
Permission assignment
Role-based authorization
Permission-based authorization
Laravel middleware
Blade directives
Gates and authorization checks
Roles

A role is a collection of permissions assigned to a user.

Examples:

Super Admin
Admin
Manager
Employee
User

For example:

Admin
 ├── users.view
 ├── users.create
 ├── users.update
 └── users.delete

Instead of assigning every permission directly to a user, permissions can be grouped into a role.

Permissions

A permission represents a specific action a user can perform.

Examples:

users.view
users.create
users.update
users.delete

employees.view
employees.create
employees.update
employees.delete

organizations.view
organizations.create
organizations.update
organizations.delete

The exact permissions available in the starter kit depend on the application's registered permissions.

User Model

The User model should use Spatie's HasRoles trait.

<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    // ...
}

The HasRoles trait provides methods for working with roles and permissions.

Creating a Permission

Permissions can be created using:

use Spatie\Permission\Models\Permission;

Permission::create([
    'name' => 'users.view',
]);

Another example:

Permission::create([
    'name' => 'employees.create',
]);
Creating a Role

Roles can be created using:

use Spatie\Permission\Models\Role;

$role = Role::create([
    'name' => 'Admin',
]);
Assigning Permissions to a Role

Permissions can be assigned to a role:

$role->givePermissionTo('users.view');

Multiple permissions can be assigned:

$role->givePermissionTo([
    'users.view',
    'users.create',
    'users.update',
    'users.delete',
]);
Assigning a Role to a User

A role can be assigned to a user:

$user->assignRole('Admin');

Example:

$user->assignRole('Manager');

A user can also have multiple roles:

$user->assignRole([
    'Manager',
    'Employee',
]);
Checking a Role

Check whether a user has a role:

if ($user->hasRole('Admin')) {
    // User is an Admin
}

Multiple roles can be checked:

if ($user->hasAnyRole(['Admin', 'Manager'])) {
    // User has at least one role
}

To require all roles:

if ($user->hasAllRoles(['Admin', 'Manager'])) {
    // User has both roles
}
Checking a Permission

Check whether a user has a permission:

if ($user->can('users.view')) {
    // User can view users
}

You can also use:

if ($user->hasPermissionTo('users.view')) {
    // Permission exists
}
Giving a Permission Directly to a User

Permissions can also be assigned directly to users:

$user->givePermissionTo('users.view');

Multiple permissions:

$user->givePermissionTo([
    'users.view',
    'users.update',
]);

However, role-based permissions are generally preferable when permissions represent a reusable job function.

Removing a Permission

Remove a permission from a role:

$role->revokePermissionTo('users.delete');

Remove a permission from a user:

$user->revokePermissionTo('users.delete');
Removing a Role

Remove a role from a user:

$user->removeRole('Manager');
Sync Roles

To replace the user's existing roles:

$user->syncRoles([
    'Admin',
]);

For example, if a user previously had:

Admin
Manager
Employee

and you execute:

$user->syncRoles(['Manager']);

the user will only have:

Manager
Sync Permissions

Permissions can also be synchronized:

$role->syncPermissions([
    'users.view',
    'users.create',
    'users.update',
]);

This replaces the role's existing permissions with the provided permissions.

Middleware

Spatie Laravel Permission provides middleware for authorization.

For example:

Route::middleware(['permission:users.view'])
    ->get('/users', [UserController::class, 'index']);

A role can also be required:

Route::middleware(['role:Admin'])
    ->get('/admin', [AdminController::class, 'index']);

Multiple roles can be specified:

Route::middleware(['role:Admin|Manager'])
    ->get('/reports', [ReportController::class, 'index']);
Permission-Based Routes

Permission-based authorization is recommended when access is based on a specific application action.

Example:

Route::middleware(['permission:employees.view'])
    ->get('/employees', [EmployeeController::class, 'index']);

For creating employees:

Route::middleware(['permission:employees.create'])
    ->post('/employees', [EmployeeController::class, 'store']);
Controller Authorization

Permissions can also be checked inside controllers.

public function destroy(User $user)
{
    abort_unless(
        auth()->user()->can('users.delete'),
        403
    );

    $user->delete();

    return back();
}
Blade

Spatie permissions can be checked in Blade templates.

Example:

@can('users.view')
    <a href="{{ route('users.index') }}">
        Users
    </a>
@endcan

For roles:

@role('Admin')
    <a href="{{ route('admin.dashboard') }}">
        Administration
    </a>
@endrole
Vue / Inertia

For an Inertia application, permissions can be shared with Vue through Inertia shared props.

Example:

return [
    'auth' => [
        'user' => auth()->user(),
        'permissions' => auth()->user()
            ->getAllPermissions()
            ->pluck('name')
            ->values(),
        'roles' => auth()->user()
            ->getRoleNames()
            ->values(),
    ],
];

Vue can then check permissions.

Example:

const can = (permission) => {
    return page.props.auth.permissions.includes(permission);
};

Usage:

<button v-if="can('users.create')">
    Create User
</button>
Navigation and Permissions

Permissions can also be used to control sidebar menu items.

Example:

{
    title: 'Users',
    route: 'users.index',
    permission: 'users.view'
}

Then only users with:

users.view

can see the menu item.

This prevents unauthorized users from seeing features they cannot access.

Super Admin

A Super Admin can be configured to bypass normal permission checks depending on the Spatie Permission configuration and the application's authorization implementation.

For example, the application may define a Gate:

Gate::before(function ($user, $ability) {
    return $user->hasRole('Super Admin')
        ? true
        : null;
});

This allows Super Admin users to perform all abilities.

Permission Naming Convention

A consistent permission naming convention is recommended.

The starter kit can use:

resource.action

Examples:

users.view
users.create
users.update
users.delete

employees.view
employees.create
employees.update
employees.delete

organizations.view
organizations.create
organizations.update
organizations.delete

roles.view
roles.create
roles.update
roles.delete

permissions.view
permissions.create
permissions.update
permissions.delete

This makes permissions easier to understand and manage.

Example Role Structure

A typical application could have:

Super Admin
    ↓
All permissions

Admin
    ↓
users.*
employees.*
organizations.*
settings.*

Manager
    ↓
employees.view
employees.create
employees.update

Employee
    ↓
employees.view
Creating Roles and Permissions with a Seeder

A seeder can be used to initialize roles and permissions.

Example:

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

$permissions = [
    'users.view',
    'users.create',
    'users.update',
    'users.delete',

    'employees.view',
    'employees.create',
    'employees.update',
    'employees.delete',

    'organizations.view',
    'organizations.create',
    'organizations.update',
    'organizations.delete',
];

foreach ($permissions as $permission) {
    Permission::firstOrCreate([
        'name' => $permission,
    ]);
}

Create an Admin role:

$admin = Role::firstOrCreate([
    'name' => 'Admin',
]);

$admin->syncPermissions($permissions);
Assigning the Default Role

After creating a user:

$user->assignRole('Employee');

For an administrator:

$user->assignRole('Admin');
Retrieving User Roles

Get all roles:

$user->getRoleNames();

Example result:

[
    'Admin',
    'Manager',
]
Retrieving User Permissions

Get permissions assigned to the user:

$user->getAllPermissions();

Get only permission names:

$user->getAllPermissions()
    ->pluck('name');
Retrieving Role Permissions
$role->permissions;

Or:

$role->getPermissionNames();

Example:

[
    'users.view',
    'users.create',
    'users.update',
]
Role Management Flow

The typical administration flow is:

Create Permission
       ↓
Create Role
       ↓
Assign Permissions to Role
       ↓
Assign Role to User
       ↓
User accesses application
       ↓
Permission Check
       ↓
Allow / Deny
Security

Roles and permissions should be checked on the server side.

Hiding a button in Vue:

<button v-if="can('users.delete')">
    Delete
</button>

is useful for the user interface, but it is not sufficient security.

The backend route or controller should also check the permission:

Route::middleware(['permission:users.delete'])
    ->delete('/users/{user}', [UserController::class, 'destroy']);

This prevents users from bypassing the frontend and directly calling the endpoint.

Common Permission Checks
Check permission
$user->can('users.view');
Check role
$user->hasRole('Admin');
Assign role
$user->assignRole('Admin');
Remove role
$user->removeRole('Admin');
Assign permission
$user->givePermissionTo('users.create');
Revoke permission
$user->revokePermissionTo('users.create');
Sync roles
$user->syncRoles(['Manager']);
Sync permissions
$role->syncPermissions([
    'users.view',
    'users.update',
]);
Summary

The Bishal Starter Kit uses Spatie Laravel Permission to provide authorization.

                    User
                     │
                     ↓
                   Roles
                     │
                     ↓
                Permissions
                     │
                     ↓
             Authorization
                ↙       ↘
             Allow      Deny

The system supports:

Roles
Permissions
User-role assignment
Direct user permissions
Role permissions
Route middleware
Controller authorization
Blade authorization
Inertia/Vue permission checks
Super Admin authorization
Permission-based navigation

For security, permissions should always be enforced on the Laravel backend, while Vue/Inertia permission checks should primarily be used to control the user interface.