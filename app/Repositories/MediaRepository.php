<?php

namespace App\Repositories;

use App\Contracts\Repositories\MediaRepositoryInterface;
use App\Models\Media;

class MediaRepository implements MediaRepositoryInterface
{
    public function all()
    {
        return Media::all();
    }

    public function find($id)
    {
        return Media::find($id);
    }

    public function create(array $data)
    {
        return Media::create($data);
    }

    public function update($id, array $data)
    {
        $media = $this->find($id);
        if ($media) {
            $media->update($data);
            return $media;
        }
        return null;
    }

    public function delete($id)
    {
        $media = $this->find($id);
        if ($media) {
            return $media->delete();
        }
        return false;
    }

    public function getByMaterial($materialId)
    {
        return Media::where('material_id', $materialId)->get();
    }

    public function deleteByMaterial($materialId)
    {
        return Media::where('material_id', $materialId)->delete();
    }

    public function findByMaterialAndType($materialId, $mediaType)
    {
        return Media::where('material_id', $materialId)
            ->where('media_type', $mediaType)
            ->first();
    }
}
