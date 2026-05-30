<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\User\RoleName;
use App\Models\Role;
use App\Models\StudentState;
use App\Models\User;
use App\Rules\Adaptive\Constants\StudentStateSchema;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        // Create mahasiswa users
        $mahasiswaList = [
            [
                'name'     => 'Andi Pratama',
                'email'    => 'andi@mahasiswa.com',
                'password' => 'mhs123',
            ],
            [
                'name'     => 'Budi Santoso',
                'email'    => 'budi@mahasiswa.com',
                'password' => 'mhs123',
            ],
            [
                'name'     => 'Citra Dewi',
                'email'    => 'citra@mahasiswa.com',
                'password' => 'mhs123',
            ],
            [
                'name'     => 'Deni Wijaya',
                'email'    => 'deni@mahasiswa.com',
                'password' => 'mhs123',
            ],
            [
                'name'     => 'Eva Putri',
                'email'    => 'eva@mahasiswa.com',
                'password' => 'mhs123',
            ],
        ];

        $role = Role::where('role_name', RoleName::MAHASISWA)->first();

        foreach ($mahasiswaList as $mahasiswa) {
            $user = User::firstOrCreate(
                ['email' => $mahasiswa['email']],
                [
                    'name'        => $mahasiswa['name'],
                    'password'    => Hash::make($mahasiswa['password']),
                    'role_id'     => $role->id,
                    'is_approved' => true,
                ],
            );

            // Jika ini Andi, beri data state awal untuk testing adaptive engine
            if ($mahasiswa['email'] === 'andi@mahasiswa.com') {
                StudentState::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        StudentStateSchema::XP              => 550,
                        StudentStateSchema::LEVEL           => 'Expert',
                        StudentStateSchema::STREAK          => 5,
                        StudentStateSchema::HINTS_USED      => 0,
                        StudentStateSchema::MAX_STREAK      => 12,
                        'total_answered' => 5,
                        'correct_count' => 4,
                        'last_active_at' => now(),
                    ],
                );
            }
        }
    }
}
