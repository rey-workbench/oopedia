<?php

namespace App\Contracts\Services;

use App\Models\SubMaterial;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contract for sub-material management service.
 */
interface SubMaterialServiceInterface
{
    /**
     * Get all sub-materials for a given material.
     *
     * @return Collection<int, SubMaterial>
     */
    public function getSubMaterialsByMaterial(int $materialId): Collection;

    /**
     * Create a new sub-material under a material.
     *
     * @return int The ID of the newly created sub-material
     */
    public function createSubMaterial(int $materialId, array $data): int;

    /**
     * Update an existing sub-material.
     */
    public function updateSubMaterial(int $subMaterialId, array $data): bool;

    /**
     * Delete a sub-material.
     */
    public function deleteSubMaterial(int $subMaterialId): bool;

    /**
     * Get a sub-material by its ID.
     */
    public function getSubMaterialById(int $subMaterialId): ?SubMaterial;

    /**
     * Get sub-materials as a simple array for JSON responses.
     *
     * @return array<int, mixed>
     */
    public function getSubMaterialsSimple(int $materialId): array;
}
