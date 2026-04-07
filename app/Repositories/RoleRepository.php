<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

final class RoleRepository implements RoleRepositoryInterface
{
    /** @return Collection<string, Role> */
    public function all(): Collection
    {
        return Role::all();
    }

    public function find(string $id): ?Role
    {
        return Role::find($id);
    }

    public function findByName(string $name): ?Role
    {
        return Role::where('role_name', '=', $name)->first();
    }

    /** @return Collection<string, User> */
    public function getUsersByRole(string $roleId): Collection
    {
        return Role::with('users')
            ->find($roleId)
            ?->users ?? new Collection();
    }
}
