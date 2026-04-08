<?php

namespace App\Services\Lms;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\SubMaterialRepositoryInterface;
use App\Contracts\Services\SubMaterialServiceInterface;
use App\Exceptions\Domain\MaterialNotFoundException;
use App\Models\SubMaterial;
use Illuminate\Database\Eloquent\Collection;

final class SubMaterialService implements SubMaterialServiceInterface
{
    public function __construct(
        public readonly SubMaterialRepositoryInterface $subMaterialRepo,
        public readonly MaterialRepositoryInterface $materialRepo,
    ) {}

    public function getSubMaterialsByMaterial(string $materialId): Collection
    {
        return $this->subMaterialRepo->findByMaterial($materialId);
    }

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

    public function updateSubMaterial(string $subMaterialId, array $data): bool
    {
        return $this->subMaterialRepo->update($subMaterialId, $data);
    }

    public function deleteSubMaterial(string $subMaterialId): bool
    {
        return $this->subMaterialRepo->delete($subMaterialId);
    }

    public function getSubMaterialById(string $subMaterialId): ?SubMaterial
    {
        return $this->subMaterialRepo->find($subMaterialId);
    }

    public function getSubMaterialsSimple(string $materialId): array
    {
        return $this->subMaterialRepo->findByMaterial($materialId)->map(fn (SubMaterial $subMaterial) => [
            'id'    => $subMaterial->id,
            'title' => $subMaterial->title,
        ])->toArray();
    }
}
