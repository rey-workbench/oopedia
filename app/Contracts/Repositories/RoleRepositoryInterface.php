<?php

namespace App\Contracts\Repositories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

interface RoleRepositoryInterface
{
    public function all(): Collection;

    public function find(string $id): ?Role;

    public function getUsersByRole(string $roleId): Collection;
}
