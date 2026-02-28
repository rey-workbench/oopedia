<?php

namespace App\Contracts\Services;

interface QuizRewardServiceInterface
{
    /** @return array<string, mixed> */
    public function calculateCorrectAnswerReward(array $state, bool $usedHint = false, string $difficulty = 'beginner', int $timeSpent = 0): array;

    /** @return array<string, mixed> */
    public function processWrongAnswer(array $state): array;

    /** @return array<string, mixed> */
    public function useHint(array $state): array;

    public function calculateAccuracy(array $state): float;
}
