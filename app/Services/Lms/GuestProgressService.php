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
        private readonly string $cookieName        = 'guest_progress',
        private readonly string $cookieXp          = 'guest_xp',
        private readonly string $cookieStreak      = 'guest_streak',
        private readonly string $cookiePerformance = 'guest_performance',
        private readonly int $cookieLifetime    = 60 * 24 * 30,
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
            'xp'     => (int) $xp,
            'streak' => (int) $streak,
        ];
    }

    public function saveGamificationState(int $xp, int $streak): void
    {
        $this->setCookie($this->cookieXp, (string) $xp);
        $this->setCookie($this->cookieStreak, (string) $streak);
    }

    public function getStudentState(): StudentState
    {
        $gamification = $this->getGamificationState();

        $perfData           = request()->cookie($this->cookiePerformance);
        $performanceMetrics = $perfData ? json_decode($perfData, true) : [];
        if (! is_array($performanceMetrics)) {
            $performanceMetrics = [];
        }

        $defaults = StudentStateSchema::defaults();

        return new StudentState(array_merge($defaults, [
            'user_id'         => 'guest',
            'xp'              => $gamification['xp'],
            'streak'          => $gamification['streak'],
            'level'           => 'Tamu',
            'total_answered'  => $performanceMetrics['total_answered']   ?? 0,
            'correct_count'   => $performanceMetrics['correct_count']    ?? 0,
            'wrong_count'     => $performanceMetrics['wrong_count']      ?? 0,
            'wrong_streak'    => $performanceMetrics['wrong_streak']     ?? 0,
            'hints_used'      => $performanceMetrics['hints_used']       ?? 0,
            'hints_available' => $performanceMetrics['hints_available']  ?? StudentStateSchema::DEFAULT_HINTS_AVAILABLE,
        ]));
    }

    public function saveStudentState(StudentState $state): void
    {
        $this->saveGamificationState($state->xp, $state->streak);

        $this->setCookie($this->cookiePerformance, json_encode([
            'total_answered'  => $state->total_answered,
            'correct_count'   => $state->correct_count,
            'wrong_count'     => $state->wrong_count,
            'wrong_streak'    => $state->wrong_streak,
            'hints_used'      => $state->hints_used,
            'hints_available' => $state->hints_available,
        ]));
    }

    private function setCookie(string $name, string $value): void
    {
        Cookie::queue($name, $value, $this->cookieLifetime, '/', null, false, false, 'lax');
    }

    private function deleteCookie(string $name): void
    {
        Cookie::queue(Cookie::forget($name));
    }
}
