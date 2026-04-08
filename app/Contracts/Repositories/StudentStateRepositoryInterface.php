<?php

namespace App\Contracts\Repositories;

use App\Models\StudentState;
use Illuminate\Database\Eloquent\Collection;

interface StudentStateRepositoryInterface
{
    public function upsert(string $userId, string $materialId, array $attributes): StudentState;

    public function getByUserAndMaterial(string $userId, string $materialId): ?StudentState;

    public function updateProgress(string $userId, string $materialId, array $progressData): void;

    public function getAll(string $userId): Collection;

    public function delete(string $userId, string $materialId): bool;
}
