<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Lms\StudentLevel;
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
                        StudentStateSchema::LEVEL           => StudentLevel::AHLI->value,
                        StudentStateSchema::STREAK          => 5,
                        StudentStateSchema::MAX_STREAK      => 12,
                        'badges'                            => ['fast_learner', 'logic_master', 'module_complete'],
                        StudentStateSchema::ACCURACY        => 85.5,
                        StudentStateSchema::SESSION_HISTORY => [80.0, 90.0, 85.0, 85.5, 90.0],
                        StudentStateSchema::CURRENT_SESSION => [
                            'correct'    => 4,
                            'total'      => 5,
                            'hints'      => 0,
                            'time_spent' => 120,
                        ],
                        StudentStateSchema::PERFORMANCE_METRICS => [
                            'trend'          => 'up',
                            'speed'          => 'normal',
                            'stagnant_count' => 0,
                        ],
                        StudentStateSchema::ADAPTIVE_STATE => [
                            'consecutive_correct' => 4,
                            'help_count_session'  => 0,
                        ],
                        'last_active_at' => now(),
                    ],
                );
            }
        }
    }
}
