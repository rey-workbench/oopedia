<?php

namespace Database\Seeders;

use App\Enums\User\RoleName;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperadminSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('role_name', RoleName::SUPERADMIN)->first();

        // Create superadmin user
        User::firstOrCreate(
            ['email' => 'superadmin@admin.com'],
            [
                'name'        => 'Super Admin',
                'password'    => Hash::make('superadmin123'),
                'role_id'     => $role->id,
                'is_approved' => true,
            ],
        );
    }
}
