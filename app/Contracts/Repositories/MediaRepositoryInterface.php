<?php

namespace App\Contracts\Repositories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Collection;

interface MediaRepositoryInterface
{
    public function all(): Collection;

    public function find(string $id): ?Media;

    public function create(array $data): Media;

    public function delete(string $id): bool;

    public function getByMaterial(string $materialId): Collection;

    public function findByMaterialAndType(string $materialId, string $mediaType): ?Media;
}
