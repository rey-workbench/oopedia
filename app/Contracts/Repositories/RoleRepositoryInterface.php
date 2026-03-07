<?php

namespace App\Contracts\Repositories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contract for Role data access.
 */
interface RoleRepositoryInterface
{
    /** @return Collection<int, Role> */
    public function all(): Collection;

    public function find(string $id): ?Role;

    public function findByName(string $name): ?Role;

    /** @return Collection<int, \App\Models\User> */
    public function getUsersByRole(string $roleId): Collection;
}
