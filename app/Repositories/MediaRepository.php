<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\MediaRepositoryInterface;
use App\Models\Media;
use Illuminate\Database\Eloquent\Collection;

final class MediaRepository implements MediaRepositoryInterface
{
    /** @return Collection<int, Media> */
    public function all(): Collection
    {
        return Media::all();
    }

    public function find(string $id): ?Media
    {
        return Media::find($id);
    }

    public function create(array $data): Media
    {
        return Media::create($data);
    }

    public function delete(string $id): bool
    {
        $media = $this->find($id);

        if ($media instanceof Media) {
            return (bool) $media->delete();
        }

        return false;
    }

    /** @return Collection<int, Media> */
    public function getByMaterial(string $materialId): Collection
    {
        return Media::where('material_id', '=', $materialId)->get();
    }

    public function findByMaterialAndType(string $materialId, string $mediaType): ?Media
    {
        return Media::where('material_id', '=', $materialId)
            ->where('media_type', '=', $mediaType)
            ->first();
    }
}
