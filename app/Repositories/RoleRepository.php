<?php

namespace App\Repositories;

use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{
    public function all()
    {
        return Role::all();
    }

    public function find($id)
    {
        return Role::find($id);
    }

    public function findByName($name)
    {
        return Role::where('role_name', $name)->first();
    }

    public function getUsersByRole($roleId)
    {
        return Role::with('users')
            ->find($roleId)
            ?->users ?? collect();
    }
}
