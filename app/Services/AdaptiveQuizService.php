<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Progress;
use App\Rules\AdaptiveQuizRules;
use App\Repositories\Interfaces\ProgressRepositoryInterface;
use Illuminate\Support\Facades\Log;

class AdaptiveQuizService
{
    protected $progressRepo;
    protected $adaptiveRules;

    public function __construct(
        ProgressRepositoryInterface $progressRepo,
        AdaptiveQuizRules $adaptiveRules
    ) {
        $this->progressRepo = $progressRepo;
        $this->adaptiveRules = $adaptiveRules;
    }

    /**
     * Process a quiz attempt and apply adaptive rules
     */
    public function processAttempt($userId, $materialId, Question $question, $isCorrect, $usedHint = false)
    {
        // 1. Get current state from latest progress
        $latestProgress = Progress::where('user_id', $userId)
            ->where('material_id', $materialId)
            ->latest()
            ->first();
        
        // Initialize or load state
        $state = $latestProgress ? ($latestProgress->attributes ?? []) : [];
        $state = $this->initializeDefaults($state);

        // 2. Evaluate all adaptive rules
        $result = $this->adaptiveRules->evaluateAll($state, $isCorrect, $usedHint);

        // 3. Calculate earned rewards
        $xpEarned = ($result['new_state']['xp'] ?? 0) - ($state['xp'] ?? 0);
        $pointsEarned = ($result['new_state']['points'] ?? 0) - ($state['points'] ?? 0);

        // 4. Log triggered rules
        if (!empty($result['triggered_rules'])) {
            Log::info('Adaptive rules triggered', [
                'user_id' => $userId,
                'material_id' => $materialId,
                'question_id' => $question->id,
                'is_correct' => $isCorrect,
                'used_hint' => $usedHint,
                'triggered_rules' => $result['triggered_rules'],
            ]);
        }

        // 5. Return results
        return [
            'status' => 'success',
            'is_correct' => $isCorrect,
            'xp_earned' => max(0, $xpEarned), // Ensure non-negative
            'points_earned' => max(0, $pointsEarned),
            'new_state' => $result['new_state'],
            'actions_applied' => $result['actions_report'],
            'triggered_rules' => $result['triggered_rules'],
            'level_changed' => in_array('current_level', array_column($result['actions_report'], 'key')),
            'show_material_redirect' => ($result['new_state']['show_material_redirect'] ?? 0) == 1,
        ];
    }

    /**
     * Initialize default values for state attributes
     */
    protected function initializeDefaults(array $state): array
    {
        $defaults = [
            'xp' => 0,
            'points' => 0,
            'current_level' => 'beginner',
            'current_streak' => 0,
            'wrong_streak' => 0,
            'level_correct_count' => 0,
            'level_attempt_count' => 0,
            'hints_used' => 0,
            'hints_available' => 0,
            'retry_count' => 0,
            'total_questions_answered' => 0,
            'correct_count' => 0,
            'wrong_count' => 0,
            'show_material_redirect' => 0,
        ];

        return array_merge($defaults, $state);
    }
}
