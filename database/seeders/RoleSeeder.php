<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create or ensure roles exist with new structure
        Role::updateOrCreate(['role_name' => 'superadmin']);
        Role::updateOrCreate(['role_name' => 'dosen']);
        Role::updateOrCreate(['role_name' => 'mahasiswa']);
    }
}
