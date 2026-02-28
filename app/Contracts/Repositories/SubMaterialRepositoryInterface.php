<?php

namespace App\Contracts\Repositories;

use App\Models\SubMaterial;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contract for SubMaterial data access.
 */
interface SubMaterialRepositoryInterface
{
    /** @return Collection<int, SubMaterial> */
    public function all(): Collection;

    public function find(int $id): ?SubMaterial;

    public function create(array $data): SubMaterial;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    /** @return Collection<int, SubMaterial> */
    public function getAllByMaterial(int $materialId): Collection;

    /** @return Collection<int, SubMaterial> */
    public function findByMaterial(int $materialId): Collection;

    public function reorder(int $materialId, array $orderData): void;

    public function findWithQuestions(int $id): SubMaterial;
}
