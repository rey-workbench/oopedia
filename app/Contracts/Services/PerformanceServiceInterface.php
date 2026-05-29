<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\Quiz\InteractionDTO;
use App\DTOs\User\PerformanceScoreDTO;
use App\Models\StudentState;

interface PerformanceServiceInterface
{
    public function findOrCreateStudentState(string $userId): StudentState;

    public function updateMetricsFromInteraction(
        InteractionDTO $interactionDTO,
    ): StudentState;

    public function getStudentSessionState(string $userId): array;

    public function syncMaterialContext(string $userId, string $materialId): StudentState;

    public function calculateScore(PerformanceScoreDTO $performanceScoreDTO): int;

    public function decrementHint(string $userId): array;
}
