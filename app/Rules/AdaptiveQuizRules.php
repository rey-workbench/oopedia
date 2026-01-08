<?php

namespace App\Rules;

class AdaptiveQuizRules
{
    /**
     * Evaluate all adaptive rules and return triggered rules with state changes
     * 
     * @param array $state Current user state (xp, streak, level, etc.)
     * @param bool $isCorrect Whether the answer was correct
     * @param bool $usedHint Whether a hint was used
     * @return array ['triggered_rules' => [], 'new_state' => [], 'actions_report' => []]
     */
    public function evaluateAll(array $state, bool $isCorrect, bool $usedHint): array
    {
        $triggeredRules = [];
        $actionsReport = [];
        $newState = $state;

        // Priority order: Higher priority first
        $rules = [
            // Highest priority: Basic answer processing
            ['priority' => 1000, 'name' => 'Reward: Jawaban Benar', 'method' => 'checkCorrectAnswer'],
            ['priority' => 1000, 'name' => 'Update: Jawaban Salah', 'method' => 'checkWrongAnswer'],
            ['priority' => 900, 'name' => 'Penalty: Hint Digunakan', 'method' => 'checkHintUsed'],
            
            // Level transitions
            ['priority' => 100, 'name' => 'Level Up: Pemula → Menengah', 'method' => 'checkLevelUpBeginnerToMedium'],
            ['priority' => 95, 'name' => 'Level Up: Menengah → Sulit', 'method' => 'checkLevelUpMediumToHard'],
            ['priority' => 90, 'name' => 'Level Down: Sulit → Menengah', 'method' => 'checkLevelDownHardToMedium'],
            ['priority' => 85, 'name' => 'Level Down: Menengah → Pemula', 'method' => 'checkLevelDownMediumToBeginner'],
            
            // Bonuses
            ['priority' => 80, 'name' => 'Bonus: Streak 5 (Hint Reward)', 'method' => 'checkStreakBonus'],
            ['priority' => 75, 'name' => 'Redirect: Retry Count ≥3', 'method' => 'checkRetryRedirect'],
            ['priority' => 70, 'name' => 'Bonus: Accuracy ≥85%', 'method' => 'checkAccuracyBonus85'],
            ['priority' => 65, 'name' => 'Bonus: Accuracy 75-84%', 'method' => 'checkAccuracyBonus75'],
            ['priority' => 60, 'name' => 'Bonus: Accuracy 60-74%', 'method' => 'checkAccuracyBonus60'],
        ];

        // Sort by priority descending
        usort($rules, fn($a, $b) => $b['priority'] <=> $a['priority']);

        // Evaluate each rule
        foreach ($rules as $rule) {
            $result = $this->{$rule['method']}($newState, $isCorrect, $usedHint);
            
            if ($result !== null) {
                $triggeredRules[] = $rule['name'];
                
                // Apply state changes
                foreach ($result['actions'] as $action) {
                    $key = $action['key'];
                    $operator = $action['operator'];
                    $value = $action['value'];
                    
                    $oldValue = $newState[$key] ?? 0;
                    
                    switch ($operator) {
                        case '=':
                            $newState[$key] = $value;
                            break;
                        case '+':
                            $newState[$key] = ($newState[$key] ?? 0) + $value;
                            break;
                        case '-':
                            $newState[$key] = ($newState[$key] ?? 0) - $value;
                            break;
                    }
                    
                    $actionsReport[] = [
                        'rule' => $rule['name'],
                        'key' => $key,
                        'old_value' => $oldValue,
                        'new_value' => $newState[$key],
                        'message' => $action['message'] ?? "{$key}: {$oldValue} → {$newState[$key]}"
                    ];
                }
            }
        }

        return [
            'triggered_rules' => $triggeredRules,
            'new_state' => $newState,
            'actions_report' => $actionsReport
        ];
    }

    /**
     * Rule 5: Jawaban Benar
     */
    protected function checkCorrectAnswer(array $state, bool $isCorrect, bool $usedHint): ?array
    {
        if (!$isCorrect) {
            return null;
        }

        return [
            'actions' => [
                ['key' => 'xp', 'operator' => '+', 'value' => 10],
                ['key' => 'points', 'operator' => '+', 'value' => 5],
                ['key' => 'current_streak', 'operator' => '+', 'value' => 1],
                ['key' => 'wrong_streak', 'operator' => '=', 'value' => 0],
                ['key' => 'total_questions_answered', 'operator' => '+', 'value' => 1],
                ['key' => 'correct_count', 'operator' => '+', 'value' => 1],
                ['key' => 'level_correct_count', 'operator' => '+', 'value' => 1],
                ['key' => 'level_attempt_count', 'operator' => '+', 'value' => 1],
            ]
        ];
    }

    /**
     * Rule 6: Jawaban Salah
     */
    protected function checkWrongAnswer(array $state, bool $isCorrect, bool $usedHint): ?array
    {
        if ($isCorrect) {
            return null;
        }

        return [
            'actions' => [
                ['key' => 'current_streak', 'operator' => '=', 'value' => 0],
                ['key' => 'wrong_streak', 'operator' => '+', 'value' => 1],
                ['key' => 'total_questions_answered', 'operator' => '+', 'value' => 1],
                ['key' => 'wrong_count', 'operator' => '+', 'value' => 1],
                ['key' => 'level_attempt_count', 'operator' => '+', 'value' => 1],
            ]
        ];
    }

    /**
     * Rule 8: Hint Digunakan
     */
    protected function checkHintUsed(array $state, bool $isCorrect, bool $usedHint): ?array
    {
        if (!$usedHint || !$isCorrect) {
            return null;
        }

        return [
            'actions' => [
                ['key' => 'hints_used', 'operator' => '+', 'value' => 1],
                ['key' => 'hints_available', 'operator' => '-', 'value' => 1],
                ['key' => 'xp', 'operator' => '-', 'value' => 5, 'message' => 'XP penalty for using hint: -5'],
            ]
        ];
    }

    /**
     * Rule 1: Level Up Pemula → Menengah
     */
    protected function checkLevelUpBeginnerToMedium(array $state, bool $isCorrect, bool $usedHint): ?array
    {
        if (($state['current_level'] ?? 'beginner') !== 'beginner') {
            return null;
        }

        if (($state['current_streak'] ?? 0) < 3) {
            return null;
        }

        if (($state['total_questions_answered'] ?? 0) < 5) {
            return null;
        }

        return [
            'actions' => [
                ['key' => 'current_level', 'operator' => '=', 'value' => 'medium'],
                ['key' => 'current_streak', 'operator' => '=', 'value' => 0],
                ['key' => 'wrong_streak', 'operator' => '=', 'value' => 0],
                ['key' => 'level_correct_count', 'operator' => '=', 'value' => 0],
                ['key' => 'level_attempt_count', 'operator' => '=', 'value' => 0],
                ['key' => 'xp', 'operator' => '+', 'value' => 100],
                ['key' => 'points', 'operator' => '+', 'value' => 50],
            ]
        ];
    }

    /**
     * Rule 2: Level Up Menengah → Sulit
     */
    protected function checkLevelUpMediumToHard(array $state, bool $isCorrect, bool $usedHint): ?array
    {
        if (($state['current_level'] ?? 'beginner') !== 'medium') {
            return null;
        }

        if (($state['current_streak'] ?? 0) < 4) {
            return null;
        }

        $accuracy = $this->calculateAccuracy($state);
        if ($accuracy < 75) {
            return null;
        }

        return [
            'actions' => [
                ['key' => 'current_level', 'operator' => '=', 'value' => 'hard'],
                ['key' => 'current_streak', 'operator' => '=', 'value' => 0],
                ['key' => 'wrong_streak', 'operator' => '=', 'value' => 0],
                ['key' => 'level_correct_count', 'operator' => '=', 'value' => 0],
                ['key' => 'level_attempt_count', 'operator' => '=', 'value' => 0],
                ['key' => 'xp', 'operator' => '+', 'value' => 150],
                ['key' => 'points', 'operator' => '+', 'value' => 75],
            ]
        ];
    }

    /**
     * Rule 3: Level Down Sulit → Menengah
     */
    protected function checkLevelDownHardToMedium(array $state, bool $isCorrect, bool $usedHint): ?array
    {
        if (($state['current_level'] ?? 'beginner') !== 'hard') {
            return null;
        }

        if (($state['wrong_streak'] ?? 0) < 3) {
            return null;
        }

        return [
            'actions' => [
                ['key' => 'current_level', 'operator' => '=', 'value' => 'medium'],
                ['key' => 'current_streak', 'operator' => '=', 'value' => 0],
                ['key' => 'wrong_streak', 'operator' => '=', 'value' => 0],
                ['key' => 'level_correct_count', 'operator' => '=', 'value' => 0],
                ['key' => 'level_attempt_count', 'operator' => '=', 'value' => 0],
                ['key' => 'retry_count', 'operator' => '+', 'value' => 1],
            ]
        ];
    }

    /**
     * Rule 4: Level Down Menengah → Pemula
     */
    protected function checkLevelDownMediumToBeginner(array $state, bool $isCorrect, bool $usedHint): ?array
    {
        if (($state['current_level'] ?? 'beginner') !== 'medium') {
            return null;
        }

        if (($state['wrong_streak'] ?? 0) < 4) {
            return null;
        }

        $accuracy = $this->calculateAccuracy($state);
        if ($accuracy >= 40) {
            return null;
        }

        return [
            'actions' => [
                ['key' => 'current_level', 'operator' => '=', 'value' => 'beginner'],
                ['key' => 'current_streak', 'operator' => '=', 'value' => 0],
                ['key' => 'wrong_streak', 'operator' => '=', 'value' => 0],
                ['key' => 'level_correct_count', 'operator' => '=', 'value' => 0],
                ['key' => 'level_attempt_count', 'operator' => '=', 'value' => 0],
                ['key' => 'retry_count', 'operator' => '+', 'value' => 1],
            ]
        ];
    }

    /**
     * Rule 7: Streak Bonus (Hint Reward)
     */
    protected function checkStreakBonus(array $state, bool $isCorrect, bool $usedHint): ?array
    {
        if (($state['current_streak'] ?? 0) !== 5) {
            return null;
        }

        return [
            'actions' => [
                ['key' => 'hints_available', 'operator' => '+', 'value' => 1],
                ['key' => 'current_streak', 'operator' => '=', 'value' => 0],
            ]
        ];
    }

    /**
     * Rule 9: Accuracy Bonus 60-74%
     */
    protected function checkAccuracyBonus60(array $state, bool $isCorrect, bool $usedHint): ?array
    {
        if (($state['total_questions_answered'] ?? 0) < 5) {
            return null;
        }

        $accuracy = $this->calculateAccuracy($state);
        if ($accuracy < 60 || $accuracy >= 75) {
            return null;
        }

        return [
            'actions' => [
                ['key' => 'xp', 'operator' => '+', 'value' => 10],
            ]
        ];
    }

    /**
     * Rule 10: Accuracy Bonus 75-84%
     */
    protected function checkAccuracyBonus75(array $state, bool $isCorrect, bool $usedHint): ?array
    {
        if (($state['total_questions_answered'] ?? 0) < 5) {
            return null;
        }

        $accuracy = $this->calculateAccuracy($state);
        if ($accuracy < 75 || $accuracy >= 85) {
            return null;
        }

        return [
            'actions' => [
                ['key' => 'xp', 'operator' => '+', 'value' => 25],
                ['key' => 'points', 'operator' => '+', 'value' => 25],
            ]
        ];
    }

    /**
     * Rule 11: Accuracy Bonus ≥85%
     */
    protected function checkAccuracyBonus85(array $state, bool $isCorrect, bool $usedHint): ?array
    {
        if (($state['total_questions_answered'] ?? 0) < 5) {
            return null;
        }

        $accuracy = $this->calculateAccuracy($state);
        if ($accuracy < 85) {
            return null;
        }

        return [
            'actions' => [
                ['key' => 'xp', 'operator' => '+', 'value' => 50],
                ['key' => 'points', 'operator' => '+', 'value' => 50],
                // Badge will be handled separately in frontend
            ]
        ];
    }

    /**
     * Rule 12: Retry Count ≥3 (Material Redirect)
     */
    protected function checkRetryRedirect(array $state, bool $isCorrect, bool $usedHint): ?array
    {
        if (($state['retry_count'] ?? 0) < 3) {
            return null;
        }

        return [
            'actions' => [
                ['key' => 'show_material_redirect', 'operator' => '=', 'value' => 1],
            ]
        ];
    }

    /**
     * Calculate accuracy percentage
     */
    protected function calculateAccuracy(array $state): float
    {
        $correct = $state['correct_count'] ?? 0;
        $total = $state['total_questions_answered'] ?? 0;

        if ($total === 0) {
            return 0;
        }

        return ($correct / $total) * 100;
    }
}
