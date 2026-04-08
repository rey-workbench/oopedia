<?php

namespace App\Contracts\Services;

use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;

interface MaterialServiceInterface
{
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
}
