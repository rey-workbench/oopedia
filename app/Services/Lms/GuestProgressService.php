<?php

declare(strict_types=1);

namespace App\Services\Lms;

use App\Contracts\Services\GuestProgressServiceInterface;
use App\Models\StudentState;
use App\Schemas\StudentStateSchema;
use Illuminate\Support\Facades\Cookie;

final class GuestProgressService implements GuestProgressServiceInterface
{
    public function __construct(
        private readonly string $cookieName = 'guest_progress',
        private readonly string $cookieXp = 'guest_xp',
        private readonly string $cookieStreak = 'guest_streak',
        private readonly string $cookieAdaptive = 'guest_adaptive',
        private readonly string $cookiePerformance = 'guest_performance',
        private readonly int $cookieLifetime = 60 * 24 * 30,
    ) {}

    /** @return array<string, mixed> */
    public function getProgress(): array
    {
        $cookie = request()->cookie($this->cookieName);

        if (! $cookie) {
            return [];
        }

        $decoded = json_decode($cookie, true);

        return is_array($decoded) ? $decoded : [];
    }

    /** @param array<string, mixed> $data */
    public function saveProgress(array $data, bool $isCorrect, string $questionId): void
    {
        $guestProgress = $this->getProgress();

        $progressKey                 = $data['material_id'] . '_' . $questionId;
        $guestProgress[$progressKey] = [
            'is_correct'     => $isCorrect,
            'attempt_number' => isset($guestProgress[$progressKey])
                ? $guestProgress[$progressKey]['attempt_number'] + 1
                : 1,
        ];

        $this->setCookie($this->cookieName, json_encode($guestProgress));

        if ($isCorrect) {
            $materialKey = $data['material_id'];
            if (! isset($guestProgress[$materialKey])) {
                $guestProgress[$materialKey] = [];
            }

            $guestProgress[$materialKey][$questionId] = [
                'is_correct'  => true,
                'answered_at' => now()->toDateTimeString(),
            ];

            $this->setCookie($this->cookieName, json_encode($guestProgress));
        }
    }

    public function resetMaterialProgress(string $materialId): void
    {
        $allProgress = $this->getProgress();

        $filtered = collect($allProgress)
            ->filter(fn ($v, $k) => ! str_starts_with((string) $k, $materialId . '_'))
            ->except($materialId)
            ->all();

        $this->setCookie($this->cookieName, json_encode($filtered));
    }

    public function clearAllProgress(): void
    {
        $this->deleteCookie($this->cookieName);
        $this->deleteCookie($this->cookieXp);
        $this->deleteCookie($this->cookieStreak);
    }

    /** @return array<string, int> */
    public function getGamificationState(): array
    {
        $xp     = request()->cookie($this->cookieXp)     ?? 0;
        $streak = request()->cookie($this->cookieStreak) ?? 0;

        return [
            StudentStateSchema::KEY_GLOBAL_XP      => (int) $xp,
            StudentStateSchema::KEY_CURRENT_STREAK => (int) $streak,
        ];
    }

    public function saveGamificationState(int $xp, int $streak): void
    {
        $this->setCookie($this->cookieXp, (string) $xp);
        $this->setCookie($this->cookieStreak, (string) $streak);
    }

    public function getStudentState(): StudentState
    {
        $xp     = request()->cookie($this->cookieXp)     ?? 0;
        $streak = request()->cookie($this->cookieStreak) ?? 0;

        $adaptiveData  = request()->cookie($this->cookieAdaptive);
        $adaptiveState = $adaptiveData ? json_decode($adaptiveData, true) : [];
        if (! is_array($adaptiveState)) {
            $adaptiveState = [];
        }

        $perfData           = request()->cookie($this->cookiePerformance);
        $performanceMetrics = $perfData ? json_decode($perfData, true) : [];
        if (! is_array($performanceMetrics)) {
            $performanceMetrics = [];
        }

        $gamification                                         = StudentStateSchema::getDefaultGamification();
        $gamification[StudentStateSchema::KEY_GLOBAL_XP]      = (int) $xp;
        $gamification[StudentStateSchema::KEY_CURRENT_STREAK] = (int) $streak;
        $gamification[StudentStateSchema::KEY_CURRENT_LEVEL]  = 'Tamu';

        return new StudentState([
            'user_id'             => 'guest',
            'gamification_data'   => $gamification,
            'learning_profile'    => StudentStateSchema::getDefaultLearningProfile(),
            'performance_metrics' => array_merge(
                StudentStateSchema::getDefaultPerformanceMetrics(),
                $performanceMetrics,
            ),
            'adaptive_state'      => array_merge(
                StudentStateSchema::getDefaultAdaptiveState(),
                $adaptiveState,
            ),
        ]);
    }

    public function saveStudentState(StudentState $state): void
    {
        $gamification = $state->gamification_data;
        $xp           = $gamification[StudentStateSchema::KEY_GLOBAL_XP]      ?? 0;
        $streak       = $gamification[StudentStateSchema::KEY_CURRENT_STREAK] ?? 0;

        $this->saveGamificationState((int) $xp, (int) $streak);

        if ($state->adaptive_state) {
            $this->setCookie($this->cookieAdaptive, json_encode($state->adaptive_state));
        }

        if ($state->performance_metrics) {
            $this->setCookie($this->cookiePerformance, json_encode($state->performance_metrics));
        }
    }

    private function setCookie(string $name, string $value): void
    {
        Cookie::queue(
            $name,
            $value,
            $this->cookieLifetime,
            '/',
            null,
            false,
            false,
            'lax',
        );
    }

    private function deleteCookie(string $name): void
    {
        Cookie::queue(Cookie::forget($name));
    }
}
