<?php

namespace App\Models;

use App\Traits\DataTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use SoftDeletes;
    use HasFactory;
    use DataTable;

    protected $fillable = [
        'parent_id',

        'name',
        'slug',
        'email',
        'phone',
        'website',

        'logo',
        'favicon',

        'address',
        'city',
        'state',
        'country',
        'postal_code',

        'timezone',
        'currency',
        'locale',

        'is_active',

        'trial_ends_at',
        'subscription_status',
        'subscription_ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',

        'trial_ends_at' => 'datetime',

        'subscription_ends_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | DataTable
    |--------------------------------------------------------------------------
    */

    public function dataTableSearchable(): array
    {
        return [
            'name',
            'slug',
            'email',
            'phone',
  

            // Relationship search
            // 'parent.name',
        ];
    }

    public function dataTableSortable(): array
    {
        return [
            'name',
            'slug',
            'email',
            'phone',
            'country',
            'state',
            'city',
            'created_at',
        ];
    }

    public function dataTableFilters(): array
    {
        return [
            'parent_id',
            'country',
            'state',
            'city',
            'is_active',
            'subscription_status',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot([
                'role',
                'is_active',
                'joined_at',
            ])
            ->withTimestamps();
    }

    public function parent()
    {
        return $this->belongsTo(
            self::class,
            'parent_id'
        );
    }

    public function children()
    {
        return $this->hasMany(
            self::class,
            'parent_id'
        );
    }
}