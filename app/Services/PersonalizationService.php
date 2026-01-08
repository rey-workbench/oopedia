<?php

namespace App\Services;

use App\Repositories\Interfaces\ProgressRepositoryInterface;
use Illuminate\Support\Facades\Log;

/**
 * PersonalizationService
 * 
 * Handles PERSONALIZATION ONLY (individual user characteristics)
 * - Initial level and learning style management
 * - Time-based profiling (average time spent)
 * - Knowledge gap analysis (weak topics per user)
 * - Cross-material progress tracking
 * - Behavioral pattern detection (fast learner, fatigued, etc.)
 */
class PersonalizationService
{
    protected $progressRepo;

    public function __construct(ProgressRepositoryInterface $progressRepo)
    {
        $this->progressRepo = $progressRepo;
    }

    // ==================== PROFILE MANAGEMENT ====================

    /**
     * Get user's initial level from their first progress record
     */
    public function getUserInitialLevel($userId, $materialId): ?string
    {
        $firstProgress = $this->progressRepo->getFirstProgress($userId, $materialId);
        return $firstProgress?->getInitialLevel();
    }

    /**
     * Set user's initial level (on first progress record)
     */
    public function setUserInitialLevel($userId, $materialId, string $level): void
    {
        $firstProgress = $this->progressRepo->getFirstProgress($userId, $materialId);
        
        if ($firstProgress) {
            $firstProgress->setInitialLevel($level);
            $firstProgress->save();
        }
    }

    /**
     * Get user's learning style
     */
    public function getUserLearningStyle($userId, $materialId): ?string
    {
        $firstProgress = $this->progressRepo->getFirstProgress($userId, $materialId);
        return $firstProgress?->getLearningStyle();
    }

    /**
     * Set user's learning style (on first progress record)
     */
    public function setUserLearningStyle($userId, $materialId, string $style): void
    {
        $firstProgress = $this->progressRepo->getFirstProgress($userId, $materialId);
        
        if ($firstProgress) {
            $firstProgress->setLearningStyle($style);
            $firstProgress->save();
        }
    }

    // ==================== TIME-BASED PROFILING ====================

    /**
     * Calculate average time spent per question for a user in a material
     * Returns float (seconds)
     */
    public function calculateAverageTimeSpent($userId, $materialId): float
    {
        $progressRecords = $this->progressRepo->getByUserAndMaterial($userId, $materialId);

        if ($progressRecords->isEmpty()) {
            return 0;
        }

        $totalTime = 0;
        $count = 0;

        foreach ($progressRecords as $progress) {
            $timeSpent = $progress->getTimeSpent();
            if ($timeSpent !== null && $timeSpent > 0) {
                $totalTime += $timeSpent;
                $count++;
            }
        }

        return $count > 0 ? round($totalTime / $count, 2) : 0;
    }

    /**
     * Calculate total time spent by user on a material (in minutes)
     */
    public function calculateTotalTimeSpent($userId, $materialId): float
    {
        $progressRecords = $this->progressRepo->getByUserAndMaterial($userId, $materialId);

        $totalSeconds = 0;

        foreach ($progressRecords as $progress) {
            $timeSpent = $progress->getTimeSpent();
            if ($timeSpent !== null && $timeSpent > 0) {
                $totalSeconds += $timeSpent;
            }
        }

        return round($totalSeconds / 60, 2); // Convert to minutes
    }

    // ==================== KNOWLEDGE GAP ANALYSIS ====================

    /**
     * Get knowledge gaps based on wrong answers by topic
     * Returns array of [topic => error_count] sorted by frequency
     */
    public function getKnowledgeGaps($userId, $materialId): array
    {
        $wrongAnswers = $this->progressRepo->getWrongAnswers($userId, $materialId);

        $topicFrequency = [];

        foreach ($wrongAnswers as $progress) {
            $tags = $progress->getTopicTags();
            foreach ($tags as $tag) {
                $topicFrequency[$tag] = ($topicFrequency[$tag] ?? 0) + 1;
            }
        }

        // Sort by frequency (most problematic topics first)
        arsort($topicFrequency);

        return $topicFrequency;
    }

    /**
     * Get most problematic topic for a user
     */
    public function getWeakestTopic($userId, $materialId): ?string
    {
        $gaps = $this->getKnowledgeGaps($userId, $materialId);
        
        if (empty($gaps)) {
            return null;
        }

        return array_key_first($gaps);
    }

    // ==================== BEHAVIORAL PATTERN DETECTION ====================

    /**
     * Detect if user is a "Fast Learner" based on speed and accuracy
     */
    public function isFastLearner($userId, $materialId, array $currentState): bool
    {
        $avgTime = $this->calculateAverageTimeSpent($userId, $materialId);
        $accuracy = $this->calculateAccuracy($currentState);

        // Fast learner criteria: avg_time < 15s AND accuracy >= 90%
        return $avgTime > 0 && $avgTime < 15 && $accuracy >= 90;
    }

    /**
     * Detect if user is experiencing fatigue
     */
    public function isFatigued($userId, $materialId, array $currentState): bool
    {
        $totalTime = $this->calculateTotalTimeSpent($userId, $materialId);
        $accuracy = $this->calculateAccuracy($currentState);
        $wrongStreak = $currentState['wrong_streak'] ?? 0;

        // Fatigue criteria: total_time > 30min AND wrong_streak >= 2 AND accuracy < 70%
        return $totalTime >= 30 && $wrongStreak >= 2 && $accuracy < 70;
    }

    // ==================== CROSS-MATERIAL TRACKING ====================

    /**
     * Get all completed materials for a user
     */
    public function getCompletedMaterials($userId): array
    {
        $latestProgress = $this->progressRepo->getLatestProgress($userId);
        return $latestProgress?->getCompletedMaterials() ?? [];
    }

    /**
     * Mark a material as completed
     */
    public function markMaterialCompleted($userId, $materialId): void
    {
        $latestProgress = $this->progressRepo->getLatestProgress($userId);

        if ($latestProgress) {
            $latestProgress->addCompletedMaterial($materialId);
            $latestProgress->save();
        }
    }

    // ==================== HELPERS ====================

    /**
     * Calculate accuracy percentage (helper)
     */
    protected function calculateAccuracy(array $state): float
    {
        $correct = $state['correct_count'] ?? 0;
        $total = $state['total_questions_answered'] ?? 0;

        if ($total === 0) {
            return 0;
        }

        return round(($correct / $total) * 100, 2);
    }

    /**
     * Get personalization profile summary for a user
     */
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
