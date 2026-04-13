<?php

namespace App\Contracts\Repositories;

use App\Models\QuizAttempt;
use Illuminate\Database\Eloquent\Collection;

interface QuizAttemptRepositoryInterface
{
    public function create(array $data): QuizAttempt;

    public function find(string $id): ?QuizAttempt;

    public function getByUser(string $userId): Collection;

    public function getByMaterial(string $materialId): Collection;
}
