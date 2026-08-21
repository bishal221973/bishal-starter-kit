<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use SoftDeletes;

    protected $fillable = [
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
}
