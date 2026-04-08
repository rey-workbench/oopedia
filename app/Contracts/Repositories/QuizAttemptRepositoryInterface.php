<?php

namespace App\Contracts\Repositories;

use App\Models\QuizAttempt;
use Illuminate\Database\Eloquent\Collection;

interface QuizAttemptRepositoryInterface
{
    public function create(array $data): QuizAttempt;

    public function find(string $id): ?QuizAttempt;

    public function getByUser(string $userId): Collection;

    public function getByUserAndQuestion(string $userId, string $questionId): Collection;

    public function getByMaterial(string $materialId): Collection;

    public function getBestAttempt(string $userId, string $questionId): ?QuizAttempt;

    public function getLatestAttempt(string $userId, string $questionId): ?QuizAttempt;

    public function countAttempts(string $userId, string $questionId): int;

    public function getCorrectAttempts(string $userId): Collection;

    public function getUserStats(string $userId): array;
}
