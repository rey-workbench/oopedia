<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Contracts\Repositories\StudentStateRepositoryInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\DTOs\Quiz\InteractionDTO;
use App\Enums\Lms\QuestionDifficulty;
use App\Models\StudentState;
use App\Rules\Adaptive\Constants\PedagogicalConstants;
use App\Rules\Adaptive\Constants\StudentStateSchema;
use Illuminate\Support\Carbon;

final class PerformanceService implements PerformanceServiceInterface
{
    public function __construct(
        private readonly StudentStateRepositoryInterface $studentStateRepo,
    ) {}

    public function getStudentState(string $userId): StudentState
    {
        return $this->studentStateRepo->findOrCreate($userId);
    }

    public function updateMetricsFromInteraction(InteractionDTO $interaction): StudentState
    {
        $userId     = $interaction->userId;
        $questionId = $interaction->questionId;
        $isCorrect  = $interaction->isCorrect;
        $timeSpent  = $interaction->timeSpent;
        $difficulty = $interaction->difficulty;
        $usedHint   = $interaction->usedHint;
        $score      = $interaction->score;

        $state = $this->getStudentState($userId);

        $currentSession = $state->current_session     ?? StudentStateSchema::defaults()[StudentStateSchema::CURRENT_SESSION];
        $metrics        = $state->performance_metrics ?? StudentStateSchema::defaults()[StudentStateSchema::PERFORMANCE_METRICS];

        $questionIds = $currentSession['question_ids'] ?? [];
        $xp          = $state->xp                      ?? 0;

        // Jika user mengulang pertanyaan yang sama di sesi ini, abaikan perhitungan gandanya
        if (in_array($questionId, $questionIds)) {
            // Kita kembalikan state tanpa perubahan metrics
            return $state;
        }

        $questionIds[]                  = $questionId;
        $currentSession['question_ids'] = $questionIds;
        $hintsAvailable                 = $state->hints_available ?? StudentStateSchema::defaults()[StudentStateSchema::HINTS_AVAILABLE];

        // 1. Update Session Counts
        $currentSession['total']++;
        if ($isCorrect) {
            $currentSession['correct']++;
        }
        if ($usedHint) {
            $currentSession['hints']++;
            $hintsAvailable = max(0, $hintsAvailable - 1);
        }
        $currentSession['time_spent'] += $timeSpent;

        // 2. Update cumulative counts (tidak terpengaruh reset sesi)
        $totalAnswered = ($state->total_answered ?? 0) + 1;
        $correctCount  = ($state->correct_count ?? 0)  + ($isCorrect ? 1 : 0);
        $hintsUsed     = ($state->hints_used ?? 0)     + ($usedHint ? 1 : 0);

        // Akurasi = kumulatif global, bukan sesi mini
        $accuracy = round(($correctCount / $totalAnswered) * 100, 2);

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
            $metrics['stagnant_count'] = $this->calculateStagnantCount($sessionHistory);

            // Reset current session
            $currentSession = [
                'correct'      => 0,
                'total'        => 0,
                'hints'        => 0,
                'time_spent'   => 0,
                'question_ids' => [],
            ];
        }

        // 5. Daily Streak Logic
        $lastActive = $state->last_active_at ? Carbon::parse($state->last_active_at) : null;
        $newStreak  = $state->streak     ?? 0;
        $maxStreak  = $state->max_streak ?? 0;

        if (! $lastActive || ! $lastActive->isToday()) {
            if ($lastActive && $lastActive->isYesterday()) {
                $newStreak += 1;
            } else {
                $newStreak = 1;
            }
            $maxStreak = max($newStreak, $maxStreak);
        }

        $xp += $score;

        // 6. Persistence
        return $this->studentStateRepo->update($userId, [
            StudentStateSchema::XP                  => $xp,
            StudentStateSchema::ACCURACY            => $accuracy,
            'total_answered'                        => $totalAnswered,
            'correct_count'                         => $correctCount,
            StudentStateSchema::HINTS_USED          => $hintsUsed,
            StudentStateSchema::CURRENT_SESSION     => $currentSession,
            StudentStateSchema::SESSION_HISTORY     => $sessionHistory,
            StudentStateSchema::PERFORMANCE_METRICS => $metrics,
            StudentStateSchema::STREAK              => $newStreak,
            StudentStateSchema::MAX_STREAK          => $maxStreak,
            StudentStateSchema::HINTS_AVAILABLE     => $hintsAvailable,
            'last_active_at'                        => now(),
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

    private function calculateStagnantCount(array $history): int
    {
        if (count($history) < 2) {
            return 0;
        }

        $stagnantCount = 0;
        $margin        = PedagogicalConstants::TREND_MARGIN;

        // Loop dari sesi terbaru (belakang) ke sesi terlama (depan)
        for ($i = count($history) - 1; $i > 0; $i--) {
            $selisih = $history[$i] - $history[$i - 1];

            // if -5% <= selisih <= +5%
            if ($selisih >= -$margin && $selisih <= $margin) {
                $stagnantCount++;
            } else {
                // Pola stagnan terputus
                break;
            }
        }

        return $stagnantCount;
    }

    public function getStudentSessionState(string $userId): array
    {
        $state = $this->getStudentState($userId);

        return [
            'accuracy'            => round($state->accuracy, 2),
            'xp'                  => $state->xp,
            'streak'              => $state->streak,
            'level'               => $state->level ?? 'Beginner',
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
