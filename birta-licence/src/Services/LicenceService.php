<?php

namespace Birta\Licence\Services;

use Birta\Licence\Contracts\LicenceServiceInterface;
use Birta\Licence\Exceptions\ExpiredLicenceException;
use Birta\Licence\Exceptions\InvalidLicenceException;
use Birta\Licence\Exceptions\LicenceNotFoundException;
use Birta\Licence\Models\Licence;

class LicenceService implements LicenceServiceInterface
{
    public function __construct(
        protected LicenceGenerator $generator
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Key
    |--------------------------------------------------------------------------
    */

    public function generateKey(): string
    {
        do {
            $key = $this->generator->generate();
        } while (
            Licence::where('key', $key)->exists()
        );

        return $key;
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(array $data = []): Licence
    {
        $expiresAt = $data['expires_at'] ?? null;

        if (
            !$expiresAt &&
            config('licence.duration')
        ) {
            $expiresAt = now()->addDays(
                config('licence.duration')
            );
        }

        return Licence::create([
            'branch_id' => $data['branch_id'] ?? null,

            'key' => $data['key']
                ?? $this->generateKey(),

            'status' => $data['status']
                ?? 'active',

            'max_users' => $data['max_users']
                ?? null,

            'issued_at' => $data['issued_at']
                ?? now(),

            'starts_at' => $data['starts_at']
                ?? now(),

            'expires_at' => $expiresAt,

            'activated_at' => $data['activated_at']
                ?? null,

            'revoked_at' => $data['revoked_at']
                ?? null,

            'metadata' => $data['metadata']
                ?? [],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Find By Key
    |--------------------------------------------------------------------------
    */

    public function find(string $key): ?Licence
    {
        return Licence::where(
            'key',
            $key
        )->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Find By Branch
    |--------------------------------------------------------------------------
    */

    public function forBranch(
        int $branchId
    ): ?Licence {
        return Licence::where(
            'branch_id',
            $branchId
        )
            ->latest('id')
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Validate
    |--------------------------------------------------------------------------
    */

    public function validate(
        string $key,
        ?int $branchId = null
    ): bool {
        $licence = $this->find($key);

        if (!$licence) {
            return false;
        }

        if (!$licence->isValid()) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Branch Check
        |--------------------------------------------------------------------------
        */

        if (
            config(
                'licence.allow_multiple_branches'
            ) &&
            $branchId !== null
        ) {
            if (
                $licence->branch_id !== null &&
                (int) $licence->branch_id !==
                (int) $branchId
            ) {
                return false;
            }
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Branch
    |--------------------------------------------------------------------------
    */

    public function validateBranch(
        int $branchId
    ): bool {
        $licence = $this->forBranch(
            $branchId
        );

        if (!$licence) {
            return false;
        }

        return $licence->isValid();
    }

    /*
    |--------------------------------------------------------------------------
    | Activate
    |--------------------------------------------------------------------------
    */

    public function activate(
        string $key,
        ?int $branchId = null
    ): Licence {
        $licence = $this->find($key);

        if (!$licence) {
            throw new LicenceNotFoundException(
                "Licence [{$key}] was not found."
            );
        }

        if ($licence->isRevoked()) {
            throw new InvalidLicenceException(
                'This licence has been revoked.'
            );
        }

        if ($licence->isExpired()) {
            throw new ExpiredLicenceException(
                'This licence has expired.'
            );
        }

        if (
            config(
                'licence.allow_multiple_branches'
            ) &&
            $branchId !== null &&
            $licence->branch_id !== null &&
            (int) $licence->branch_id !==
            (int) $branchId
        ) {
            throw new InvalidLicenceException(
                'This licence does not belong to this branch.'
            );
        }

        $licence->update([
            'status' => 'active',
            'activated_at' => now(),
        ]);

        return $licence->fresh();
    }

    /*
    |--------------------------------------------------------------------------
    | Revoke
    |--------------------------------------------------------------------------
    */

    public function revoke(
        string $key
    ): bool {
        $licence = $this->findOrFail($key);

        return $licence->update([
            'status' => 'revoked',
            'revoked_at' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Suspend
    |--------------------------------------------------------------------------
    */

    public function suspend(
        string $key
    ): bool {
        $licence = $this->findOrFail($key);

        return $licence->update([
            'status' => 'suspended',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Reactivate
    |--------------------------------------------------------------------------
    */

    public function reactivate(
        string $key
    ): bool {
        $licence = $this->findOrFail($key);

        if ($licence->isExpired()) {
            throw new ExpiredLicenceException(
                'Cannot reactivate an expired licence.'
            );
        }

        return $licence->update([
            'status' => 'active',
            'revoked_at' => null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Current Licence
    |--------------------------------------------------------------------------
    */

    public function current(
        ?int $branchId = null
    ): ?Licence {
        $branchId ??= $this->getCurrentBranchId();

        if (!$branchId) {
            return null;
        }

        return $this->forBranch(
            $branchId
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Valid
    |--------------------------------------------------------------------------
    */

    public function valid(
        ?int $branchId = null
    ): bool {
        $licence = $this->current(
            $branchId
        );

        if (!$licence) {
            return false;
        }

        return $licence->isValid();
    }

    /*
    |--------------------------------------------------------------------------
    | Expired
    |--------------------------------------------------------------------------
    */

    public function expired(
        ?int $branchId = null
    ): bool {
        $licence = $this->current(
            $branchId
        );

        if (!$licence) {
            return false;
        }

        return $licence->isExpired();
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(
        string $key
    ): bool {
        $licence = $this->findOrFail($key);

        return (bool) $licence->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | Find Or Fail
    |--------------------------------------------------------------------------
    */

    protected function findOrFail(
        string $key
    ): Licence {
        $licence = $this->find($key);

        if (!$licence) {
            throw new LicenceNotFoundException(
                "Licence [{$key}] was not found."
            );
        }

        return $licence;
    }

    /*
    |--------------------------------------------------------------------------
    | Current Branch
    |--------------------------------------------------------------------------
    */

    protected function getCurrentBranchId(): ?int
    {
        return auth()->user()?->branch_id
            ?? session('branch_id');
    }
}