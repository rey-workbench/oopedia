<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Models\StudentState;

interface GuestProgressServiceInterface
{
    public function getProgress(): array;

    public function saveProgress(array $data, bool $isCorrect, string $questionId): void;

    public function resetMaterialProgress(string $materialId): void;

    public function clearAllProgress(): void;

    public function getGamificationState(): array;

    public function saveGamificationState(int $xp, int $streak): void;

    public function getStudentState(): StudentState;

    public function getStudentSessionState(): array;

    public function saveStudentState(StudentState $studentState): void;
}
