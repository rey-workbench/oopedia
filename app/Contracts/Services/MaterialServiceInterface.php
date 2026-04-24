<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Models\Material;
use App\Models\SubMaterial;
use Illuminate\Database\Eloquent\Collection;

interface MaterialServiceInterface
{
    // --- Material Management (from MaterialServiceInterface) ---
    public function getAllMaterials(
        ?string $search = null,
        string $sort = 'created_at',
        string $direction = 'asc',
    ): Collection;

    public function getAllOrdered(): Collection;

    public function getMaterialById(string $id): ?Material;

    public function getMaterialWithQuestionsAndAnswers(string $id): ?Material;

    public function createMaterial(array $data, mixed $coverImage = null): Material;

    public function updateMaterial(string $materialId, array $data, mixed $coverImage = null): Material;

    public function deleteMaterial(string $materialId): void;

    public function deleteMedia(string $mediaId): string;

    public function getSidebarMaterials(?string $userId, bool $isGuest): Collection;

    // --- Material Viewing & Progress (from MaterialViewServiceInterface) ---
    public function getMaterialsList(?string $userId, bool $isGuest): Collection;

    public function getMaterialDetail(string $materialId, ?string $userId, bool $isGuest, array $guestProgress = []): array;

    public function getSubMaterialDetail(string $materialId, string $subMaterialId, bool $isGuest): array;

    public function resetMaterialProgress(string $userId, string $materialId): void;

    // --- Sub-Material Management (from SubMaterialServiceInterface) ---
    public function getSubMaterialsByMaterial(string $materialId): Collection;

    public function createSubMaterial(string $materialId, array $data): string;

    public function updateSubMaterial(string $subMaterialId, array $data): bool;

    public function deleteSubMaterial(string $subMaterialId): bool;

    public function getSubMaterialById(string $subMaterialId): ?SubMaterial;

    public function getSubMaterialsSimple(string $materialId): array;
}
