<?php

namespace App\Services\User;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Models\StudentState;

/**
 * PerformanceService
 *
 * Handles PERSONALIZATION ONLY (individual user characteristics)
 * Refactored to use StudentState and QuizAttempt
 */
class PerformanceService implements PerformanceServiceInterface
{
    public function __construct(
        protected ProgressRepositoryInterface $progressRepo,
    ) {}

    // ==================== PROFILE MANAGEMENT ====================

    public function getStudentState(int $userId): StudentState
    {
        return $this->progressRepo->getOrCreateStudentState($userId);
    }

    public function getUserInitialLevel(int $userId, int $materialId): ?string
    {
        $state = $this->progressRepo->getOrCreateStudentState($userId);

        return $state->current_level;
    }

    public function setUserInitialLevel(int $userId, int $materialId, string $level): void
    {
        $state                = $this->progressRepo->getOrCreateStudentState($userId);
        $state->current_level = $level;
        $state->save();
    }

    public function getUserLearningStyle(int $userId, int $materialId): ?string
    {
        $state = $this->progressRepo->getOrCreateStudentState($userId);

        return $state->learning_style;
    }

    public function setUserLearningStyle(int $userId, int $materialId, string $style): void
    {
        $state                     = $this->progressRepo->getOrCreateStudentState($userId);
        $profile                   = $state->learning_profile ?? [];
        $profile['learning_style'] = $style;
        $state->learning_profile   = $profile;
        $state->save();
    }

    /**
     * Update learning style based on real-time interaction
     */
    public function updateLearningStyleFromInteraction(int $userId, string $questionType, int $timeSpent): string
    {
        $state   = $this->progressRepo->getOrCreateStudentState($userId);
        $profile = $state->learning_profile ?? [];

        // Initialize time distribution if not exists
        if (! isset($profile['time_distribution'])) {
            $profile['time_distribution'] = ['visual' => 0, 'textual' => 0];
        }

        // Map question type to learning style category
        // Teori -> Textual
        // Studi Kasus, Sintaks -> Visual (Diagrams, Code Structures)
        $category = ($questionType === 'teori') ? 'textual' : 'visual';

        // Update time
        $profile['time_distribution'][$category] += $timeSpent;

        // Recalculate dominant style
        $visualTime  = $profile['time_distribution']['visual'];
        $textualTime = $profile['time_distribution']['textual'];

        $newStyle = ($visualTime > $textualTime) ? 'visual' : 'textual';

        // Update profile
        $profile['learning_style'] = $newStyle;
        $state->learning_profile   = $profile;
        $state->save();

        return $newStyle;
    }

    /**
     * Update student performance counters (Strict Service Layer).
     */
    public function updateStudentPerformance(int $userId, bool $isCorrect, int $timeSpent = 0, bool $usedHint = false): StudentState
    {
        $state = $this->progressRepo->getOrCreateStudentState($userId);
        $state->updatePerformance($isCorrect, $timeSpent, $usedHint);

        return $state;
    }

    // ==================== TIME-BASED PROFILING ====================

    public function calculateAverageTimeSpent(int $userId, int $materialId): float
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

    public function calculateTotalTimeSpent(int $userId, int $materialId): float
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
    public function getKnowledgeGaps(int $userId, int $materialId): array
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

    public function getWeakestTopic(int $userId, int $materialId): ?string
    {
        $gaps = $this->getKnowledgeGaps($userId, $materialId);

        return empty($gaps) ? null : array_key_first($gaps);
    }

    // ==================== BEHAVIORAL PATTERN DETECTION ====================

    public function isFastLearner(int $userId, int $materialId, array $currentState): bool
    {
        $avgTime  = $this->calculateAverageTimeSpent($userId, $materialId);
        $accuracy = $this->calculateAccuracy($currentState);

        return $avgTime > 0 && $avgTime < 15 && $accuracy >= 90;
    }

    public function isFatigued(int $userId, int $materialId, array $currentState): bool
    {
        $totalTime   = $this->calculateTotalTimeSpent($userId, $materialId);
        $accuracy    = $this->calculateAccuracy($currentState);
        $wrongStreak = $currentState['wrong_streak'] ?? 0;

        return $totalTime >= 30 && $wrongStreak >= 2 && $accuracy < 70;
    }

    // ==================== CROSS-MATERIAL TRACKING ====================

    /** @return array<int, int> */
    public function getCompletedMaterials(int $userId): array
    {
        $state = $this->progressRepo->getOrCreateStudentState($userId);

        return $state ? ($state->unlocked_modules ?? []) : [];
    }

    public function markMaterialCompleted(int $userId, int $materialId): void
    {
        $state     = $this->progressRepo->getOrCreateStudentState($userId);
        $completed = $state->unlocked_modules ?? [];
        if (! in_array($materialId, $completed)) {
            $completed[]             = $materialId;
            $state->unlocked_modules = $completed;
            $state->save();
        }
    }

    // ==================== HELPERS ====================

    protected function calculateAccuracy(array $state): float
    {
        $correct = $state['correct_count']            ?? 0;
        $total   = $state['total_questions_answered'] ?? 0;

        return ($total === 0) ? 0 : round(($correct / $total) * 100, 2);
    }

    /** @return array<string, mixed> */
    public function getPersonalizationProfile(int $userId, int $materialId, array $currentState): array
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
    public function calculateScore(bool $isCorrect, bool $usedHint, int $timeSpent, ?string $difficulty = 'beginner'): int
    {
        if (! $isCorrect) {
            return 0;
        }

        // Base score for correct answer
        $score = 80;

        // Difficulty multiplier
        $score += match ($difficulty) {
            'hard'   => 10,
            'medium' => 5,
            default  => 0,
        };

        // Time bonus (G05: < 50% of allocated time)
        $allocatedTimeMap = [
            'beginner' => 45,
            'medium'   => 90,
            'hard'     => 150,
            'final'    => 300,
        ];
        $allocatedTime = $allocatedTimeMap[$difficulty] ?? 60;

        if ($timeSpent > 0 && $timeSpent < ($allocatedTime / 2)) {
            $score += 10;
        }

        // Hint penalty
        if ($usedHint) {
            $score -= 20;
        }

        return max(0, min(100, $score));
    }
}
