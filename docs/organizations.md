Organizations

The Bishal Starter Kit supports organizations and branches through the organizations table.

An organization represents a company, business, institution, or other tenant within the application. Organizations can also have parent/child relationships to support multiple branches.

Organization Table

Organizations are stored in:

organizations

The table contains:

id
parent_id
name
slug
email
phone
website
vat
logo
favicon
address
city
state
country
postal_code
is_active
trial_ends_at
subscription_status
subscription_ends_at
deleted_at
created_at
updated_at
Creating an Organization

An organization requires a name and a unique slug.

Example:

use App\Models\Organization;

$organization = Organization::create([
    'name' => 'Bishal Websoft',
    'slug' => 'bishal-websoft',
    'email' => 'info@example.com',
    'phone' => '+977-9800000000',
    'website' => 'https://example.com',
    'country' => 'Nepal',
    'is_active' => true,
]);
Organization Name

The name field contains the organization's display name.

$table->string('name');

Example:

Bishal Websoft Pvt. Ltd.
Organization Slug

The slug is a unique identifier for the organization.

$table->string('slug')->unique();

Example:

bishal-websoft

Because the slug is unique, two organizations cannot use the same slug.

Example:

$organization->slug;
Parent Organizations and Branches

The starter kit supports organization branches using parent_id.

$table->foreignId('parent_id')
    ->nullable()
    ->constrained('organizations')
    ->nullOnDelete();

An organization without a parent is a main organization.

Bishal Websoft
├── Nepal Branch
├── India Branch
└── USA Branch

Each branch can reference the main organization through parent_id.

Creating a Branch

Example:

$branch = Organization::create([
    'parent_id' => $organization->id,
    'name' => 'Nepal Branch',
    'slug' => 'nepal-branch',
    'country' => 'Nepal',
    'is_active' => true,
]);
Organization Relationships

The Organization model can define the parent relationship:

public function parent()
{
    return $this->belongsTo(
        Organization::class,
        'parent_id'
    );
}

Branches can be retrieved with:

public function children()
{
    return $this->hasMany(
        Organization::class,
        'parent_id'
    );
}

Then:

$organization->children;

returns the organization's branches.

Contact Information

Organizations support the following contact fields:

email
phone
website
vat

Example:

$organization->update([
    'email' => 'info@example.com',
    'phone' => '+977-9800000000',
    'website' => 'https://example.com',
    'vat' => '123456789',
]);

All of these fields are nullable.

Organization Branding

The organization supports:

logo
favicon

Migration:

$table->string('logo')->nullable();
$table->string('favicon')->nullable();

Example:

$organization->update([
    'logo' => 'organizations/logo.png',
    'favicon' => 'organizations/favicon.ico',
]);

The stored values can represent paths to uploaded organization assets.

Address

Organizations can store:

address
city
state
country
postal_code

Example:

$organization->update([
    'address' => 'New Road',
    'city' => 'Nepalgunj',
    'state' => 'Lumbini Province',
    'country' => 'Nepal',
    'postal_code' => '21900',
]);
Organization Status

The is_active field controls whether an organization is active.

$table->boolean('is_active')->default(true);

An active organization:

$organization->is_active === true;

An inactive organization:

$organization->is_active === false;
Trial Period

Organizations can have a trial period through:

trial_ends_at

Migration:

$table->timestamp('trial_ends_at')->nullable();

Example:

$organization->update([
    'trial_ends_at' => now()->addDays(30),
]);

You can check whether the trial has expired:

if (
    $organization->trial_ends_at &&
    now()->greaterThan($organization->trial_ends_at)
) {
    // Trial expired
}
Subscription

Organizations have subscription information through:

subscription_status
subscription_ends_at

The default subscription status is:

trial

Migration:

$table->string('subscription_status')
    ->default('trial');

$table->timestamp('subscription_ends_at')
    ->nullable();

Example:

$organization->update([
    'subscription_status' => 'active',
    'subscription_ends_at' => now()->addYear(),
]);

The starter kit stores the subscription state but the available subscription statuses depend on the application's subscription implementation.

Soft Deletes

Organizations use Laravel soft deletes:

$table->softDeletes();

This means deleting an organization does not immediately remove its database record.

Example:

$organization->delete();

The record remains in the database with a deleted_at timestamp.

To retrieve deleted organizations:

Organization::withTrashed()->get();

To restore an organization:

$organization->restore();
Organization Model

A typical Organization model can be:

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Organization extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'email',
        'phone',
        'website',
        'vat',
        'logo',
        'favicon',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'is_active',
        'trial_ends_at',
        'subscription_status',
        'subscription_ends_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'trial_ends_at' => 'datetime',
            'subscription_ends_at' => 'datetime',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            Organization::class,
            'parent_id'
        );
    }

    public function children(): HasMany
    {
        return $this->hasMany(
            Organization::class,
            'parent_id'
        );
    }
}
Organization and Users

Users are connected to organizations through the organization_user pivot table.

The relationship allows a user to belong to an organization with additional employee information.

User
 ↓
organization_user
 ↓
Organization

The pivot table contains fields such as:

organization_id
user_id
employee_code
gender
date_of_birth
personal_email
personal_phone
department
designation
joined_at
salary
employee_type
is_active
can_login

See the Employees documentation for details about employee organization membership.

Organization-Specific Configuration

Several configuration tables contain:

organization_id

This allows configuration to be associated with a specific organization.

For example:

Organization
   │
   ├── Configuration
   ├── Settings
   └── Mail Settings

This allows organizations to have their own:

Application configuration
Theme settings
Mail settings
Notification settings
Security settings
Organization Creation Example

A complete organization creation example:

$organization = Organization::create([
    'name' => 'Bishal Websoft Pvt. Ltd.',
    'slug' => 'bishal-websoft',
    'email' => 'info@example.com',
    'phone' => '+977-9800000000',
    'website' => 'https://example.com',
    'vat' => '123456789',
    'address' => 'Nepalgunj',
    'city' => 'Nepalgunj',
    'state' => 'Lumbini Province',
    'country' => 'Nepal',
    'postal_code' => '21900',
    'is_active' => true,
    'trial_ends_at' => now()->addDays(30),
    'subscription_status' => 'trial',
]);
Organization Creation Validation

A typical validation request can use:

$request->validate([
    'name' => [
        'required',
        'string',
        'max:255',
    ],

    'slug' => [
        'required',
        'string',
        'max:255',
        'unique:organizations,slug',
    ],

    'email' => [
        'nullable',
        'email',
        'max:255',
    ],

    'phone' => [
        'nullable',
        'string',
        'max:255',
    ],

    'website' => [
        'nullable',
        'url',
        'max:255',
    ],

    'vat' => [
        'nullable',
        'string',
        'max:255',
    ],

    'country' => [
        'nullable',
        'string',
        'max:255',
    ],

    'postal_code' => [
        'nullable',
        'string',
        'max:255',
    ],
]);
Organization Hierarchy

The organization structure can be represented as:

Main Organization
│
├── Branch 1
│   ├── Employees
│   └── Configuration
│
├── Branch 2
│   ├── Employees
│   └── Configuration
│
└── Branch 3
    ├── Employees
    └── Configuration

The parent_id field provides the parent-child relationship.

Organization Status Check

Before performing organization-specific operations, the application can check:

if (! $organization->is_active) {
    abort(403, 'Organization is inactive.');
}

For subscription-based applications, the subscription status can also be checked:

if ($organization->subscription_status !== 'active') {
    // Handle inactive subscription
}

The exact subscription rules depend on the application's business logic.

Database Structure

The organization migration is:

Schema::create('organizations', function (Blueprint $table) {
    $table->id();

    $table->foreignId('parent_id')
        ->nullable()
        ->constrained('organizations')
        ->nullOnDelete();

    $table->string('name');
    $table->string('slug')->unique();

    $table->string('email')->nullable();
    $table->string('phone')->nullable();
    $table->string('website')->nullable();
    $table->string('vat')->nullable();

    $table->string('logo')->nullable();
    $table->string('favicon')->nullable();

    $table->text('address')->nullable();
    $table->string('city')->nullable();
    $table->string('state')->nullable();
    $table->string('country')->nullable();
    $table->string('postal_code')->nullable();

    $table->boolean('is_active')->default(true);

    $table->timestamp('trial_ends_at')->nullable();

    $table->string('subscription_status')
        ->default('trial');

    $table->timestamp('subscription_ends_at')
        ->nullable();

    $table->softDeletes();
    $table->timestamps();
});
Summary

Organizations are a core part of the Bishal Starter Kit's multi-organization structure.

Organization
│
├── Basic Information
├── Contact Information
├── Branding
├── Address
├── Status
├── Trial
├── Subscription
│
├── Users / Employees
├── Configuration
├── Settings
└── Mail Settings

The parent_id relationship also allows organizations to support branches:

Main Organization
       ↓
    Branches
       ↓
    Employees

This structure allows the starter kit to support both standalone organizations and applications with multiple branches.