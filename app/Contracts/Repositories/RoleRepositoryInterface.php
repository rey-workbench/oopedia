<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface RoleRepositoryInterface
{
    public function all(): Collection;
}
