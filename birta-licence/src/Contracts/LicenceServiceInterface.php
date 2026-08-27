<?php

namespace Birta\Licence\Contracts;

use Birta\Licence\Models\Licence;

interface LicenceServiceInterface
{
    public function generateKey(): string;

    public function create(array $data = []): Licence;

    public function find(string $key): ?Licence;

    public function forBranch(int $branchId): ?Licence;

    public function validate(
        string $key,
        ?int $branchId = null
    ): bool;

    public function validateBranch(
        int $branchId
    ): bool;

    public function activate(
        string $key,
        ?int $branchId = null
    ): Licence;

    public function revoke(string $key): bool;

    public function suspend(string $key): bool;

    public function reactivate(string $key): bool;

    public function current(
        ?int $branchId = null
    ): ?Licence;

    public function valid(
        ?int $branchId = null
    ): bool;

    public function expired(
        ?int $branchId = null
    ): bool;

    public function delete(string $key): bool;
}