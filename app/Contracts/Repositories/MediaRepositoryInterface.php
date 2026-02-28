<?php

namespace App\Contracts\Repositories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contract for Media file data access.
 */
interface MediaRepositoryInterface
{
    /** @return Collection<int, Media> */
    public function all(): Collection;

    public function find(int $id): ?Media;

    public function create(array $data): Media;

    public function update(int $id, array $data): ?Media;

    public function delete(int $id): bool;

    /** @return Collection<int, Media> */
    public function getByMaterial(int $materialId): Collection;

    public function deleteByMaterial(int $materialId): bool;

    public function findByMaterialAndType(int $materialId, string $mediaType): ?Media;
}
