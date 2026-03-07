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

    public function find(string $id): ?Media;

    public function create(array $data): Media;

    public function update(string $id, array $data): ?Media;

    public function delete(string $id): bool;

    /** @return Collection<int, Media> */
    public function getByMaterial(string $materialId): Collection;

    public function deleteByMaterial(string $materialId): bool;

    public function findByMaterialAndType(string $materialId, string $mediaType): ?Media;
}
