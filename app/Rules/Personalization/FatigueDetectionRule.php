<?php

namespace App\Rules\Personalization;

/**
 * Fatigue Detection Rule
 * 
 * Detects users experiencing fatigue
 */
class FatigueDetectionRule
{
    public function getName(): string
    {
        return 'Personalisasi: Deteksi Kelelahan';
    }

    public function evaluate(array $state): ?array
    {
        $totalQuestions = $state['total_questions_answered'] ?? 0;
        $totalTime = $state['total_time_spent'] ?? 0;
        $wrongStreak = $state['wrong_streak'] ?? 0;
        $accuracy = $this->calculateAccuracy($state);

        if ($totalQuestions >= 10 && $totalTime >= 30 && $wrongStreak >= 2 && $accuracy < 70) {
            return [
                'actions' => [
                    ['key' => 'show_fatigue_warning', 'operator' => '=', 'value' => 1, 'message' => 'Sebaiknya istirahat sejenak'],
                    ['key' => 'hints_available', 'operator' => '+', 'value' => 1, 'message' => 'Bonus hint'],
                    ['key' => 'personalization_type', 'operator' => '=', 'value' => 'fatigued'],
                ]
            ];
        }

        return null;
    }

    private function calculateAccuracy(array $state): float
    {
        $correct = $state['correct_count'] ?? 0;
        $total = $state['total_questions_answered'] ?? 0;
        return $total > 0 ? ($correct / $total) * 100 : 0;
    }
}
