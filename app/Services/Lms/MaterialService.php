<?php

namespace App\Services\Lms;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\MediaRepositoryInterface;
use App\Contracts\Services\MaterialServiceInterface;
use App\Exceptions\Domain\MaterialNotFoundException;
use App\Exceptions\Domain\MediaOperationException;
use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class MaterialService implements MaterialServiceInterface
{
    public function __construct(protected
        MaterialRepositoryInterface $materialRepo, protected
        MediaRepositoryInterface $mediaRepo,
        )
    {
    }

    /** @return Collection<int, Material> */
    public function getAllMaterials(?string $search = null, string $sort = 'created_at', string $direction = 'asc'): Collection
    {
        return $this->materialRepo->getMaterialsForAdmin($search, $sort, $direction);
    }

    /** @return Collection<int, Material> */
    public function getAllOrdered(): Collection
    {
        return $this->materialRepo->getAllOrdered();
    }

    public function getMaterialById(string $id): ?Material
    {
        return $this->materialRepo->find($id);
    }

    public function getMaterialWithQuestionsAndAnswers(string $id): ?Material
    {
        return $this->materialRepo->findWithQuestionsAndAnswers($id);
    }

    public function createMaterial(array $data, mixed $coverImage = null): Material
    {
        $material = $this->materialRepo->create([
            'title' => $data['title'],
            'content' => $data['content'],
            'module_id' => $data['module_id'],
            'created_by' => $data['created_by'],
            'is_final_project' => $data['is_final_project'] ?? false,
        ]);

        if ($coverImage) {
            $this->uploadCoverImage($material, $coverImage, $data['title']);
        }

        Cache::forget('sidebar_materials_v4');

        return $material;
    }

    public function updateMaterial(string $materialId, array $data, mixed $coverImage = null): Material
    {
        $material = $this->materialRepo->find($materialId);

        if (!$material) {
            throw new MaterialNotFoundException($materialId);
        }

        $this->materialRepo->update($material->id, [
            'title' => $data['title'],
            'content' => $data['content'] ?? $data['description'] ?? null,
            'module_id' => $data['module_id'] ?? $material->module_id,
            'is_final_project' => $data['is_final_project'] ?? $material->is_final_project,
        ]);

        if ($coverImage) {
            $this->deleteCoverImage($material);
            $this->uploadCoverImage($material, $coverImage, $data['title']);
        }

        Cache::forget('sidebar_materials_v4');

        return $material->fresh();
    }

    public function deleteMaterial(string $materialId): void
    {
        $material = $this->materialRepo->find($materialId);

        if (!$material) {
            throw new MaterialNotFoundException($materialId);
        }

        $mediaFiles = $this->mediaRepo->getByMaterial($material->id);

        foreach ($mediaFiles as $media) {
            $this->removeMediaFile($media->media_url);
            $this->mediaRepo->delete($media->id);
        }

        $this->materialRepo->delete($material->id);

        Cache::forget('sidebar_materials_v4');
    }

    public function deleteMedia(string $mediaId): string
    {
        $media = $this->mediaRepo->find($mediaId);

        if (!$media) {
            throw new MediaOperationException("Media dengan ID '{$mediaId}' tidak ditemukan.");
        }

        $materialId = $media->material_id;

        $this->removeMediaFile($media->media_url);
        $this->mediaRepo->delete($mediaId);

        return (string)$materialId;
    }

    protected function uploadCoverImage(Material $material, mixed $file, string $title): void
    {
        $path = $file->store('materials', 'images');

        $this->mediaRepo->create([
            'material_id' => $material->id,
            'media_type' => 'image',
            'media_url' => '/images/' . $path,
        ]);
    }

    protected function deleteCoverImage(Material $material): void
    {
        $existingMedia = $this->mediaRepo->findByMaterialAndType($material->id, 'image');

        if ($existingMedia) {
            $this->removeMediaFile($existingMedia->media_url);
            $this->mediaRepo->delete($existingMedia->id);
        }
    }

    protected function removeMediaFile(string $path): void
    {
        if (str_starts_with($path, '/images/')) {
            $path = str_replace('/images/', '', $path);

            if (Storage::disk('images')->exists($path)) {
                Storage::disk('images')->delete($path);
            }
        }
        elseif (str_starts_with($path, '/storage/')) {
            $path = str_replace('/storage/', '', $path);

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
        else {
            $path = str_replace('storage/', '', $path);

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}
