<?php

namespace App\Services\Lms;

use App\Contracts\Services\GuestProgressServiceInterface;
use Illuminate\Support\Facades\Cookie;

class GuestProgressService implements GuestProgressServiceInterface
{
    private const COOKIE_NAME = 'guest_progress';

    private const COOKIE_XP = 'guest_xp';

    private const COOKIE_STREAK = 'guest_streak';

    private const COOKIE_LIFETIME = 60 * 24 * 30;

    public function getProgress(): array
    {
        $cookie = request()->cookie(self::COOKIE_NAME);

        if (! $cookie) {
            return [];
        }

        $decoded = json_decode($cookie, true);

        return is_array($decoded) ? $decoded : [];
    }

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

        $this->setCookie(self::COOKIE_NAME, json_encode($guestProgress));

        if ($isCorrect) {
            $materialKey = $data['material_id'];
            if (! isset($guestProgress[$materialKey])) {
                $guestProgress[$materialKey] = [];
            }

            $guestProgress[$materialKey][$questionId] = [
                'is_correct'  => true,
                'answered_at' => now()->toDateTimeString(),
            ];

            $this->setCookie(self::COOKIE_NAME, json_encode($guestProgress));
        }
    }

    public function resetMaterialProgress(string $materialId): void
    {
        $allProgress = $this->getProgress();

        $filtered = collect($allProgress)
            ->filter(fn ($v, $k) => ! str_starts_with((string) $k, $materialId . '_'))
            ->except($materialId)
            ->all();

        $this->setCookie(self::COOKIE_NAME, json_encode($filtered));
    }

    public function clearAllProgress(): void
    {
        $this->deleteCookie(self::COOKIE_NAME);
        $this->deleteCookie(self::COOKIE_XP);
        $this->deleteCookie(self::COOKIE_STREAK);
    }

    public function getGamificationState(): array
    {
        $xp     = request()->cookie(self::COOKIE_XP)     ?? 0;
        $streak = request()->cookie(self::COOKIE_STREAK) ?? 0;

        return [
            'xp'     => (int) $xp,
            'streak' => (int) $streak,
        ];
    }

    public function saveGamificationState(int $xp, int $streak): void
    {
        $this->setCookie(self::COOKIE_XP, (string) $xp);
        $this->setCookie(self::COOKIE_STREAK, (string) $streak);
    }

    private function setCookie(string $name, string $value): void
    {
        Cookie::queue(
            $name,
            $value,
            self::COOKIE_LIFETIME,
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
