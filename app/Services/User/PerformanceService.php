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
use App\Enums\Lms\StudentLevel;
use App\Models\StudentState;
use App\Rules\Adaptive\Constants\PedagogicalConstants;
use App\Rules\Adaptive\Constants\StudentStateSchema;

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
                LearningStyle::VISUAL->value  => 0,
                LearningStyle::TEXTUAL->value => 0,
            ];
        }

        $category = $questionType->value === ContentCategory::SINTAKS->value
            ? LearningStyle::VISUAL->value
            : LearningStyle::TEXTUAL->value;
        $distribution[$category] = ($distribution[$category] ?? 0) + $timeSpent;

        $visualTime  = $distribution[LearningStyle::VISUAL->value]  ?? 0;
        $textualTime = $distribution[LearningStyle::TEXTUAL->value] ?? 0;
        $totalTime   = $visualTime + $textualTime;

        if ($totalTime === 0) {
            $newStyle = LearningStyle::VISUAL->value;
        } else {
            $diff     = abs($visualTime - $textualTime) / $totalTime;
            $newStyle = $diff < PedagogicalConstants::RATIO_STYLE_MIXED
                ? LearningStyle::MIXED->value
                : ($visualTime > $textualTime ? LearningStyle::VISUAL->value : LearningStyle::TEXTUAL->value);
        }

        $this->studentStateRepo->update($userId, [
            StudentStateSchema::TIME_DISTRIBUTION => $distribution,
            StudentStateSchema::LEARNING_STYLE    => $newStyle,
        ]);

        return match ($newStyle) {
            LearningStyle::VISUAL->value  => LearningStyle::VISUAL,
            LearningStyle::TEXTUAL->value => LearningStyle::TEXTUAL,
            default                       => LearningStyle::MIXED,
        };
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
        $rewards = PedagogicalConstants::SCORE_REWARDS;
        $score   = $rewards['base'];

        $score += $rewards['difficulty_bonus'][$diffKey] ?? 0;
        $allocatedTime = PedagogicalConstants::ALLOCATED_TIME[$diffKey]    ?? 60;
        if ($timeSpent > 0 && $timeSpent < ($allocatedTime / 2)) {
            $score += $rewards['time_bonus'];
        }

        if ($usedHint) {
            $score -= $rewards['hint_penalty'];
        }

        return max(0, min(100, $score));
    }

    /** Reset navigation, streak, and wrong_streak when student switches material */
    public function resetMaterialMetrics(string $userId, ?string $newMaterialId = null): StudentState
    {
        return $this->studentStateRepo->update($userId, [
            StudentStateSchema::CURRENT_STREAK    => 0, // Per-module streak isolation
            StudentStateSchema::WRONG_STREAK      => 0,
            StudentStateSchema::TARGET_DIFFICULTY => null,
            StudentStateSchema::CURRENT_MATERIAL_ID => $newMaterialId,
        ]);
    }

    public function syncMaterialContext(string $userId, string $materialId): StudentState
    {
        $state = $this->getStudentState($userId);

        if ((string) $state->current_material_id !== (string) $materialId) {
            return $this->resetMaterialMetrics($userId, $materialId);
        }

        return $state;
    }

    public function getStudentSessionState(string $userId): array
    {
        $state = $this->getStudentState($userId);

        return [
            'gamification' => [
                'global_xp'      => $state->xp,
                'current_level'  => $state->level ?? StudentLevel::PEMULA->value,
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
                'learning_style'    => $state->learning_style    ?? LearningStyle::MIXED->value,
            ],
        ];
    }
}
