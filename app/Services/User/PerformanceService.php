<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\StudentStateRepositoryInterface;
use App\Contracts\Services\GamificationServiceInterface;
use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Enums\Lms\ContentCategory;
use App\Enums\Lms\QuestionDifficulty;
use App\Models\StudentState;
use App\Rules\Adaptive\Constants\AdaptiveConstants;
use App\Schemas\StudentStateSchema;

final class PerformanceService implements PerformanceServiceInterface
{
    public function __construct(
        public readonly ProgressRepositoryInterface $progressRepo,
        public readonly StudentStateRepositoryInterface $studentStateRepo,
        public readonly GamificationServiceInterface $gamificationService,
        public readonly GuestProgressServiceInterface $guestProgressService,
    ) {}

    public function getStudentState(string $userId): StudentState
    {
        if ($userId === 'guest') {
            return $this->guestProgressService->getStudentState();
        }

        return $this->studentStateRepo->findOrCreate($userId);
    }

    public function updateLearningStyleFromInteraction(string $userId, ContentCategory|string $questionType, int $timeSpent): string
    {
        $typeKey = $questionType instanceof ContentCategory ? $questionType->value : $questionType;
        $state   = $this->getStudentState($userId);
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

        $this->studentStateRepo->update($userId, [
            'learning_profile' => $profile,
        ]);

        return $newStyle;
    }

    public function updateStudentPerformance(
        string $userId,
        bool $isCorrect,
        int $timeSpent = 0,
        bool $usedHint = false,
    ): StudentState {
        $state = $this->getStudentState($userId);

        $metrics      = $state->performance_metrics ?? [];
        $gamification = $state->gamification_data   ?? [];

        $metrics[StudentStateSchema::KEY_TOTAL_QUESTIONS_ANSWERED] =
            ($metrics[StudentStateSchema::KEY_TOTAL_QUESTIONS_ANSWERED] ?? 0) + 1;

        if ($usedHint) {
            $metrics[StudentStateSchema::KEY_HINTS_USED_COUNT] =
                ($metrics[StudentStateSchema::KEY_HINTS_USED_COUNT] ?? 0) + 1;
            $metrics[StudentStateSchema::KEY_HINTS_AVAILABLE] = max(
                0,
                ($metrics[StudentStateSchema::KEY_HINTS_AVAILABLE] ?? StudentStateSchema::DEFAULT_HINTS_AVAILABLE) - 1,
            );
        }

        if ($isCorrect) {
            $metrics[StudentStateSchema::KEY_CORRECT_COUNT] =
                ($metrics[StudentStateSchema::KEY_CORRECT_COUNT] ?? 0) + 1;
            $gamification[StudentStateSchema::KEY_CURRENT_STREAK] =
                ($gamification[StudentStateSchema::KEY_CURRENT_STREAK] ?? 0) + 1;
            $gamification[StudentStateSchema::KEY_MAX_STREAK] = max(
                $gamification[StudentStateSchema::KEY_MAX_STREAK]     ?? 0,
                $gamification[StudentStateSchema::KEY_CURRENT_STREAK] ?? 0,
            );
            $metrics[StudentStateSchema::KEY_WRONG_STREAK] = 0;
        } else {
            $metrics[StudentStateSchema::KEY_WRONG_COUNT] =
                ($metrics[StudentStateSchema::KEY_WRONG_COUNT] ?? 0) + 1;
            $metrics[StudentStateSchema::KEY_WRONG_STREAK] =
                ($metrics[StudentStateSchema::KEY_WRONG_STREAK] ?? 0) + 1;
            $gamification[StudentStateSchema::KEY_CURRENT_STREAK] = 0;
        }

        $state->performance_metrics = $metrics;
        $state->gamification_data   = $gamification;
        $state->last_active_at      = now();

        $this->studentStateRepo->update($userId, [
            'performance_metrics' => $metrics,
            'gamification_data'   => $gamification,
            'last_active_at'      => now(),
        ]);

        return $state;
    }

    public function useHint(string $userId, int $count = 1): StudentState
    {
        $state   = $this->getStudentState($userId);
        $metrics = $state->performance_metrics ?? [];

        $metrics[StudentStateSchema::KEY_HINTS_USED_COUNT] = (
            $metrics[StudentStateSchema::KEY_HINTS_USED_COUNT] ?? 0
        ) + $count;

        $metrics[StudentStateSchema::KEY_HINTS_AVAILABLE] = max(
            0,
            ($metrics[StudentStateSchema::KEY_HINTS_AVAILABLE] ?? StudentStateSchema::DEFAULT_HINTS_AVAILABLE) - $count
        );

        $state->performance_metrics = $metrics;

        $this->studentStateRepo->update($userId, [
            'performance_metrics' => $metrics,
        ]);

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

    public function resetMaterialMetrics(string $userId, array $adaptiveState): StudentState
    {
        $state   = $this->getStudentState($userId);
        $metrics = $state->performance_metrics ?? [];
        $metrics[StudentStateSchema::KEY_WRONG_STREAK] = 0;

        return $this->studentStateRepo->update($userId, [
            'adaptive_state'      => $adaptiveState,
            'performance_metrics' => $metrics,
        ]);
    }
}
