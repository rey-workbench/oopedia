<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\GamificationServiceInterface;
use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Enums\Lms\ContentCategory;
use App\Enums\Lms\QuestionDifficulty;
use App\Models\Material;
use App\Models\StudentState;
use App\Rules\Adaptive\Constants\AdaptiveConstants;
use App\Schemas\StudentStateSchema;

final class PerformanceService implements PerformanceServiceInterface
{
    public function __construct(
        public readonly ProgressRepositoryInterface $progressRepo,
        public readonly GamificationServiceInterface $gamificationService,
        public readonly GuestProgressServiceInterface $guestProgressService,
    ) {}

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
        $state                                               = $this->getStudentState($userId);
        $gamification                                        = $state->gamification_data ?? [];
        $gamification[StudentStateSchema::KEY_CURRENT_LEVEL] = $level;
        $state->gamification_data                            = $gamification;

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
        $state                                           = $this->getStudentState($userId);
        $profile                                         = $state->learning_profile ?? [];
        $profile[StudentStateSchema::KEY_LEARNING_STYLE] = $style;
        $state->learning_profile                         = $profile;

        if ($userId === 'guest') {
            $this->guestProgressService->saveStudentState($state);

            return;
        }

        $state->save();
    }

    public function updateLearningStyleFromInteraction(string $userId, ContentCategory|string $questionType, int $timeSpent): string
    {
        $typeKey = $questionType instanceof ContentCategory ? $questionType->value : $questionType;
        $state   = $this->progressRepo->getOrCreateStudentState($userId);
        $profile = $state->learning_profile ?? [];

        if (! isset($profile[StudentStateSchema::KEY_TIME_DISTRIBUTION])) {
            $profile[StudentStateSchema::KEY_TIME_DISTRIBUTION] = [
                StudentStateSchema::STYLE_VISUAL  => 0,
                StudentStateSchema::STYLE_TEXTUAL => 0,
            ];
        }

        $category = $typeKey === ContentCategory::SINTAKS->value
            ? StudentStateSchema::STYLE_VISUAL
            : StudentStateSchema::STYLE_TEXTUAL;
        $profile[StudentStateSchema::KEY_TIME_DISTRIBUTION][$category] += $timeSpent;

        $visualTime  = $profile[StudentStateSchema::KEY_TIME_DISTRIBUTION][StudentStateSchema::STYLE_VISUAL]    ?? 0;
        $textualTime = $profile[StudentStateSchema::KEY_TIME_DISTRIBUTION][StudentStateSchema::STYLE_TEXTUAL]   ?? 0;
        $totalTime   = $visualTime + $textualTime;

        if ($totalTime == 0) {
            $newStyle = StudentStateSchema::STYLE_VISUAL;
        } else {
            $diff = abs($visualTime - $textualTime) / $totalTime;
            if ($diff < StudentStateSchema::RATIO_STYLE_MIXED) {
                $newStyle = StudentStateSchema::STYLE_MIXED;
            } else {
                $newStyle = $visualTime > $textualTime
                    ? StudentStateSchema::STYLE_VISUAL
                    : StudentStateSchema::STYLE_TEXTUAL;
            }
        }

        $profile[StudentStateSchema::KEY_LEARNING_STYLE] = $newStyle;
        $state->learning_profile                         = $profile;

        if ($userId === 'guest') {
            $this->guestProgressService->saveStudentState($state);
        } else {
            $state->save();
        }

        return $newStyle;
    }

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

    public function calculateAverageTimeSpent(string $userId, string $materialId): float
    {
        $attempts = $this->progressRepo->getByUserAndMaterial($userId, $materialId);

        if ($attempts->isEmpty()) {
            return 0;
        }

        $totalTime = 0;
        $count     = 0;

        foreach ($attempts as $attempt) {
            if ($attempt->time_spent > 0) {
                $totalTime += $attempt->time_spent;
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
            if ($attempt->time_spent > 0) {
                $totalSeconds += $attempt->time_spent;
            }
        }

        return round($totalSeconds / 60, 2);
    }

    public function getKnowledgeGaps(string $userId, string $materialId): array
    {
        $wrongAttempts  = $this->progressRepo->getWrongAnswers($userId, $materialId);
        $topicFrequency = [];

        foreach ($wrongAttempts as $attempt) {
            $topicFrequency['General'] = ($topicFrequency['General'] ?? 0) + 1;
        }

        arsort($topicFrequency);

        return $topicFrequency;
    }

    public function getWeakestTopic(string $userId, string $materialId): ?string
    {
        $gaps = $this->getKnowledgeGaps($userId, $materialId);

        return empty($gaps) ? null : array_key_first($gaps);
    }

    public function isFastLearner(string $userId, string $materialId, array $currentState): bool
    {
        $avgTime    = $this->calculateAverageTimeSpent($userId, $materialId);
        $accuracy   = $this->gamificationService->calculateAccuracy($currentState);
        $thresholds = StudentStateSchema::BEHAVIOURAL_THRESHOLDS['fast_learner'];

        return $avgTime > 0                             &&
            $avgTime < $thresholds['max_avg_time_mins'] &&
            $accuracy >= $thresholds['min_accuracy_pct'];
    }

    public function isFatigued(string $userId, string $materialId, array $currentState): bool
    {
        $totalTime   = $this->calculateTotalTimeSpent($userId, $materialId);
        $accuracy    = $this->gamificationService->calculateAccuracy($currentState);
        $wrongStreak = $currentState[StudentStateSchema::KEY_WRONG_STREAK] ?? 0;
        $thresholds  = StudentStateSchema::BEHAVIOURAL_THRESHOLDS['fatigue'];

        return $totalTime   >= $thresholds['min_time_mins']
            && $wrongStreak >= $thresholds['min_wrong_streak']
            && $accuracy < $thresholds['max_accuracy_pct'];
    }

    public function getCompletedMaterials(string $userId): array
    {
        $state = $this->progressRepo->getOrCreateStudentState($userId);

        return $state->learning_profile[StudentStateSchema::KEY_UNLOCKED_MODULES] ?? [];
    }

    public function markMaterialCompleted(string $userId, string $materialId): void
    {
        if ($userId === 'guest') {
            return;
        }

        $material = Material::find($materialId);
        $moduleId = $material?->module_id ?? $materialId;

        $state     = $this->getStudentState($userId);
        $profile   = $state->learning_profile                             ?? [];
        $completed = $profile[StudentStateSchema::KEY_UNLOCKED_MODULES]   ?? [];

        if (! in_array($moduleId, $completed)) {
            $completed[]                                       = $moduleId;
            $profile[StudentStateSchema::KEY_UNLOCKED_MODULES] = $completed;
            $state->learning_profile                           = $profile;
            $state->save();
        }
    }

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

    public function calculateScore(
        bool $isCorrect,
        bool $usedHint,
        int $timeSpent,
        QuestionDifficulty|string|null $difficulty = 'beginner',
    ): int {
        $diffKey = $difficulty instanceof QuestionDifficulty ? $difficulty->value : ($difficulty ?? 'beginner');
        if (! $isCorrect) {
            return 0;
        }

        $rewards = StudentStateSchema::SCORE_REWARDS;
        $score   = $rewards['base'];

        $score += $rewards['difficulty_bonus'][$diffKey]             ?? 0;
        $allocatedTime = AdaptiveConstants::ALLOCATED_TIME[$diffKey] ?? 60;
        if ($timeSpent > 0 && $timeSpent < ($allocatedTime / 2)) {
            $score += $rewards['time_bonus'];
        }

        if ($usedHint) {
            $score -= $rewards['hint_penalty'];
        }

        return max(0, min(100, $score));
    }
}
