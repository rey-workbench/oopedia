<?php

namespace App\Services\User;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\GamificationServiceInterface;
use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Models\Material;
use App\Models\StudentState;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

/**
 * PerformanceService
 *
 * Handles PERSONALIZATION ONLY (individual user characteristics)
 * Refactored to use StudentState and QuizAttempt
 */
class PerformanceService implements PerformanceServiceInterface
{
    // ==================== SCORE CALCULATION CONSTANTS ====================
    /** Base score for a correct answer */
    private const SCORE_BASE = 80;

    /** Bonus points for answering quickly (G05) */
    private const SCORE_TIME_BONUS = 10;

    /** Penalty for using a hint */
    private const SCORE_HINT_PENALTY = 20;

    /** Extra points per difficulty level */
    private const SCORE_DIFFICULTY_BONUS = ['hard' => 10, 'medium' => 5];

    // ==================== BEHAVIOURAL DETECTION CONSTANTS ====================
    /** Average time (minutes) below which a student is considered a fast learner */
    private const FAST_LEARNER_MAX_AVG_TIME = 15;

    /** Minimum accuracy (%) required alongside fast time to confirm fast-learner */
    private const FAST_LEARNER_MIN_ACCURACY = 90;

    /** Total session time (minutes) threshold for fatigue detection */
    private const FATIGUE_MIN_TIME = 30;

    /** Wrong-answer streak threshold for fatigue detection */
    private const FATIGUE_MIN_WRONG_STREAK = 2;

    /** Accuracy (%) below which a fatigued student is flagged */
    private const FATIGUE_MAX_ACCURACY = 70;

    // ==================== LEARNING STYLE CONSTANTS ====================
    /** If |visual − textual| / total < this ratio, the style is 'mixed' */
    private const STYLE_MIXED_THRESHOLD = 0.20;

    public function __construct(
        protected ProgressRepositoryInterface $progressRepo,
        protected GamificationServiceInterface $gamificationService,
        protected GuestProgressServiceInterface $guestProgressService,
    ) {
    }

    // ==================== PROFILE MANAGEMENT ====================

    public function getStudentState(string $userId): StudentState
    {
        if ($userId === 'guest') {
            return $this->guestProgressService->getStudentState();
        }

        return $this->progressRepo->getOrCreateStudentState($userId);
    }

    public function getUserInitialLevel(string $userId, string $materialId): ?string
    {
        $state = $this->progressRepo->getOrCreateStudentState($userId);

        return $state->current_level;
    }

    public function setUserInitialLevel(string $userId, string $materialId, string $level): void
    {
        $state                         = $this->getStudentState($userId);
        $gamification                  = $state->gamification_data ?? [];
        $gamification['current_level'] = $level;
        $state->gamification_data      = $gamification;

        if ($userId === 'guest') {
            $this->guestProgressService->saveStudentState($state);
        } else {
            $state->save();
        }
    }

    public function getUserLearningStyle(string $userId, string $materialId): ?string
    {
        $state = $this->progressRepo->getOrCreateStudentState($userId);

        return $state->learning_style;
    }

    public function setUserLearningStyle(string $userId, string $materialId, string $style): void
    {
        $state                     = $this->getStudentState($userId);
        $profile                   = $state->learning_profile ?? [];
        $profile['learning_style'] = $style;
        $state->learning_profile   = $profile;

        if ($userId === 'guest') {
            $this->guestProgressService->saveStudentState($state);
        } else {
            $state->save();
        }
    }

    /**
     * Update learning style based on real-time interaction.
     * Returns 'visual', 'textual', or 'mixed' (G07, G08, G27).
     */
    public function updateLearningStyleFromInteraction(string $userId, string $questionType, int $timeSpent): string
    {
        $state   = $this->progressRepo->getOrCreateStudentState($userId);
        $profile = $state->learning_profile ?? [];

        // Initialize time distribution if not exists
        if (! isset($profile['time_distribution'])) {
            $profile['time_distribution'] = ['visual' => 0, 'textual' => 0];
        }

        // Teori → Textual; Studi Kasus / Sintaks → Visual
        $category = ($questionType === 'teori') ? 'textual' : 'visual';
        $profile['time_distribution'][$category] += $timeSpent;

        // Recalculate dominant style (with mixed detection)
        $visualTime  = $profile['time_distribution']['visual'];
        $textualTime = $profile['time_distribution']['textual'];
        $totalTime   = $visualTime + $textualTime;

        if ($totalTime == 0) {
            $newStyle = 'visual';
        } else {
            $diff = abs($visualTime - $textualTime) / $totalTime;
            if ($diff < self::STYLE_MIXED_THRESHOLD) {
                $newStyle = 'mixed';
            } else {
                $newStyle = $visualTime > $textualTime ? 'visual' : 'textual';
            }
        }

        $profile['learning_style'] = $newStyle;
        $state->learning_profile   = $profile;

        if ($userId === 'guest') {
            $this->guestProgressService->saveStudentState($state);
        } else {
            $state->save();
        }

        return $newStyle;
    }

    /**
     * Update student performance counters (Strict Service Layer).
     */
    public function updateStudentPerformance(
        string $userId,
        bool $isCorrect,
        int $timeSpent = 0,
        bool $usedHint = false,
    ): StudentState {
        $state = $this->getStudentState($userId);
        $state->updatePerformance($isCorrect, $timeSpent, $usedHint);

        if ($userId === 'guest') {
            $this->guestProgressService->saveStudentState($state);
        }

        return $state;
    }

    // ==================== TIME-BASED PROFILING ====================

    public function calculateAverageTimeSpent(string $userId, string $materialId): float
    {
        $attempts = $this->progressRepo->getByUserAndMaterial($userId, $materialId);

        if ($attempts->isEmpty()) {
            return 0;
        }

        $totalTime = 0;
        $count     = 0;

        foreach ($attempts as $attempt) {
            // QuizAttempt has time_spent
            $timeSpent = $attempt->time_spent;
            if ($timeSpent > 0) {
                $totalTime += $timeSpent;
                $count++;
            }
        }

        return $count > 0 ? round($totalTime / $count, 2) : 0;
    }

    public function calculateTotalTimeSpent(string $userId, string $materialId): float
    {
        $attempts = $this->progressRepo->getByUserAndMaterial($userId, $materialId);

        $totalSeconds = 0;
        foreach ($attempts as $attempt) {
            $timeSpent = $attempt->time_spent;
            if ($timeSpent > 0) {
                $totalSeconds += $timeSpent;
            }
        }

        return round($totalSeconds / 60, 2); // Minutes
    }

    // ==================== KNOWLEDGE GAP ANALYSIS ====================

    /** @return array<string, int> */
    public function getKnowledgeGaps(string $userId, string $materialId): array
    {
        $wrongAttempts  = $this->progressRepo->getWrongAnswers($userId, $materialId);
        $topicFrequency = [];

        foreach ($wrongAttempts as $attempt) {
            // Temporary: Use difficulty as 'topic' to show *something*
            $tag = 'General';

            $topicFrequency[$tag] = ($topicFrequency[$tag] ?? 0) + 1;
        }

        arsort($topicFrequency);

        return $topicFrequency;
    }

    public function getWeakestTopic(string $userId, string $materialId): ?string
    {
        $gaps = $this->getKnowledgeGaps($userId, $materialId);

        return empty($gaps) ? null : array_key_first($gaps);
    }

    // ==================== BEHAVIORAL PATTERN DETECTION ====================

    public function isFastLearner(string $userId, string $materialId, array $currentState): bool
    {
        $avgTime  = $this->calculateAverageTimeSpent($userId, $materialId);
        $accuracy = $this->gamificationService->calculateAccuracy($currentState);

        return $avgTime > 0                            &&
            $avgTime < self::FAST_LEARNER_MAX_AVG_TIME &&
            $accuracy >= self::FAST_LEARNER_MIN_ACCURACY;
    }

    public function isFatigued(string $userId, string $materialId, array $currentState): bool
    {
        $totalTime   = $this->calculateTotalTimeSpent($userId, $materialId);
        $accuracy    = $this->gamificationService->calculateAccuracy($currentState);
        $wrongStreak = $currentState['wrong_streak'] ?? 0;

        return $totalTime   >= self::FATIGUE_MIN_TIME
            && $wrongStreak >= self::FATIGUE_MIN_WRONG_STREAK
            && $accuracy < self::FATIGUE_MAX_ACCURACY;
    }

    // ==================== CROSS-MATERIAL TRACKING ====================

    /** @return array<int, int> */
    public function getCompletedMaterials(string $userId): array
    {
        $state = $this->progressRepo->getOrCreateStudentState($userId);

        return $state ? ($state->unlocked_modules ?? []) : [];
    }

    public function markMaterialCompleted(string $userId, string $materialId): void
    {
        if ($userId === 'guest') {
            return;
        }

        // Resolve module_id from Material — unlocked_modules tracks MODULE ids,
        // not material ids, which is what getUnlockStatusFacts() checks.
        $material = Material::find($materialId);
        $moduleId = $material?->module_id ?? $materialId;

        $state     = $this->getStudentState($userId);
        $profile   = $state->learning_profile     ?? [];
        $completed = $profile['unlocked_modules'] ?? [];

        if (! in_array($moduleId, $completed)) {
            $completed[]                 = $moduleId;
            $profile['unlocked_modules'] = $completed;
            $state->learning_profile     = $profile;
            $state->save();
        }
    }

    // ==================== HELPERS ====================

    /** @return array<string, mixed> */
    public function getPersonalizationProfile(string $userId, string $materialId, array $currentState): array
    {
        return [
            'initial_level'       => $this->getUserInitialLevel($userId, $materialId),
            'learning_style'      => $this->getUserLearningStyle($userId, $materialId),
            'avg_time_spent'      => $this->calculateAverageTimeSpent($userId, $materialId),
            'total_time_spent'    => $this->calculateTotalTimeSpent($userId, $materialId),
            'is_fast_learner'     => $this->isFastLearner($userId, $materialId, $currentState),
            'is_fatigued'         => $this->isFatigued($userId, $materialId, $currentState),
            'weakest_topic'       => $this->getWeakestTopic($userId, $materialId),
            'completed_materials' => $this->getCompletedMaterials($userId),
        ];
    }

    /**
     * Calculate nuanced score based on correctness, hint usage, time, and difficulty.
     * Aligned with Rule Base facts (G01-G04, G05-G06).
     */
    public function calculateScore(
        bool $isCorrect,
        bool $usedHint,
        int $timeSpent,
        ?string $difficulty = 'beginner',
    ): int {
        if (! $isCorrect) {
            return 0;
        }

        $score = self::SCORE_BASE;

        // Difficulty bonus
        $score += self::SCORE_DIFFICULTY_BONUS[$difficulty] ?? 0;

        // Time bonus (G05: answered in < 50% of allocated time)
        $allocatedTime = AdaptiveConstants::ALLOCATED_TIME[$difficulty] ?? 60;
        if ($timeSpent > 0 && $timeSpent < ($allocatedTime / 2)) {
            $score += self::SCORE_TIME_BONUS;
        }

        // Hint penalty
        if ($usedHint) {
            $score -= self::SCORE_HINT_PENALTY;
        }

        return max(0, min(100, $score));
    }
}
