<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\StudentStateRepositoryInterface;
use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Enums\Lms\ContentCategory;
use App\Enums\Lms\LearningStyle;
use App\Enums\Lms\QuestionDifficulty;
use App\Models\StudentState;
use App\Rules\Adaptive\Constants\AdaptiveConstants as AC;

final class PerformanceService implements PerformanceServiceInterface
{
    public function __construct(
        public readonly ProgressRepositoryInterface $progressRepo,
        public readonly StudentStateRepositoryInterface $studentStateRepo,
        public readonly GuestProgressServiceInterface $guestProgressService,
    ) {}

    public function getStudentState(string $userId): StudentState
    {
        if ($userId === 'guest') {
            return $this->guestProgressService->getStudentState();
        }

        return $this->studentStateRepo->findOrCreate($userId);
    }

    public function updateLearningStyleFromInteraction(string $userId, ContentCategory $questionType, int $timeSpent): LearningStyle
    {
        $state = $this->getStudentState($userId);

        $distribution = $state->time_distribution ?? [];
        if (empty($distribution)) {
            $distribution = [
                AC::STYLE_VISUAL  => 0,
                AC::STYLE_TEXTUAL => 0,
            ];
        }

        $category = $questionType->value === ContentCategory::SINTAKS->value
            ? AC::STYLE_VISUAL
            : AC::STYLE_TEXTUAL;
        $distribution[$category] = ($distribution[$category] ?? 0) + $timeSpent;

        $visualTime  = $distribution[AC::STYLE_VISUAL]  ?? 0;
        $textualTime = $distribution[AC::STYLE_TEXTUAL] ?? 0;
        $totalTime   = $visualTime + $textualTime;

        if ($totalTime === 0) {
            $newStyle = AC::STYLE_VISUAL;
        } else {
            $diff     = abs($visualTime - $textualTime) / $totalTime;
            $newStyle = $diff < AC::RATIO_STYLE_MIXED
                ? AC::STYLE_MIXED
                : ($visualTime > $textualTime ? AC::STYLE_VISUAL : AC::STYLE_TEXTUAL);
        }

        $this->studentStateRepo->update($userId, [
            'time_distribution' => $distribution,
            'learning_style'    => $newStyle,
        ]);

        return match ($newStyle) {
            AC::STYLE_VISUAL                  => LearningStyle::VISUAL,
            AC::STYLE_TEXTUAL                 => LearningStyle::TEXTUAL,
            default                           => LearningStyle::MIXED,
        };
    }

    public function updateStudentPerformance(
        string $userId,
        bool $isCorrect,
        int $timeSpent = 0,
        bool $usedHint = false,
    ): StudentState {
        $state = $this->getStudentState($userId);

        $updates = [
            'total_answered' => $state->total_answered + 1,
            'last_active_at' => now(),
        ];

        if ($usedHint) {
            $updates['hints_used']      = $state->hints_used + 1;
            $updates['hints_available'] = max(0, $state->hints_available - 1);
        }

        if ($isCorrect) {
            $updates['correct_count'] = $state->correct_count + 1;
            $updates['streak']        = $state->streak        + 1;
            $updates['max_streak']    = max($state->max_streak, $state->streak + 1);
            $updates['wrong_streak']  = 0;
        } else {
            $updates['wrong_count']  = $state->wrong_count  + 1;
            $updates['wrong_streak'] = $state->wrong_streak + 1;
            $updates['streak']       = 0;
        }

        return $this->studentStateRepo->update($userId, $updates);
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
        QuestionDifficulty $difficulty,
    ): int {
        if (! $isCorrect) {
            return 0;
        }

        $diffKey = $difficulty->value;
        $rewards = AC::SCORE_REWARDS;
        $score   = $rewards['base'];

        $score += $rewards['difficulty_bonus'][$diffKey]             ?? 0;
        $allocatedTime = AC::ALLOCATED_TIME[$diffKey]                ?? 60;
        if ($timeSpent > 0 && $timeSpent < ($allocatedTime / 2)) {
            $score += $rewards['time_bonus'];
        }

        if ($usedHint) {
            $score -= $rewards['hint_penalty'];
        }

        return max(0, min(100, $score));
    }

    /** Reset navigation + wrong_streak when student switches material */
    public function resetMaterialMetrics(string $userId): StudentState
    {
        return $this->studentStateRepo->update($userId, [
            'wrong_streak'        => 0,
            'target_difficulty'   => null,
            'current_material_id' => null,
        ]);
    }

    public function getStudentSessionState(string $userId): array
    {
        $state = $this->getStudentState($userId);

        return [
            'gamification' => [
                'global_xp'      => $state->xp,
                'current_level'  => $state->level ?? 'Pemula',
                'current_streak' => $state->streak,
                'max_streak'     => $state->max_streak,
            ],
            'performance' => [
                'total_questions_answered' => $state->total_answered,
                'correct_count'            => $state->correct_count,
                'wrong_count'              => $state->wrong_count,
                'hints_available'          => $state->hints_available,
            ],
            'adaptive' => [
                'fast_track_active' => $state->fast_track_active ?? false,
                'learning_style'    => $state->learning_style ?? AC::STYLE_MIXED,
            ],
        ];
    }
}
