<?php

namespace App\Contracts\Repositories;

use App\Models\SubMaterial;
use Illuminate\Database\Eloquent\Collection;

interface SubMaterialRepositoryInterface
{
    public function all(): Collection;

    public function find(string $id): ?SubMaterial;

    public function create(array $data): SubMaterial;

    public function update(string $id, array $data): bool;

    public function delete(string $id): bool;

    public function findByMaterial(string $materialId): Collection;

    public function findWithQuestions(string $id): SubMaterial;
}
