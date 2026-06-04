<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

interface RoleRepositoryInterface
{
    public function all(): Collection;

    public function findByRoleName(string $roleName): ?Role;
}
