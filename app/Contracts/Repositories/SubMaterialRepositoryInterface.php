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

    public function find(string $id): ?SubMaterial;

    public function create(array $data): SubMaterial;

    public function update(string $id, array $data): bool;

    public function delete(string $id): bool;

    /** @return Collection<int, SubMaterial> */
    public function getAllByMaterial(string $materialId): Collection;

    /** @return Collection<int, SubMaterial> */
    public function findByMaterial(string $materialId): Collection;

    public function reorder(string $materialId, array $orderData): void;

    public function findWithQuestions(string $id): SubMaterial;
}
