<?php

namespace App\Repositories;

use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository implements RoleRepositoryInterface
{
    /** @return Collection<int, Role> */
    public function all(): Collection
    {
        return Role::all();
    }

    public function find(int $id): ?Role
    {
        return Role::find($id);
    }

    public function findByName(string $name): ?Role
    {
        return Role::where('role_name', $name)->first();
    }

    /** @return Collection<int, User> */
    public function getUsersByRole(int $roleId): Collection
    {
        return Role::with('users')
            ->find($roleId)
            ?->users ?? collect();
    }
}
