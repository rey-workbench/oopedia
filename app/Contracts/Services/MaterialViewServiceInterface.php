<?php

namespace App\Contracts\Services;

use Illuminate\Database\Eloquent\Collection;

interface MaterialViewServiceInterface
{
    /** @return Collection<int, \App\Models\Material> */
    public function getMaterialsList(string|null $userId, bool $isGuest): Collection;

    /** @return array<string, mixed> */
    public function getMaterialDetail(string $materialId, string|null $userId, bool $isGuest): array;

    /** @return array<string, mixed> */
    public function getSubMaterialDetail(string $materialId, string $subMaterialId, bool $isGuest): array;

    public function resetMaterialProgress(string $userId, string $materialId): void;
}
