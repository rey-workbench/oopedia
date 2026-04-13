<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\SubMaterialRepositoryInterface;
use App\Models\SubMaterial;
use Illuminate\Database\Eloquent\Collection;

final class SubMaterialRepository implements SubMaterialRepositoryInterface
{
    /** @return Collection<string, SubMaterial> */
    public function all(): Collection
    {
        return SubMaterial::all();
    }

    public function find(string $id): ?SubMaterial
    {
        return SubMaterial::find($id);
    }

    public function create(array $data): SubMaterial
    {
        return SubMaterial::create($data);
    }

    public function update(string $id, array $data): bool
    {
        $subMaterial = $this->find($id);

        if (! $subMaterial) {
            return false;
        }

        return (bool) $subMaterial->update($data);
    }

    public function delete(string $id): bool
    {
        $subMaterial = $this->find($id);

        if (! $subMaterial) {
            return false;
        }

        return (bool) $subMaterial->delete();
    }

    /** @return Collection<int, SubMaterial> */
    public function getAllByMaterial(string $materialId): Collection
    {
        return SubMaterial::where('material_id', '=', $materialId)
            ->ordered()
            ->get();
    }

    /** @return Collection<int, SubMaterial> */
    public function findByMaterial(string $materialId): Collection
    {
        return $this->getAllByMaterial($materialId);
    }

    public function findWithQuestions(string $id): SubMaterial
    {
        return SubMaterial::with('questions')->findOrFail($id);
    }
}
