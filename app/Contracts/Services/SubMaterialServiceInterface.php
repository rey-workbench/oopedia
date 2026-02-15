<?php

namespace App\Contracts\Services;

use Illuminate\Database\Eloquent\Collection;

interface SubMaterialServiceInterface
{
    /**
     * Get all sub-materials for a material
     */
    public function getSubMaterialsByMaterial(int $materialId): Collection;

    /**
     * Create a new sub-material
     */
    public function createSubMaterial(int $materialId, array $data): int;

    /**
     * Update a sub-material
     */
    public function updateSubMaterial(int $subMaterialId, array $data): bool;

    /**
     * Delete a sub-material
     */
    public function deleteSubMaterial(int $subMaterialId): bool;

    /**
     * Get sub-material by ID
     */
    public function getSubMaterialById(int $subMaterialId);

    /**
     * Get sub-materials as simple array for JSON response
     */
    public function getSubMaterialsSimple(int $materialId): array;
}
