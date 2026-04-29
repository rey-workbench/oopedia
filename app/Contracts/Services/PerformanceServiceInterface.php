<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\Quiz\InteractionDTO;
use App\Enums\Lms\QuestionDifficulty;
use App\Models\StudentState;

interface PerformanceServiceInterface
{
    public function getStudentState(string $userId): StudentState;

    public function updateMetricsFromInteraction(
        InteractionDTO $interaction,
    ): StudentState;

    public function getStudentSessionState(string $userId): array;

    public function syncMaterialContext(string $userId, string $materialId): StudentState;

    public function calculateScore(bool $isCorrect, bool $usedHint, int $timeSpent, QuestionDifficulty|string $difficulty): int;
}
