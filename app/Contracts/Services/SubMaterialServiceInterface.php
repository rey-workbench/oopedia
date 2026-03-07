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
     * @return Collection<string, SubMaterial>
     */
    public function getSubMaterialsByMaterial(string $materialId): Collection;

    /**
     * Create a new sub-material under a material.
     *
     * @return string The ID of the newly created sub-material
     */
    public function createSubMaterial(string $materialId, array $data): string;

    /**
     * Update an existing sub-material.
     */
    public function updateSubMaterial(string $subMaterialId, array $data): bool;

    /**
     * Delete a sub-material.
     */
    public function deleteSubMaterial(string $subMaterialId): bool;

    /**
     * Get a sub-material by its ID.
     */
    public function getSubMaterialById(string $subMaterialId): ?SubMaterial;

    /**
     * Get sub-materials as a simple array for JSON responses.
     *
     * @return array<string, mixed>
     */
    public function getSubMaterialsSimple(string $materialId): array;
}
