<?php

namespace App\Services\Lms;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\SubMaterialRepositoryInterface;
use App\Contracts\Services\SubMaterialServiceInterface;
use App\Exceptions\Domain\MaterialNotFoundException;
use App\Models\SubMaterial;
use Illuminate\Database\Eloquent\Collection;

class SubMaterialService implements SubMaterialServiceInterface
{
    public function __construct(
        protected SubMaterialRepositoryInterface $subMaterialRepo,
        protected MaterialRepositoryInterface $materialRepo,
    ) {
    }

    /**
     * Get all sub-materials for a material
     */
    public function getSubMaterialsByMaterial(string $materialId): Collection
    {
        return $this->subMaterialRepo->findByMaterial($materialId);
    }

    /**
     * Create a new sub-material
     */
    public function createSubMaterial(string $materialId, array $data): string
    {
        $material = $this->materialRepo->find($materialId);

        if (! $material) {
            throw new MaterialNotFoundException($materialId);
        }

        $data['material_id'] = $materialId;

        $subMaterial = $this->subMaterialRepo->create($data);

        return $subMaterial->id;
    }

    /**
     * Update a sub-material
     */
    public function updateSubMaterial(string $subMaterialId, array $data): bool
    {
        return $this->subMaterialRepo->update($subMaterialId, $data);
    }

    /**
     * Delete a sub-material
     */
    public function deleteSubMaterial(string $subMaterialId): bool
    {
        return $this->subMaterialRepo->delete($subMaterialId);
    }

    /**
     * Get sub-material by ID
     */
    public function getSubMaterialById(string $subMaterialId): ?SubMaterial
    {
        return $this->subMaterialRepo->find($subMaterialId);
    }

    /**
     * Get sub-materials as simple array for JSON response
     */
    public function getSubMaterialsSimple(string $materialId): array
    {
        $subMaterials = $this->subMaterialRepo->findByMaterial($materialId);

        return $subMaterials->map(function ($sub) {
            return [
                'id'    => $sub->id,
                'title' => $sub->title,
            ];
        })->toArray();
    }
}
