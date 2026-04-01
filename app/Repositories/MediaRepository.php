<?php

namespace App\Repositories;

use App\Contracts\Repositories\MediaRepositoryInterface;
use App\Models\Media;
use Illuminate\Database\Eloquent\Collection;

class MediaRepository implements MediaRepositoryInterface
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

    public function update(string $id, array $data): ?Media
    {
        $media = $this->find($id);

        if ($media) {
            $media->update($data);

            return $media;
        }

        return null;
    }

    public function delete(string $id): bool
    {
        $media = $this->find($id);

        if ($media) {
            return (bool) $media->delete();
        }

        return false;
    }

    /** @return Collection<int, Media> */
    public function getByMaterial(string $materialId): Collection
    {
        return Media::where('material_id', '=', $materialId)->get();
    }

    public function deleteByMaterial(string $materialId): bool
    {
        return (bool) Media::where('material_id', '=', $materialId)->delete();
    }

    public function findByMaterialAndType(string $materialId, string $mediaType): ?Media
    {
        return Media::where('material_id', '=', $materialId)
            ->where('media_type', '=', $mediaType)
            ->first();
    }
}
