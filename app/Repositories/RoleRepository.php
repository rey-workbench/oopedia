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
        return Role::all();
    }
}
