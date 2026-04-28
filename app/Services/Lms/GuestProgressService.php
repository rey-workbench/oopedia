<?php

declare(strict_types=1);

namespace App\Services\Lms;

use App\Contracts\Services\GuestProgressServiceInterface;
use App\Models\StudentState;
use App\Rules\Adaptive\Constants\StudentStateSchema;
use Illuminate\Support\Facades\Cookie;

final class GuestProgressService implements GuestProgressServiceInterface
{
    public function __construct(
        private readonly string $cookieName        = 'guest_progress',
        private readonly string $cookieXp          = 'guest_xp',
        private readonly string $cookieStreak      = 'guest_streak',
        private readonly string $cookiePerformance = 'guest_performance',
        private readonly int $cookieLifetime       = 60 * 24 * 30,
    ) {}

    /** @return array<string, mixed> */
    public function getProgress(): array
    {
        $cookie = request()->cookie($this->cookieName);

        if (! $cookie) {
            return [];
        }

        $decoded = json_decode((string) $cookie, true);

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
        $performanceMetrics = $perfData ? json_decode((string) $perfData, true) : [];
        if (! is_array($performanceMetrics)) {
            $performanceMetrics = [];
        }

        $defaults = StudentStateSchema::defaults();

        return new StudentState(array_merge($defaults, [
            'user_id'                             => 'guest',
            StudentStateSchema::XP                => $gamification['xp'],
            StudentStateSchema::STREAK            => $gamification['streak'],
            StudentStateSchema::LEVEL             => 'Tamu',
            StudentStateSchema::TOTAL_ANSWERED    => $performanceMetrics[StudentStateSchema::TOTAL_ANSWERED]    ?? 0,
            StudentStateSchema::CORRECT_COUNT     => $performanceMetrics[StudentStateSchema::CORRECT_COUNT]     ?? 0,
            StudentStateSchema::WRONG_COUNT       => $performanceMetrics[StudentStateSchema::WRONG_COUNT]       ?? 0,
            StudentStateSchema::HINTS_USED        => $performanceMetrics[StudentStateSchema::HINTS_USED]        ?? 0,
            StudentStateSchema::HINTS_AVAILABLE   => $performanceMetrics[StudentStateSchema::HINTS_AVAILABLE]   ?? 3,
            StudentStateSchema::TARGET_DIFFICULTY => $performanceMetrics[StudentStateSchema::TARGET_DIFFICULTY] ?? null,
        ]));
    }

    public function saveStudentState(StudentState $state): void
    {
        $this->saveGamificationState((int) $state->xp, (int) $state->streak);

        $this->setCookie($this->cookiePerformance, json_encode([
            StudentStateSchema::TOTAL_ANSWERED    => $state->total_answered,
            StudentStateSchema::CORRECT_COUNT     => $state->correct_count,
            StudentStateSchema::WRONG_COUNT       => $state->wrong_count,
            StudentStateSchema::HINTS_USED        => $state->hints_used,
            StudentStateSchema::HINTS_AVAILABLE   => $state->hints_available,
            StudentStateSchema::TARGET_DIFFICULTY => $state->target_difficulty,
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
