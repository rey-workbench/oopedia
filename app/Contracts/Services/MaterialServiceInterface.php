<?php

namespace App\Contracts\Services;

use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contract for materials management service.
 */
interface MaterialServiceInterface
{
    /**
     * Get all materials for admin with optional search and sorting.
     *
     * @return Collection<int, Material>
     */
    public function getAllMaterials(?string $search = null, string $sort = 'created_at', string $direction = 'asc'): Collection;

    /**
     * Get all materials ordered by creation date.
     *
     * @return Collection<int, Material>
     */
    public function getAllOrdered(): Collection;

    /**
     * Get a material by its ID.
     */
    public function getMaterialById(int $id): ?Material;

    /**
     * Get a material with all its questions.
     */
    public function getMaterialWithQuestions(int $id): ?Material;

    /**
     * Get a material with full question and answer data.
     */
    public function getMaterialWithQuestionsAndAnswers(int $id): ?Material;

    /**
     * Create a new material with optional cover image.
     */
    public function createMaterial(array $data, mixed $coverImage = null): Material;

    /**
     * Update an existing material by ID.
     */
    public function updateMaterial(int $materialId, array $data, mixed $coverImage = null): Material;

    /**
     * Delete a material and all its associated media.
     */
    public function deleteMaterial(int $materialId): void;

    /**
     * Delete a specific media file by ID, returns the parent material ID.
     */
    public function deleteMedia(int $mediaId): int;
}
