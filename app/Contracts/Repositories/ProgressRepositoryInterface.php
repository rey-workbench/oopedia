<?php

namespace App\Contracts\Repositories;

use App\Models\QuizAttempt;
use App\Models\StudentState;
use Illuminate\Support\Collection;

interface ProgressRepositoryInterface
{
    public function getUserProgressStats(?string $userId): Collection;

    public function getUserMaterialProgress(?string $userId): Collection;

    public function getRecentActivities(?string $userId, int $limit = 5): Collection;

    public function getDetailedUserProgress(?string $userId): Collection;

    public function getCorrectAnswersWithAttempts(string $roleName): Collection;

    public function getLeaderboardStats(string $roleName): Collection;

    public function saveProgress(array $data): QuizAttempt;

    public function getStudentState(?string $userId): ?StudentState;

    public function getOrCreateStudentState(?string $userId): StudentState;

    public function getAnsweredQuestionIds(string $userId, string $materialId): Collection;

    public function resetProgress(string $userId, string $materialId): void;

    public function getStudentCountByMaterial(): Collection;

    public function getRecentSystemProgress(int $limit): Collection;

    public function getMaterialPerformanceStats(): Collection;

    public function getPopularMaterials(int $limit): Collection;

    public function getLastAccessTime(?string $userId, string $materialId): ?string;

    public function getLatestAttemptsForQuestions(string $userId, array $questionIds): Collection;
}
