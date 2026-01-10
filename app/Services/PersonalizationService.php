<?php

namespace App\Services;

use App\Repositories\ProgressRepository;
use Illuminate\Support\Facades\Log;

/**
 * PersonalizationService
 * 
 * Handles PERSONALIZATION ONLY (individual user characteristics)
 * Refactored to use StudentState and QuizAttempt
 */
class PersonalizationService
{
    protected $progressRepo;

    public function __construct(ProgressRepository $progressRepo)
    {
        $this->progressRepo = $progressRepo;
    }

    // ==================== PROFILE MANAGEMENT ====================

    public function getUserInitialLevel($userId, $materialId): ?string
    {
        $state = $this->progressRepo->getStudentState($userId);
        return $state->current_level;
    }

    public function setUserInitialLevel($userId, $materialId, string $level): void
    {
        $state = $this->progressRepo->getStudentState($userId);
        $state->current_level = $level;
        $state->save();
    }

    public function getUserLearningStyle($userId, $materialId): ?string
    {
        $state = $this->progressRepo->getStudentState($userId);
        return $state->learning_style;
    }

    public function setUserLearningStyle($userId, $materialId, string $style): void
    {
        $state = $this->progressRepo->getStudentState($userId);
        $state->learning_style = $style;
        $state->save();
    }

    /**
     * Update student performance counters (Strict Service Layer).
     */
    public function updateStudentPerformance($userId, bool $isCorrect, int $timeSpent = 0)
    {
        $state = $this->progressRepo->getStudentState($userId);
        $state->updatePerformance($isCorrect, $timeSpent);
        return $state;
    }

    // ==================== TIME-BASED PROFILING ====================

    public function calculateAverageTimeSpent($userId, $materialId): float
    {
        $attempts = $this->progressRepo->getByUserAndMaterial($userId, $materialId);

        if ($attempts->isEmpty()) {
            return 0;
        }

        $totalTime = 0;
        $count = 0;

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

    public function calculateTotalTimeSpent($userId, $materialId): float
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

    public function getKnowledgeGaps($userId, $materialId): array
    {
        $wrongAttempts = $this->progressRepo->getWrongAnswers($userId, $materialId);
        $topicFrequency = [];

        foreach ($wrongAttempts as $attempt) {
            // Infer topic from Question -> Material
            // Since we don't have tags, we use Material Title or Question ID as proxy?
            // Or maybe specific keywords if available. 
            // For now, let's use a dummy tag or material title.
            // Assuming $attempt->question->material exists (loaded via repo join usually)
            
            // To be safe, we need to load relationship if not loaded.
            // But repo query joins questions.
            // Let's assume we can get difficulty as a "topic" proxy for now?
            // Or if we can't get tags, we return empty or generic.
            
            // Temporary: Use difficulty as 'topic' to show *something*
            $tag = 'General';
            // We could fetch question text keywords if we wanted to be fancy.
            
            $topicFrequency[$tag] = ($topicFrequency[$tag] ?? 0) + 1;
        }

        arsort($topicFrequency);
        return $topicFrequency;
    }

    public function getWeakestTopic($userId, $materialId): ?string
    {
        $gaps = $this->getKnowledgeGaps($userId, $materialId);
        return empty($gaps) ? null : array_key_first($gaps);
    }

    // ==================== BEHAVIORAL PATTERN DETECTION ====================

    public function isFastLearner($userId, $materialId, array $currentState): bool
    {
        $avgTime = $this->calculateAverageTimeSpent($userId, $materialId);
        $accuracy = $this->calculateAccuracy($currentState);
        return $avgTime > 0 && $avgTime < 15 && $accuracy >= 90;
    }

    public function isFatigued($userId, $materialId, array $currentState): bool
    {
        $totalTime = $this->calculateTotalTimeSpent($userId, $materialId);
        $accuracy = $this->calculateAccuracy($currentState);
        $wrongStreak = $currentState['wrong_streak'] ?? 0;
        return $totalTime >= 30 && $wrongStreak >= 2 && $accuracy < 70;
    }

    // ==================== CROSS-MATERIAL TRACKING ====================

    public function getCompletedMaterials($userId): array
    {
        $state = $this->progressRepo->getStudentState($userId);
        // Assuming unlocked_modules contains list of IDs or we treat unlocked as completed
        return $state ? ($state->unlocked_modules ?? []) : [];
    }

    public function markMaterialCompleted($userId, $materialId): void
    {
        $state = $this->progressRepo->getStudentState($userId);
        $completed = $state->unlocked_modules ?? [];
        if (!in_array($materialId, $completed)) {
            $completed[] = $materialId;
            $state->unlocked_modules = $completed;
            $state->save();
        }
    }

    // ==================== HELPERS ====================

    protected function calculateAccuracy(array $state): float
    {
        $correct = $state['correct_count'] ?? 0;
        $total = $state['total_questions_answered'] ?? 0;
        return ($total === 0) ? 0 : round(($correct / $total) * 100, 2);
    }

    public function getPersonalizationProfile($userId, $materialId, array $currentState): array
    {
        return [
            'initial_level' => $this->getUserInitialLevel($userId, $materialId),
            'learning_style' => $this->getUserLearningStyle($userId, $materialId),
            'avg_time_spent' => $this->calculateAverageTimeSpent($userId, $materialId),
            'total_time_spent' => $this->calculateTotalTimeSpent($userId, $materialId),
            'is_fast_learner' => $this->isFastLearner($userId, $materialId, $currentState),
            'is_fatigued' => $this->isFatigued($userId, $materialId, $currentState),
            'weakest_topic' => $this->getWeakestTopic($userId, $materialId),
            'completed_materials' => $this->getCompletedMaterials($userId),
        ];
    }
}
