<?php

namespace App\Services;

use App\Models\AdaptiveRule;
use App\Models\User;
use App\Models\Material;
use Illuminate\Support\Facades\Log;

class ForwardChainingService
{
    /**
     * Evaluasi semua rules untuk user tertentu pada materi tertentu
     * 
     * @param User $user
     * @param Material|null $material
     * @param array $userStats Statistik user saat ini
     * @return array Rules yang terpenuhi beserta aksinya
     */
    public function evaluateRules(User $user, ?Material $material, array $userStats)
    {
        // Ambil rules yang aktif, diurutkan berdasarkan prioritas
        $query = AdaptiveRule::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc');

        // Filter berdasarkan materi jika ada
        if ($material) {
            $query->where(function($q) use ($material) {
                $q->whereNull('material_id')
                  ->orWhere('material_id', $material->id);
            });
        } else {
            $query->whereNull('material_id');
        }

        $rules = $query->get();
        $triggeredRules = [];

        // Forward Chaining: Evaluasi setiap rule
        foreach ($rules as $rule) {
            if ($this->evaluateCondition($rule, $userStats)) {
                $triggeredRules[] = [
                    'rule' => $rule,
                    'action' => [
                        'type' => $rule->action_type,
                        'value' => $rule->action_value
                    ]
                ];

                // Log untuk debugging
                Log::info('Adaptive Rule Triggered', [
                    'user_id' => $user->id,
                    'rule_id' => $rule->id,
                    'rule_name' => $rule->name,
                    'action' => $rule->action_type,
                    'action_value' => $rule->action_value
                ]);
            }
        }

        return $triggeredRules;
    }

    /**
     * Evaluasi kondisi dari sebuah rule
     * 
     * @param AdaptiveRule $rule
     * @param array $userStats
     * @return bool
     */
    private function evaluateCondition(AdaptiveRule $rule, array $userStats)
    {
        // Ambil nilai dari user stats berdasarkan tipe kondisi
        $actualValue = $userStats[$rule->condition_type] ?? 0;
        $conditionValue = $rule->condition_value;

        // Evaluasi berdasarkan operator
        switch ($rule->condition_operator) {
            case '>':
                return $actualValue > floatval($conditionValue);
            
            case '<':
                return $actualValue < floatval($conditionValue);
            
            case '>=':
                return $actualValue >= floatval($conditionValue);
            
            case '<=':
                return $actualValue <= floatval($conditionValue);
            
            case '==':
                return $actualValue == floatval($conditionValue);
            
            case 'between':
                // Format: "min-max"
                $range = explode('-', $conditionValue);
                if (count($range) === 2) {
                    $min = floatval(trim($range[0]));
                    $max = floatval(trim($range[1]));
                    return $actualValue >= $min && $actualValue <= $max;
                }
                return false;
            
            default:
                return false;
        }
    }

    /**
     * Terapkan aksi dari rules yang terpenuhi
     * 
     * @param array $triggeredRules
     * @return array Hasil penerapan aksi
     */
    public function applyActions(array $triggeredRules)
    {
        $results = [];

        foreach ($triggeredRules as $triggeredRule) {
            $rule = $triggeredRule['rule'];
            $action = $triggeredRule['action'];

            switch ($action['type']) {
                case 'change_difficulty':
                    $results[] = [
                        'type' => 'difficulty_change',
                        'value' => $action['value'],
                        'message' => "Tingkat kesulitan diubah menjadi {$action['value']}"
                    ];
                    break;

                case 'show_hint':
                    $results[] = [
                        'type' => 'show_hint',
                        'value' => true,
                        'message' => 'Petunjuk akan ditampilkan'
                    ];
                    break;

                case 'skip_questions':
                    $results[] = [
                        'type' => 'skip_questions',
                        'value' => intval($action['value']),
                        'message' => "Melewati {$action['value']} soal"
                    ];
                    break;

                case 'recommend_material':
                    $results[] = [
                        'type' => 'recommend_material',
                        'value' => $action['value'],
                        'message' => "Direkomendasikan untuk mempelajari materi: {$action['value']}"
                    ];
                    break;

                case 'end_quiz':
                    $results[] = [
                        'type' => 'end_quiz',
                        'value' => true,
                        'message' => 'Kuis akan diakhiri'
                    ];
                    break;
            }
        }

        return $results;
    }

    /**
     * Hitung statistik user untuk evaluasi rules
     * 
     * @param User $user
     * @param Material|null $material
     * @return array
     */
    public function calculateUserStats(User $user, ?Material $material = null)
    {
        $query = $user->progress();

        if ($material) {
            $query->whereHas('question', function($q) use ($material) {
                $q->where('material_id', $material->id);
            });
        }

        $progress = $query->get();
        
        $totalQuestions = $progress->count();
        $correctAnswers = $progress->where('is_correct', true)->count();
        $wrongAnswers = $totalQuestions - $correctAnswers;

        // Hitung consecutive correct/wrong
        $consecutiveCorrect = 0;
        $consecutiveWrong = 0;
        $currentStreak = 0;
        $lastResult = null;

        foreach ($progress->sortByDesc('created_at') as $item) {
            if ($item->is_correct) {
                if ($lastResult === true) {
                    $currentStreak++;
                } else {
                    $currentStreak = 1;
                }
                $consecutiveCorrect = max($consecutiveCorrect, $currentStreak);
                $lastResult = true;
            } else {
                if ($lastResult === false) {
                    $currentStreak++;
                } else {
                    $currentStreak = 1;
                }
                $consecutiveWrong = max($consecutiveWrong, $currentStreak);
                $lastResult = false;
            }
        }

        // Hitung waktu yang dihabiskan (dalam detik)
        $timeSpent = $progress->sum('time_spent') ?? 0;

        // Hitung accuracy rate
        $accuracyRate = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;

        // Hitung score range (persentase jawaban benar)
        $scoreRange = $accuracyRate;

        return [
            'score_range' => round($scoreRange, 2),
            'consecutive_correct' => $consecutiveCorrect,
            'consecutive_wrong' => $consecutiveWrong,
            'time_spent' => $timeSpent,
            'accuracy_rate' => round($accuracyRate, 2),
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'wrong_answers' => $wrongAnswers
        ];
    }
}
