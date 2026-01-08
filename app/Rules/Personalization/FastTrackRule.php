<?php

namespace App\Rules\Personalization;

/**
 * Fast-Track Rule
 * 
 * Detects users with high speed + high accuracy
 */
class FastTrackRule
{
    public function getName(): string
    {
        return 'Personalisasi: Fast-Track';
    }

    public function evaluate(array $state): ?array
    {
        if (!($state['is_correct'] ?? false)) {
            return null;
        }

        $avgTime = $state['avg_time_spent'] ?? 999;
        $streak = $state['current_streak'] ?? 0;
        $accuracy = $this->calculateAccuracy($state);

        if ($avgTime < 15 && $streak >= 3 && $accuracy >= 90) {
            return [
                'actions' => [
                    ['key' => 'xp', 'operator' => '+', 'value' => 30, 'message' => 'Fast-Track Bonus: +30 XP'],
                    ['key' => 'fast_track_active', 'operator' => '=', 'value' => 1],
                    ['key' => 'personalization_type', 'operator' => '=', 'value' => 'fast_learner'],
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
