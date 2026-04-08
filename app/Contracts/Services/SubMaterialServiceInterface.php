<?php

namespace App\Contracts\Services;

use App\Models\SubMaterial;
use Illuminate\Database\Eloquent\Collection;

interface SubMaterialServiceInterface
{
    public function getSubMaterialsByMaterial(string $materialId): Collection;

    public function createSubMaterial(string $materialId, array $data): string;

    public function updateSubMaterial(string $subMaterialId, array $data): bool;

    public function deleteSubMaterial(string $subMaterialId): bool;

    public function getSubMaterialById(string $subMaterialId): ?SubMaterial;

    public function getSubMaterialsSimple(string $materialId): array;
}
