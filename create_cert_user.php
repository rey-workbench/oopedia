<?php
$role = \App\Models\Role::where('role_name', 'Mahasiswa')->first();
$user = \App\Models\User::updateOrCreate(
    ['email' => 'test@gmail.com'],
    [
        'name' => 'Rey',
        'nim' => '2241720999',
        'class' => 'TI-4A',
        'password' => 'password',
        'role_id' => $role->id,
        'is_approved' => true
    ]
);

$material = \App\Models\Material::first();

$state = \App\Models\StudentState::updateOrCreate(
    ['user_id' => $user->id],
    [
        'xp' => 15000,
        'level' => 'Expert',
        'streak' => 10,
        'max_streak' => 10,
        'total_answered' => 100,
        'correct_count' => 95,
        'accuracy' => 95.0,
        'hints_used' => 0,
        'hints_available' => 10,
        'session_history' => [],
        'current_session' => [],
        'performance_metrics' => [
            'last_result' => true,
            'last_response_time' => 5,
            'trend' => 'up'
        ],
        'certifications' => $material ? [$material->id => 'gold'] : [],
        'adaptive_state' => []
    ]
);

echo "Berhasil membuat user tersertifikasi dengan email: " . $user->email . "\n";
