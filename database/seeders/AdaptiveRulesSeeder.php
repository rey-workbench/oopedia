<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdaptiveRule;
use App\Models\User;

class AdaptiveRulesSeeder extends Seeder
{
    public function run()
    {
        // Pastikan ada creator
        $creator = User::where('role_id', 2)->first() ?? User::where('role_id', 1)->first() ?? User::first();
        $creatorId = $creator ? $creator->id : 1;

        $rules = [
            // ===== RULE 1: Level Up Pemula ke Menengah =====
            [
                'name' => 'Level Up: Pemula → Menengah',
                'description' => 'Menandakan mahasiswa telah memahami materi dasar',
                'conditions' => [
                    ['type' => 'current_level', 'operator' => '==', 'value' => 'beginner'],
                    ['type' => 'current_streak', 'operator' => '>=', 'value' => 3],
                    ['type' => 'total_questions_answered', 'operator' => '>=', 'value' => 5]
                ],
                'actions' => [
                    ['type' => 'update_attribute', 'key' => 'current_level', 'operator' => '=', 'value' => 'medium'],
                    ['type' => 'update_attribute', 'key' => 'current_streak', 'operator' => '=', 'value' => 0],
                    ['type' => 'update_attribute', 'key' => 'wrong_streak', 'operator' => '=', 'value' => 0],
                    ['type' => 'update_attribute', 'key' => 'level_correct_count', 'operator' => '=', 'value' => 0],
                    ['type' => 'update_attribute', 'key' => 'level_attempt_count', 'operator' => '=', 'value' => 0],
                    ['type' => 'update_attribute', 'key' => 'xp', 'operator' => '+', 'value' => 100],
                    ['type' => 'update_attribute', 'key' => 'points', 'operator' => '+', 'value' => 50]
                ],
                'priority' => 100
            ],
            
            // ===== RULE 2: Level Up Menengah ke Sulit =====
            [
                'name' => 'Level Up: Menengah → Sulit',
                'description' => 'Memastikan kesiapan menghadapi level sulit',
                'conditions' => [
                    ['type' => 'current_level', 'operator' => '==', 'value' => 'medium'],
                    ['type' => 'current_streak', 'operator' => '>=', 'value' => 4],
                    ['type' => 'accuracy', 'operator' => '>=', 'value' => 75]
                ],
                'actions' => [
                    ['type' => 'update_attribute', 'key' => 'current_level', 'operator' => '=', 'value' => 'hard'],
                    ['type' => 'update_attribute', 'key' => 'current_streak', 'operator' => '=', 'value' => 0],
                    ['type' => 'update_attribute', 'key' => 'wrong_streak', 'operator' => '=', 'value' => 0],
                    ['type' => 'update_attribute', 'key' => 'level_correct_count', 'operator' => '=', 'value' => 0],
                    ['type' => 'update_attribute', 'key' => 'level_attempt_count', 'operator' => '=', 'value' => 0],
                    ['type' => 'update_attribute', 'key' => 'xp', 'operator' => '+', 'value' => 150],
                    ['type' => 'update_attribute', 'key' => 'points', 'operator' => '+', 'value' => 75]
                ],
                'priority' => 95
            ],
            
            // ===== RULE 3: Level Down Sulit ke Menengah =====
            [
                'name' => 'Level Down: Sulit → Menengah',
                'description' => 'Mengurangi beban kognitif',
                'conditions' => [
                    ['type' => 'current_level', 'operator' => '==', 'value' => 'hard'],
                    ['type' => 'wrong_streak', 'operator' => '>=', 'value' => 3]
                ],
                'actions' => [
                    ['type' => 'update_attribute', 'key' => 'current_level', 'operator' => '=', 'value' => 'medium'],
                    ['type' => 'update_attribute', 'key' => 'current_streak', 'operator' => '=', 'value' => 0],
                    ['type' => 'update_attribute', 'key' => 'wrong_streak', 'operator' => '=', 'value' => 0],
                    ['type' => 'update_attribute', 'key' => 'level_correct_count', 'operator' => '=', 'value' => 0],
                    ['type' => 'update_attribute', 'key' => 'level_attempt_count', 'operator' => '=', 'value' => 0],
                    ['type' => 'update_attribute', 'key' => 'retry_count', 'operator' => '+', 'value' => 1]
                ],
                'priority' => 90
            ],
            
            // ===== RULE 4: Level Down Menengah ke Pemula =====
            [
                'name' => 'Level Down: Menengah → Pemula',
                'description' => 'Memperkuat pemahaman dasar',
                'conditions' => [
                    ['type' => 'current_level', 'operator' => '==', 'value' => 'medium'],
                    ['type' => 'wrong_streak', 'operator' => '>=', 'value' => 4],
                    ['type' => 'accuracy', 'operator' => '<', 'value' => 40]
                ],
                'actions' => [
                    ['type' => 'update_attribute', 'key' => 'current_level', 'operator' => '=', 'value' => 'beginner'],
                    ['type' => 'update_attribute', 'key' => 'current_streak', 'operator' => '=', 'value' => 0],
                    ['type' => 'update_attribute', 'key' => 'wrong_streak', 'operator' => '=', 'value' => 0],
                    ['type' => 'update_attribute', 'key' => 'level_correct_count', 'operator' => '=', 'value' => 0],
                    ['type' => 'update_attribute', 'key' => 'level_attempt_count', 'operator' => '=', 'value' => 0],
                    ['type' => 'update_attribute', 'key' => 'retry_count', 'operator' => '+', 'value' => 1]
                ],
                'priority' => 85
            ],
            
            // ===== RULE 5: Jawaban Benar =====
            [
                'name' => 'Reward: Jawaban Benar',
                'description' => 'Memberi reward langsung',
                'conditions' => [
                    ['type' => 'is_correct', 'operator' => '==', 'value' => 'true']
                ],
                'actions' => [
                    ['type' => 'update_attribute', 'key' => 'xp', 'operator' => '+', 'value' => 10],
                    ['type' => 'update_attribute', 'key' => 'points', 'operator' => '+', 'value' => 5],
                    ['type' => 'update_attribute', 'key' => 'current_streak', 'operator' => '+', 'value' => 1],
                    ['type' => 'update_attribute', 'key' => 'wrong_streak', 'operator' => '=', 'value' => 0],
                    ['type' => 'update_attribute', 'key' => 'total_questions_answered', 'operator' => '+', 'value' => 1]
                ],
                'priority' => 1000 // Highest priority
            ],
            
            // ===== RULE 6: Jawaban Salah =====
            [
                'name' => 'Update: Jawaban Salah',
                'description' => 'Menjaga konsistensi evaluasi',
                'conditions' => [
                    ['type' => 'is_correct', 'operator' => '==', 'value' => 'false']
                ],
                'actions' => [
                    ['type' => 'update_attribute', 'key' => 'current_streak', 'operator' => '=', 'value' => 0],
                    ['type' => 'update_attribute', 'key' => 'wrong_streak', 'operator' => '+', 'value' => 1],
                    ['type' => 'update_attribute', 'key' => 'total_questions_answered', 'operator' => '+', 'value' => 1]
                ],
                'priority' => 1000 // Highest priority
            ],
            
            // ===== RULE 7: Streak Bonus (Hint Reward) =====
            [
                'name' => 'Bonus: Streak 5 (Hint Reward)',
                'description' => 'Memberikan hint sebagai reward konsistensi',
                'conditions' => [
                    ['type' => 'current_streak', 'operator' => '==', 'value' => 5]
                ],
                'actions' => [
                    ['type' => 'update_attribute', 'key' => 'hints_available', 'operator' => '+', 'value' => 1],
                    ['type' => 'update_attribute', 'key' => 'current_streak', 'operator' => '=', 'value' => 0]
                ],
                'priority' => 80
            ],
            
            // ===== RULE 8: Hint Digunakan =====
            [
                'name' => 'Penalty: Hint Digunakan',
                'description' => 'Menjaga keadilan penilaian (XP dikurangi 50%)',
                'conditions' => [
                    ['type' => 'used_hint', 'operator' => '==', 'value' => 'true'],
                    ['type' => 'is_correct', 'operator' => '==', 'value' => 'true']
                ],
                'actions' => [
                    ['type' => 'update_attribute', 'key' => 'hints_used', 'operator' => '+', 'value' => 1],
                    ['type' => 'update_attribute', 'key' => 'hints_available', 'operator' => '-', 'value' => 1],
                    ['type' => 'update_attribute', 'key' => 'xp', 'operator' => '-', 'value' => 5], // 50% Penalty from base 10
                ],
                'priority' => 900
            ],
            
            // ===== RULE 9: Accuracy Bonus 60-74% =====
            [
                'name' => 'Bonus: Accuracy 60-74%',
                'description' => 'Menghargai performa stabil',
                'conditions' => [
                    ['type' => 'total_questions_answered', 'operator' => '>=', 'value' => 5],
                    ['type' => 'accuracy', 'operator' => '>=', 'value' => 60],
                    ['type' => 'accuracy', 'operator' => '<', 'value' => 75]
                ],
                'actions' => [
                    ['type' => 'update_attribute', 'key' => 'xp', 'operator' => '+', 'value' => 10]
                ],
                'priority' => 60
            ],
            
            // ===== RULE 10: Accuracy Bonus 75-84% =====
            [
                'name' => 'Bonus: Accuracy 75-84%',
                'description' => 'Menghargai pemahaman baik',
                'conditions' => [
                    ['type' => 'total_questions_answered', 'operator' => '>=', 'value' => 5],
                    ['type' => 'accuracy', 'operator' => '>=', 'value' => 75],
                    ['type' => 'accuracy', 'operator' => '<', 'value' => 85]
                ],
                'actions' => [
                    ['type' => 'update_attribute', 'key' => 'xp', 'operator' => '+', 'value' => 25],
                    ['type' => 'update_attribute', 'key' => 'points', 'operator' => '+', 'value' => 25]
                ],
                'priority' => 65
            ],
            
            // ===== RULE 11: Accuracy Bonus ≥85% =====
            [
                'name' => 'Bonus: Accuracy ≥85% (Badge Akurat)',
                'description' => 'Menghargai pemahaman sangat tinggi',
                'conditions' => [
                    ['type' => 'total_questions_answered', 'operator' => '>=', 'value' => 5],
                    ['type' => 'accuracy', 'operator' => '>=', 'value' => 85]
                ],
                'actions' => [
                    ['type' => 'update_attribute', 'key' => 'xp', 'operator' => '+', 'value' => 50],
                    ['type' => 'update_attribute', 'key' => 'points', 'operator' => '+', 'value' => 50],
                    // Badge will be handled separately in frontend
                ],
                'priority' => 70
            ],
            
            // ===== RULE 12: Retry Count ≥3 (Material Redirect) =====
            [
                'name' => 'Redirect: Retry Count ≥3',
                'description' => 'Memberikan redirect yes/no untuk belajar ke materi terkait',
                'conditions' => [
                    ['type' => 'retry_count', 'operator' => '>=', 'value' => 3]
                ],
                'actions' => [
                    ['type' => 'update_attribute', 'key' => 'show_material_redirect', 'operator' => '=', 'value' => 1]
                ],
                'priority' => 75
            ]
        ];

        foreach ($rules as $ruleData) {
            AdaptiveRule::updateOrCreate(
                ['name' => $ruleData['name']],
                array_merge($ruleData, [
                    'created_by' => $creatorId,
                    'is_active' => true,
                    // Fill old columns for backward compatibility
                    'condition_type' => 'composite',
                    'condition_operator' => 'composite',
                    'condition_value' => 'composite',
                    'action_type' => 'update_attribute',
                    'action_value' => 'composite'
                ])
            );
        }
    }
}
