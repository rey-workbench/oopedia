<?php

namespace Database\Seeders;

use App\Enums\User\RoleName;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (RoleName::cases() as $role) {
            Role::firstOrCreate(['role_name' => $role]);
        }
    }
}
