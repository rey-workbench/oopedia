<?php

namespace Database\Seeders;

use App\Enums\User\RoleName;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DosenSeeder extends Seeder
{
    public function run(): void
    {
        // Create dosen users
        $dosenList = [
            [
                'name'     => 'Dr. Ahmad',
                'email'    => 'ahmad@dosen.com',
                'password' => 'dosen123',
            ],
            [
                'name'     => 'Prof. Sarah',
                'email'    => 'sarah@dosen.com',
                'password' => 'dosen123',
            ],
            [
                'name'     => 'Dr. Budi',
                'email'    => 'budi@dosen.com',
                'password' => 'dosen123',
            ],
        ];

        $role = Role::where('role_name', RoleName::DOSEN)->first();

        foreach ($dosenList as $dosen) {
            User::firstOrCreate(
                ['email' => $dosen['email']],
                [
                    'name'        => $dosen['name'],
                    'password'    => Hash::make($dosen['password']),
                    'role_id'     => $role->id,
                    'is_approved' => true,
                ],
            );
        }
    }
}
