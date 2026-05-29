<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Contracts\Repositories\StudentStateRepositoryInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\DTOs\Quiz\InteractionDTO;
use App\Enums\Lms\QuestionDifficulty;
use App\Enums\Lms\StudentLevel;
use App\Enums\User\RoleName;
use App\Models\StudentState;
use App\Rules\Adaptive\Constants\PedagogicalConstants;
use App\Rules\Adaptive\Constants\StudentStateSchema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;

final readonly class PerformanceService implements PerformanceServiceInterface
{
    public function __construct(
        private StudentStateRepositoryInterface $studentStateRepository,
    ) {
    }

    public function findOrCreateStudentState(string $userId): StudentState
    {
        return $this->studentStateRepository->findOrCreate($userId);
    }

    public function updateMetricsFromInteraction(InteractionDTO $interactionDTO): StudentState
    {
        $userId = $interactionDTO->userId;
        $questionId = $interactionDTO->questionId;
        $isCorrect = $interactionDTO->isCorrect;
        $timeSpent = $interactionDTO->timeSpent;
        $difficulty = $interactionDTO->difficulty;
        $usedHint = $interactionDTO->usedHint;
        $score = $interactionDTO->score;

        $studentState = $this->findOrCreateStudentState($userId);

        $currentSession = $studentState->current_session ?? StudentStateSchema::defaults()[StudentStateSchema::CURRENT_SESSION];
        $metrics = $studentState->performance_metrics ?? StudentStateSchema::defaults()[StudentStateSchema::PERFORMANCE_METRICS];

        $questionIds = $currentSession['question_ids'] ?? [];
        $xp = $studentState->xp ?? 0;

        // Jika user mengulang pertanyaan yang sama di sesi ini, abaikan perhitungan gandanya
        // Prevent double counting within the same mini-session to avoid spamming
        // However, we allow it if the current state seems stagnant or if it's a legitimate retry
        if (in_array($questionId, $questionIds) && $isCorrect && $studentState->correct_count > 0) {
            $lastInteraction = $studentState->current_session['last_question_id'] ?? null;
            if ($lastInteraction === $questionId) {
                return $studentState;
            }
        }

        $questionIds[] = $questionId;
        $currentSession['question_ids'] = $questionIds;
        $hintsAvailable = $studentState->hints_available ?? StudentStateSchema::defaults()[StudentStateSchema::HINTS_AVAILABLE];

        // 1. Update Session Counts
        $currentSession['total']++;
        if ($isCorrect) {
            $currentSession['correct']++;
        }

        if ($usedHint) {
            $currentSession['hints']++;
        }

        $currentSession['time_spent'] += $timeSpent;

        // 2. Update cumulative counts (tidak terpengaruh reset sesi)
        $totalAnswered = ($studentState->total_answered ?? 0) + 1;
        $correctCount = ($studentState->correct_count ?? 0) + ($isCorrect ? 1 : 0);
        $hintsUsed = ($studentState->hints_used ?? 0);

        // Akurasi = kumulatif global, bukan sesi mini
        $accuracy = round(($correctCount / $totalAnswered) * 100, 2);

        // 3. Speed Analysis (vs Baseline)
        $baseline = PedagogicalConstants::BASELINE_TIME[$difficulty->value] ?? 30;
        $speed = 'normal';
        if ($timeSpent > ($baseline * 2)) {
            $speed = 'slow';
        } elseif ($timeSpent < ($baseline / 2)) {
            $speed = 'fast';
        }

        $metrics['speed'] = $speed;
        $metrics['last_result'] = $isCorrect;
        $metrics['last_response_time'] = $timeSpent;
        $metrics['last_used_hint'] = $usedHint;

        // 4. Session Buffer Logic (Every 5 questions, record session and reset)
        $sessionHistory = $studentState->session_history ?? [0, 0, 0];
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
                'correct' => 0,
                'total' => 0,
                'hints' => 0,
                'time_spent' => 0,
                'question_ids' => [],
            ];
        }

        // 5. Daily Streak Logic
        $lastActive = $studentState->last_active_at ? Date::parse($studentState->last_active_at) : null;
        $newStreak = $studentState->streak ?? 0;
        $maxStreak = $studentState->max_streak ?? 0;

        if (!$lastActive instanceof Carbon || !$lastActive->isToday()) {
            if ($lastActive instanceof Carbon && $lastActive->isYesterday()) {
                $newStreak += 1;
            } else {
                $newStreak = 1;
            }

            $maxStreak = max($newStreak, $maxStreak);
        }

        $xp += $score;

        // 6. Persistence
        return $this->studentStateRepository->update($userId, [
            StudentStateSchema::XP => $xp,
            StudentStateSchema::ACCURACY => $accuracy,
            'total_answered' => $totalAnswered,
            'correct_count' => $correctCount,
            StudentStateSchema::HINTS_USED => $hintsUsed,
            StudentStateSchema::CURRENT_SESSION => $currentSession,
            StudentStateSchema::SESSION_HISTORY => $sessionHistory,
            StudentStateSchema::PERFORMANCE_METRICS => $metrics,
            StudentStateSchema::STREAK => $newStreak,
            StudentStateSchema::MAX_STREAK => $maxStreak,
            StudentStateSchema::HINTS_AVAILABLE => $hintsAvailable,
            'last_active_at' => now(),
        ]);
    }

    private function calculateTrend(array $history): string
    {
        if (count($history) < 3) {
            return 'stable';
        }

        $n = count($history);
        $sesiN = $history[$n - 1];
        $sesiN1 = $history[$n - 2];
        $sesiN2 = $history[$n - 3];

        $delta1 = $sesiN1 - $sesiN2;
        $delta2 = $sesiN - $sesiN1;
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
        $margin = PedagogicalConstants::TREND_MARGIN;

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
        $studentState = $this->findOrCreateStudentState($userId);

        return [
            'gamification' => [
                'xp' => $studentState->xp,
                'level' => $studentState->level ?? StudentLevel::PEMULA->value,
                'streak' => $studentState->streak,
                'badges' => $studentState->badges ?? [],
            ],
            'performance' => [
                'total_answered' => $studentState->total_answered,
                'correct_count' => $studentState->correct_count,
                'accuracy' => round((float) $studentState->accuracy, 2),
                'hints_used' => $studentState->hints_used,
            ],
            'hints_available' => $studentState->hints_available,
            'adaptive_engine' => [
                'session_history' => $studentState->session_history ?? [],
                'current_session' => $studentState->current_session ?? [],
                'performance_metrics' => $studentState->performance_metrics ?? [],
                'adaptive_state' => $studentState->adaptive_state ?? [],
            ],
        ];
    }

    public function syncMaterialContext(string $userId, string $materialId): StudentState
    {
        $studentState = $this->findOrCreateStudentState($userId);
        $studentState->current_material_id = $materialId;
        if ($userId !== RoleName::GUEST->value) {
            $studentState->save();
        }

        return $studentState;
    }

    public function calculateScore(\App\DTOs\User\PerformanceScoreDTO $performanceScoreDTO): int
    {
        if (!$performanceScoreDTO->isCorrect) {
            return 0;
        }

        $baseScore = 10;

        $difficultyValue = $performanceScoreDTO->difficulty instanceof QuestionDifficulty ? $performanceScoreDTO->difficulty->value : $performanceScoreDTO->difficulty;
        $multiplier = match ($difficultyValue) {
            'medium' => 1.5,
            'hard' => 2.0,
            default => 1.0,
        };

        $score = $baseScore * $multiplier;

        if ($performanceScoreDTO->usedHint) {
            $score -= 2;
        }

        // Time penalty for taking > 2x baseline
        $baseline = PedagogicalConstants::BASELINE_TIME[$difficultyValue] ?? 30;
        if ($performanceScoreDTO->timeSpent > ($baseline * 2)) {
            $score -= 1;
        }

        return (int) max(5, $score);
    }

    public function decrementHint(string $userId): array
    {
        $studentState = $this->findOrCreateStudentState($userId);

        if ($studentState->hints_available > 0) {
            $studentState->hints_available--;
            $studentState->hints_used++;
            if ($userId !== RoleName::GUEST->value) {
                $studentState->save();
            }
        }

        return $this->getStudentSessionState($userId);
    }
}
