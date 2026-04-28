<?php

namespace Database\Seeders;

use App\Enums\Lms\StudentLevel;
use App\Enums\User\RoleName;
use App\Models\Role;
use App\Models\StudentState;
use App\Models\User;
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

            // Jika ini Andi, beri data sertifikat contoh
            if ($mahasiswa['email'] === 'andi@mahasiswa.com') {
                StudentState::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'xp'             => 550,
                        'level'          => StudentLevel::AHLI->value,
                        'streak'         => 5,
                        'max_streak'     => 12,
                        'badges'         => ['fast_learner', 'logic_master', 'module_complete'],
                        'learning_style' => 'visual',
                        'certifications' => [
                            '01kpwk01et585ktfn1672hzsbq' => 'gold',
                            '01kpwk01ew9f7t6ybvtang3fap' => 'silver',
                            '01kpwk02cxd12cekz09a1wm1jj' => 'bronze',
                        ],
                        'unlocked_modules' => ['1', '2', '3'],
                        'last_active_at'   => now(),
                    ],
                );
            }
        }
    }
}
