<?php

namespace Birta\Licence\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Licence extends Model
{
    protected $table = 'licences';

    protected $fillable = [
        'branch_id',
        'key',
        'status',
        'max_users',
        'issued_at',
        'starts_at',
        'expires_at',
        'activated_at',
        'revoked_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'activated_at' => 'datetime',
            'revoked_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null
            && $this->expires_at->isPast();
    }

    public function isRevoked(): bool
    {
        return $this->status === 'revoked';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function hasStarted(): bool
    {
        return $this->starts_at === null
            || $this->starts_at->isPast();
    }

    public function isValid(): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        if (!$this->hasStarted()) {
            return false;
        }

        if ($this->isExpired()) {
            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeValid(Builder $query): Builder
    {
        return $query
            ->where('status', 'active')
            ->where(function (Builder $query) {
                $query
                    ->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $query) {
                $query
                    ->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Remaining Days
    |--------------------------------------------------------------------------
    */

    public function remainingDays(): ?int
    {
        if (!$this->expires_at) {
            return null;
        }

        return max(
            0,
            (int) now()->diffInDays(
                $this->expires_at,
                false
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Metadata
    |--------------------------------------------------------------------------
    */

    public function getMetadata(
        string $key,
        mixed $default = null
    ): mixed {
        return data_get(
            $this->metadata,
            $key,
            $default
        );
    }

    public function setMetadata(
        string $key,
        mixed $value
    ): self {
        $metadata = $this->metadata ?? [];

        data_set(
            $metadata,
            $key,
            $value
        );

        $this->metadata = $metadata;

        return $this;
    }
}


// php artisan vendor:publish --tag=licence-config
// php artisan vendor:publish --tag=licence-migrations
// composer require birta/birta-licence:@dev


// use Birta\Licence\Facades\Licence;
// 3. Create the licence

// If you have a branch with ID 1:

// $licence = Licence::create([
//     'branch_id' => 1,
//     'max_users' => 50,
//     'starts_at' => now(),
//     'expires_at' => now()->addYear(),
// ]);

// Licence::valid($licence->key);
// $licence->isValid();