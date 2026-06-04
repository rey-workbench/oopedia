<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

final class RoleRepository implements RoleRepositoryInterface
{
    public function all(): Collection
    {
        return Role::select(['id', 'role_name', 'display_name'])->get();
    }

    public function findByRoleName(string $roleName): ?Role
    {
        return Role::where('role_name', $roleName)->first();
    }
}
