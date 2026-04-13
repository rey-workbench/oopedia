<?php

namespace App\Contracts\Repositories;

use App\Models\StudentState;
use Illuminate\Database\Eloquent\Collection;

interface StudentStateRepositoryInterface
{
    public function getByUserAndMaterial(string $userId, string $materialId): ?StudentState;

    public function getAll(string $userId): Collection;

    public function findByUserId(string $userId): ?StudentState;

    public function findOrCreate(string $userId): StudentState;

    public function update(string $userId, array $data): StudentState;

    public function delete(string $userId, string $materialId): bool;
}
