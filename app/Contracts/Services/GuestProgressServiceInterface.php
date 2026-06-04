<?php

declare(strict_types=1);

namespace App\Contracts\Services;

interface GuestProgressServiceInterface
{
    public function getProgress(): array;

    public function saveProgress(array $data, bool $isCorrect, string $questionId): void;

    public function resetMaterialProgress(string $materialId): void;

    public function clearAllProgress(): void;

    public function getGamificationState(): array;
}
