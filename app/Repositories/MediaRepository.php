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

    public function find(int $id): ?Media
    {
        return Media::find($id);
    }

    public function create(array $data): Media
    {
        return Media::create($data);
    }

    public function update(int $id, array $data): ?Media
    {
        $media = $this->find($id);

        if ($media) {
            $media->update($data);

            return $media;
        }

        return null;
    }

    public function delete(int $id): bool
    {
        $media = $this->find($id);

        if ($media) {
            return (bool) $media->delete();
        }

        return false;
    }

    /** @return Collection<int, Media> */
    public function getByMaterial(int $materialId): Collection
    {
        return Media::where('material_id', $materialId)->get();
    }

    public function deleteByMaterial(int $materialId): bool
    {
        return (bool) Media::where('material_id', $materialId)->delete();
    }

    public function findByMaterialAndType(int $materialId, string $mediaType): ?Media
    {
        return Media::where('material_id', $materialId)
            ->where('media_type', $mediaType)
            ->first();
    }
}
