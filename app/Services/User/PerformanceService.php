<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Contracts\Repositories\StudentStateRepositoryInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Enums\Lms\QuestionDifficulty;
use App\Models\StudentState;
use App\Rules\Adaptive\Constants\PedagogicalConstants;
use App\Rules\Adaptive\Constants\StudentStateSchema;

final class PerformanceService implements PerformanceServiceInterface
{
    public function __construct(
        private readonly StudentStateRepositoryInterface $studentStateRepo,
    ) {}

    public function getStudentState(string $userId): StudentState
    {
        return $this->studentStateRepo->findOrCreate($userId);
    }

    public function updateMetricsFromInteraction(
        string $userId,
        bool $isCorrect,
        int $timeSpent,
        QuestionDifficulty $difficulty,
        bool $usedHint,
    ): StudentState {
        $state = $this->getStudentState($userId);

        $currentSession = $state->current_session     ?? StudentStateSchema::defaults()[StudentStateSchema::CURRENT_SESSION];
        $metrics        = $state->performance_metrics ?? StudentStateSchema::defaults()[StudentStateSchema::PERFORMANCE_METRICS];

        // 1. Update Session Counts
        $currentSession['total']++;
        if ($isCorrect) {
            $currentSession['correct']++;
        }
        if ($usedHint) {
            $currentSession['hints']++;
        }
        $currentSession['time_spent'] += $timeSpent;

        // 2. Calculate Current Accuracy
        $accuracy = ($currentSession['correct'] / $currentSession['total']) * 100;

        // 3. Speed Analysis (vs Baseline)
        $baseline = PedagogicalConstants::BASELINE_TIME[$difficulty->value] ?? 30;
        $speed    = 'normal';
        if ($timeSpent > ($baseline * 2)) {
            $speed = 'slow';
        } elseif ($timeSpent < ($baseline / 2)) {
            $speed = 'fast';
        }
        $metrics['speed'] = $speed;

        // 4. Session Buffer Logic (Every 5 questions, record session and reset)
        $sessionHistory = $state->session_history ?? [0, 0, 0];
        if ($currentSession['total'] >= 5) {
            $sessionAccuracy = ($currentSession['correct'] / $currentSession['total']) * 100;

            // Update History (Slide)
            $sessionHistory[] = $sessionAccuracy;
            if (count($sessionHistory) > 5) {
                array_shift($sessionHistory);
            }

            // Trend Analysis (Last 3 sessions)
            $metrics['trend'] = $this->calculateTrend($sessionHistory);

            // Stagnancy Detection (Boredom Diagnosis)
            if ($this->isStagnant($sessionHistory)) {
                $metrics['stagnant_count']++;
            } else {
                $metrics['stagnant_count'] = 0;
            }

            // Reset current session
            $currentSession = [
                'correct'    => 0,
                'total'      => 0,
                'hints'      => 0,
                'time_spent' => 0,
            ];
        }

        // 5. Persistence
        return $this->studentStateRepo->update($userId, [
            StudentStateSchema::ACCURACY            => $accuracy,
            StudentStateSchema::CURRENT_SESSION     => $currentSession,
            StudentStateSchema::SESSION_HISTORY     => $sessionHistory,
            StudentStateSchema::PERFORMANCE_METRICS => $metrics,
        ]);
    }

    private function calculateTrend(array $history): string
    {
        if (count($history) < 3) {
            return 'stable';
        }

        $n      = count($history);
        $sesiN  = $history[$n - 1];
        $sesiN1 = $history[$n - 2];
        $sesiN2 = $history[$n - 3];

        $delta1 = $sesiN1 - $sesiN2;
        $delta2 = $sesiN  - $sesiN1;
        $margin = PedagogicalConstants::TREND_MARGIN;

        if ($delta1 > $margin && $delta2 > $margin) {
            return 'up';
        }
        if ($delta1 < -$margin && $delta2 < -$margin) {
            return 'down';
        }

        return 'stable';
    }

    private function isStagnant(array $history): bool
    {
        if (count($history) < 3) {
            return false;
        }
        $last3 = array_slice($history, -3);
        $max   = max($last3);
        $min   = min($last3);

        return ($max - $min) < PedagogicalConstants::TREND_MARGIN;
    }

    public function getStudentSessionState(string $userId): array
    {
        $state = $this->getStudentState($userId);

        return [
            'accuracy'            => round($state->accuracy, 2),
            'xp'                  => $state->xp,
            'streak'              => $state->streak,
            'hints_available'     => $state->hints_available,
            'target_difficulty'   => $state->target_difficulty,
            'adaptive_state'      => $state->adaptive_state      ?? [],
            'performance_metrics' => $state->performance_metrics ?? [],
        ];
    }

    public function syncMaterialContext(string $userId, string $materialId): StudentState
    {
        $state                      = $this->getStudentState($userId);
        $state->current_material_id = $materialId;
        $state->save();

        return $state;
    }

    public function calculateScore(bool $isCorrect, bool $usedHint, int $timeSpent, QuestionDifficulty|string $difficulty): int
    {
        if (! $isCorrect) {
            return 0;
        }

        $baseScore = 10;

        $difficultyValue = $difficulty instanceof QuestionDifficulty ? $difficulty->value : $difficulty;
        $multiplier      = match ($difficultyValue) {
            'medium' => 1.5,
            'hard'   => 2.0,
            default  => 1.0,
        };

        $score = $baseScore * $multiplier;

        if ($usedHint) {
            $score -= 2;
        }

        // Time penalty for taking > 2x baseline
        $baseline = PedagogicalConstants::BASELINE_TIME[$difficultyValue] ?? 30;
        if ($timeSpent > ($baseline * 2)) {
            $score -= 1;
        }

        return (int) max(5, $score);
    }
}
