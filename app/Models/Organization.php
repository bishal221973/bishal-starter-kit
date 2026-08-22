<?php

namespace App\Models;

use App\Models\Concerns\HasDataTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use SoftDeletes;
    use HasFactory;

    use HasDataTable;

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

    protected array $dataTableSearchable = [
        'name',
        'slug',
        'email',
        'phone',
        'vat',
    ];

    protected array $dataTableSortable = [
        'name',
        'slug',
        'email',
        'phone',
        'vat',
        'created_at',
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
