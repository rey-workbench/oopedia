<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\User\RoleName;
use App\Models\Role;
use App\Models\StudentState;
use App\Models\User;
use App\Rules\Adaptive\Constants\StudentStateSchema;
use DB;
use Illuminate\Database\Seeder;
use App\Enums\Lms\StudentLevel;
use Illuminate\Support\Facades\Hash;

class TestRulesSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('role_name', RoleName::MAHASISWA)->first();

        if (!$role) {
            $this->command->error("Role MAHASISWA not found. Please run RoleSeeder first.");
            return;
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::where('email', 'like', 'r%@tester.com')->delete();
        DB::table('adaptive_execution_logs')->truncate();
        DB::table('quiz_attempts')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $testers = [
            // R00: Progres Terjaga (Fallback - Normal)
            [
                'name'     => 'Tester R00 (Progres Terjaga)',
                'email'    => 'r00@tester.com',
                'password' => 'mhs123',
                'state'    => [
                    'total_answered' => 20,
                    'correct_count'  => 15,
                    StudentStateSchema::ACCURACY => 75.0, // G18
                    StudentStateSchema::HINTS_USED => 1, // G10
                    StudentStateSchema::LEVEL => StudentLevel::PEMULA->value,
                    StudentStateSchema::PERFORMANCE_METRICS => [
                        'trend' => 'stable', // G06
                        'last_response_time' => 30, // G13
                        'last_result' => true,
                    ]
                ]
            ],
            // R01: Analisa Performa Optimal (Diagnosis Only, Not R06 because hint > 0)
            [
                'name'     => 'Tester R01 (Performa Optimal)',
                'email'    => 'r01@tester.com',
                'password' => 'mhs123',
                'state'    => [
                    'total_answered' => 20,
                    'correct_count'  => 19,
                    StudentStateSchema::ACCURACY => 95.0, // G17
                    StudentStateSchema::HINTS_USED => 1, // G10 (Prevents G20 -> prevents R06)
                    StudentStateSchema::LEVEL => StudentLevel::MENENGAH->value,
                    StudentStateSchema::PERFORMANCE_METRICS => [
                        'trend' => 'up',
                        'last_response_time' => 5, // G11
                        'last_result' => true, // G21
                    ]
                ]
            ],
            // R02: Analisa Krisis Belajar (Diagnosis -> Memicu R07)
            [
                'name'     => 'Tester R02 (Krisis Belajar)',
                'email'    => 'r02@tester.com',
                'password' => 'mhs123',
                'state'    => [
                    'total_answered' => 20,
                    'correct_count'  => 6,
                    StudentStateSchema::ACCURACY => 30.0, // G01
                    StudentStateSchema::HINTS_USED => 0,
                    StudentStateSchema::LEVEL => StudentLevel::PEMULA->value,
                    StudentStateSchema::PERFORMANCE_METRICS => [
                        'trend' => 'down', // G05
                        'last_response_time' => 20,
                        'last_result' => false, // G22
                    ]
                ]
            ],
            // R03: Analisa Kesulitan Materi (Diagnosis -> Memicu R08)
            [
                'name'     => 'Tester R03 (Kesulitan Materi)',
                'email'    => 'r03@tester.com',
                'password' => 'mhs123',
                'state'    => [
                    'total_answered' => 20,
                    'correct_count'  => 10,
                    StudentStateSchema::ACCURACY => 50.0, // G02
                    StudentStateSchema::HINTS_USED => 0,
                    StudentStateSchema::LEVEL => StudentLevel::PEMULA->value,
                    StudentStateSchema::PERFORMANCE_METRICS => [
                        'trend' => 'stable',
                        'last_response_time' => 50, // G12
                        'last_result' => false, // G22
                    ]
                ]
            ],
            // R04: Analisa Pola Bantuan (Diagnosis -> Memicu R09)
            [
                'name'     => 'Tester R04 (Pola Bantuan)',
                'email'    => 'r04@tester.com',
                'password' => 'mhs123',
                'state'    => [
                    'total_answered' => 20,
                    'correct_count'  => 14,
                    StudentStateSchema::ACCURACY => 70.0, 
                    StudentStateSchema::HINTS_USED => 5, // G08
                    StudentStateSchema::LEVEL => StudentLevel::PEMULA->value,
                    StudentStateSchema::PERFORMANCE_METRICS => [
                        'trend' => 'stable',
                        'last_response_time' => 50, // G12
                        'last_result' => true,
                    ]
                ]
            ],
            // R05: Analisa Potensi Menebak (Diagnosis -> Memicu R10)
            [
                'name'     => 'Tester R05 (Potensi Menebak)',
                'email'    => 'r05@tester.com',
                'password' => 'mhs123',
                'state'    => [
                    'total_answered' => 20,
                    'correct_count'  => 7,
                    StudentStateSchema::ACCURACY => 35.0, // G01
                    StudentStateSchema::HINTS_USED => 0,
                    StudentStateSchema::LEVEL => StudentLevel::PEMULA->value,
                    StudentStateSchema::PERFORMANCE_METRICS => [
                        'trend' => 'stable',
                        'last_response_time' => 5, // G11
                        'last_result' => false,
                    ]
                ]
            ],
            // R06: Strategi Akselerasi (Intervensi V03 + G20)
            [
                'name'     => 'Tester R06 (Strategi Akselerasi)',
                'email'    => 'r06@tester.com',
                'password' => 'mhs123',
                'state'    => [
                    'total_answered' => 20,
                    'correct_count'  => 19,
                    StudentStateSchema::ACCURACY => 95.0, // G17
                    StudentStateSchema::HINTS_USED => 0, // G20
                    StudentStateSchema::LEVEL => StudentLevel::MENENGAH->value,
                    StudentStateSchema::PERFORMANCE_METRICS => [
                        'trend' => 'up',
                        'last_response_time' => 5, // G11
                        'last_result' => true, // G21
                    ]
                ]
            ],
            // R07: Strategi Intervensi Krisis (Intervensi V01)
            [
                'name'     => 'Tester R07 (Intervensi Krisis)',
                'email'    => 'r07@tester.com',
                'password' => 'mhs123',
                'state'    => [
                    'total_answered' => 20,
                    'correct_count'  => 5,
                    StudentStateSchema::ACCURACY => 25.0, // G01
                    StudentStateSchema::HINTS_USED => 2,
                    StudentStateSchema::LEVEL => StudentLevel::PEMULA->value,
                    StudentStateSchema::PERFORMANCE_METRICS => [
                        'trend' => 'down', // G05
                        'last_response_time' => 15,
                        'last_result' => false, // G22
                    ]
                ]
            ],
            // R08: Strategi Adaptasi Kesulitan (Intervensi V02)
            [
                'name'     => 'Tester R08 (Adaptasi Kesulitan)',
                'email'    => 'r08@tester.com',
                'password' => 'mhs123',
                'state'    => [
                    'total_answered' => 20,
                    'correct_count'  => 9,
                    StudentStateSchema::ACCURACY => 45.0, // G02
                    StudentStateSchema::HINTS_USED => 1,
                    StudentStateSchema::LEVEL => StudentLevel::PEMULA->value,
                    StudentStateSchema::PERFORMANCE_METRICS => [
                        'trend' => 'down',
                        'last_response_time' => 60, // G12
                        'last_result' => false, // G22
                    ]
                ]
            ],
            // R09: Strategi Penguatan Mandiri (Intervensi V04)
            [
                'name'     => 'Tester R09 (Penguatan Mandiri)',
                'email'    => 'r09@tester.com',
                'password' => 'mhs123',
                'state'    => [
                    'total_answered' => 20,
                    'correct_count'  => 17,
                    StudentStateSchema::ACCURACY => 85.0, 
                    StudentStateSchema::HINTS_USED => 4, // G08
                    StudentStateSchema::LEVEL => StudentLevel::MENENGAH->value,
                    StudentStateSchema::PERFORMANCE_METRICS => [
                        'trend' => 'stable',
                        'last_response_time' => 55, // G12
                        'last_result' => true,
                    ]
                ]
            ],
            // R10: Strategi Bimbingan Fokus (Intervensi V05)
            [
                'name'     => 'Tester R10 (Bimbingan Fokus)',
                'email'    => 'r10@tester.com',
                'password' => 'mhs123',
                'state'    => [
                    'total_answered' => 20,
                    'correct_count'  => 4,
                    StudentStateSchema::ACCURACY => 20.0, // G01
                    StudentStateSchema::HINTS_USED => 0,
                    StudentStateSchema::LEVEL => StudentLevel::PEMULA->value,
                    StudentStateSchema::PERFORMANCE_METRICS => [
                        'trend' => 'down',
                        'last_response_time' => 4, // G11
                        'last_result' => false,
                    ]
                ]
            ],
            // R11: Strategi Kelulusan Materi (G19 Expert, G17 Akurasi > 90%)
            [
                'name'     => 'Tester R11 (Kelulusan Materi)',
                'email'    => 'r11@tester.com',
                'password' => 'mhs123',
                'state'    => [
                    'total_answered' => 50,
                    'correct_count'  => 49,
                    StudentStateSchema::ACCURACY => 98.0, // G17
                    StudentStateSchema::HINTS_USED => 0,
                    StudentStateSchema::LEVEL => StudentLevel::AHLI->value, // G19
                    StudentStateSchema::PERFORMANCE_METRICS => [
                        'trend' => 'up',
                        'last_response_time' => 20, // Not G11 to avoid R06 overriding R11
                        'last_result' => true,
                    ]
                ]
            ]
        ];

        foreach ($testers as $tester) {
            $user = User::updateOrCreate(
                ['email' => $tester['email']],
                [
                    'name'        => $tester['name'],
                    'password'    => Hash::make($tester['password']),
                    'role_id'     => $role->id,
                    'is_approved' => true,
                ]
            );

            StudentState::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'total_answered'                    => $tester['state']['total_answered'] ?? 0,
                    'correct_count'                     => $tester['state']['correct_count'] ?? 0,
                    StudentStateSchema::XP              => $tester['state'][StudentStateSchema::XP] ?? 0,
                    StudentStateSchema::LEVEL           => $tester['state'][StudentStateSchema::LEVEL] ?? StudentLevel::PEMULA->value,
                    StudentStateSchema::STREAK          => $tester['state'][StudentStateSchema::STREAK] ?? 0,
                    StudentStateSchema::HINTS_USED      => $tester['state'][StudentStateSchema::HINTS_USED] ?? 0,
                    StudentStateSchema::HINTS_AVAILABLE => 3,
                    StudentStateSchema::MAX_STREAK      => max(5, $tester['state'][StudentStateSchema::STREAK] ?? 0),
                    'badges'                            => [],
                    StudentStateSchema::ACCURACY        => $tester['state'][StudentStateSchema::ACCURACY] ?? 0.0,
                    StudentStateSchema::SESSION_HISTORY => [],
                    StudentStateSchema::CURRENT_SESSION => [
                        'correct'    => 0,
                        'total'      => 0,
                        'hints'      => $tester['state'][StudentStateSchema::HINTS_USED] ?? 0,
                        'time_spent' => 0,
                    ],
                    StudentStateSchema::PERFORMANCE_METRICS => $tester['state'][StudentStateSchema::PERFORMANCE_METRICS] ?? [
                        'trend'          => 'stable',
                        'speed'          => 'normal',
                        'stagnant_count' => 0,
                        'last_used_hint' => false,
                        'last_response_time' => 20,
                        'last_result' => false,
                    ],
                    StudentStateSchema::ADAPTIVE_STATE => [
                        'consecutive_correct' => 0,
                        'help_count_session'  => $tester['state'][StudentStateSchema::HINTS_USED] ?? 0,
                    ],
                    'last_active_at' => now(),
                ]
            );
        }

        $this->command->info("Test rules users seeded successfully!");
    }
}
