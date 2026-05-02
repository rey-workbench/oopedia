<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\Material\MaterialCreateDTO;
use App\DTOs\Material\MaterialUpdateDTO;
use App\Models\Material;
use Illuminate\Support\Collection;

interface MaterialServiceInterface
{
    // --- Material Management (from MaterialServiceInterface) ---
    public function getAllMaterials(
        ?string $search = null,
        string $sort = 'created_at',
        string $direction = 'asc',
    ): Collection;

    public function getAllOrdered(): Collection;

    public function getMaterialById(string $id): ?array;

    public function getMaterialWithQuestionsAndAnswers(string $id): ?array;

    public function createMaterial(MaterialCreateDTO $materialCreateDTO): Material;

    public function updateMaterial(string $materialId, MaterialUpdateDTO $materialUpdateDTO): Material;

    public function deleteMaterial(string $materialId): void;

    public function deleteMedia(string $mediaId): string;

    public function getSidebarMaterials(?string $userId, bool $isGuest): Collection;

    // --- Material Viewing & Progress (from MaterialViewServiceInterface) ---
    public function getMaterialsList(?string $userId, bool $isGuest): Collection;

    public function getMaterialDetail(string $materialId, ?string $userId, bool $isGuest, array $guestProgress = []): array;

    public function resetMaterialProgress(string $userId, string $materialId): void;
}
