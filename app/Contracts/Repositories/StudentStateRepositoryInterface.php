<?php

namespace App\Contracts\Repositories;

use App\Models\StudentState;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contract for StudentState data access.
 */
interface StudentStateRepositoryInterface
{
    public function upsert(int $userId, int $materialId, array $attributes): StudentState;

    public function getByUserAndMaterial(int $userId, int $materialId): ?StudentState;

    public function updateProgress(int $userId, int $materialId, array $progressData): void;

    /** @return Collection<int, StudentState> */
    public function getAll(int $userId): Collection;

    public function delete(int $userId, int $materialId): bool;
}
