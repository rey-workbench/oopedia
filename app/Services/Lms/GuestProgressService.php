<?php

namespace App\Services\Lms;

use App\Contracts\Services\GuestProgressServiceInterface;

class GuestProgressService implements GuestProgressServiceInterface
{
    private const SESSION_KEY = 'guest_progress';
    private const SESSION_XP_KEY = 'guest_xp';
    private const SESSION_STREAK_KEY = 'guest_streak';

    /**
     * Get the guest's progress data from session.
     */
    public function getProgress(): array
    {
        return session(self::SESSION_KEY, []);
    }

    /**
     * Save progress for a specific question.
     */
    public function saveProgress(array $data, bool $isCorrect, string $questionId): void
    {
        $guestProgress = $this->getProgress();

        $progressKey = $data['material_id'] . '_' . $questionId;
        $guestProgress[$progressKey] = [
            'is_correct' => $isCorrect,
            'attempt_number' => isset($guestProgress[$progressKey])
            ? $guestProgress[$progressKey]['attempt_number'] + 1
            : 1,
        ];

        session([self::SESSION_KEY => $guestProgress]);

        if ($isCorrect) {
            $materialSessionKey = self::SESSION_KEY . '.' . $data['material_id'];
            if (!session()->has($materialSessionKey)) {
                session([$materialSessionKey => []]);
            }

            $currentProgress = session($materialSessionKey, []);
            $currentProgress[$questionId] = [
                'is_correct' => true,
                'answered_at' => now()->toDateTimeString(),
            ];

            session([$materialSessionKey => $currentProgress]);
        }
    }

    /**
     * Reset progress for a specific material.
     */
    public function resetMaterialProgress(string $materialId): void
    {
        $allProgress = $this->getProgress();

        $filtered = collect($allProgress)
            ->filter(fn($v, $k) => !str_starts_with((string)$k, $materialId . '_'))
            ->all();

        session([self::SESSION_KEY => $filtered]);
        session()->forget(self::SESSION_KEY . '.' . $materialId);
    }

    /**
     * Clear all guest progress.
     */
    public function clearAllProgress(): void
    {
        session()->forget(self::SESSION_KEY);
        session()->forget(self::SESSION_XP_KEY);
        session()->forget(self::SESSION_STREAK_KEY);
    }

    /**
     * Get the current XP and Streak from session.
     */
    public function getGamificationState(): array
    {
        return [
            'xp' => session(self::SESSION_XP_KEY, 0),
            'streak' => session(self::SESSION_STREAK_KEY, 0),
        ];
    }

    /**
     * Save the new XP and Streak to session.
     */
    public function saveGamificationState(int $xp, int $streak): void
    {
        session([self::SESSION_XP_KEY => $xp]);
        session([self::SESSION_STREAK_KEY => $streak]);
    }
}
