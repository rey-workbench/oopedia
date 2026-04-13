<?php

namespace App\Contracts\Repositories;

use App\Models\StudentState;
use Illuminate\Database\Eloquent\Collection;

interface StudentStateRepositoryInterface
{
    public function getByUserAndMaterial(string $userId, string $materialId): ?StudentState;

    public function getAll(string $userId): Collection;

    public function delete(string $userId, string $materialId): bool;
}
