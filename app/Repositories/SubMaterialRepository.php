<?php

namespace App\Repositories;

use App\Contracts\Repositories\SubMaterialRepositoryInterface;
use App\Models\SubMaterial;
use Illuminate\Database\Eloquent\Collection;

class SubMaterialRepository implements SubMaterialRepositoryInterface
{
    /** @return Collection<int, SubMaterial> */
    public function all(): Collection
    {
        return SubMaterial::all();
    }

    public function find(int $id): ?SubMaterial
    {
        return SubMaterial::find($id);
    }

    public function create(array $data): SubMaterial
    {
        return SubMaterial::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $subMaterial = $this->find($id);

        if (! $subMaterial) {
            return false;
        }

        return (bool) $subMaterial->update($data);
    }

    public function delete(int $id): bool
    {
        $subMaterial = $this->find($id);

        if ($subMaterial) {
            $result = $subMaterial->delete();

            return $result === true;
        }

        return false;
    }

    /** @return Collection<int, SubMaterial> */
    public function getAllByMaterial(int $materialId): Collection
    {
        return SubMaterial::where('material_id', $materialId)
            ->ordered()
            ->get();
    }

    /** @return Collection<int, SubMaterial> */
    public function findByMaterial(int $materialId): Collection
    {
        return $this->getAllByMaterial($materialId);
    }

    public function reorder(int $materialId, array $orderData): void
    {
        foreach ($orderData as $order => $subMaterialId) {
            SubMaterial::where('id', $subMaterialId)
                ->where('material_id', $materialId)
                ->update(['order' => $order + 1]);
        }
    }

    public function findWithQuestions(int $id): SubMaterial
    {
        return SubMaterial::with('questions')->findOrFail($id);
    }
}
