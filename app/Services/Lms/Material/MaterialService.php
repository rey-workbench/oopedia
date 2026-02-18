<?php

namespace App\Services\Lms\Material;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\MediaRepositoryInterface;
use App\Contracts\Services\MaterialServiceInterface;
use App\Exceptions\Domain\MaterialNotFoundException;
use App\Exceptions\Domain\MediaOperationException;
use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class MaterialService implements MaterialServiceInterface
{
    public function __construct(
        protected MaterialRepositoryInterface $materialRepo,
        protected MediaRepositoryInterface $mediaRepo,
    ) {}

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

    public function getMaterialById(int $id): ?Material
    {
        return $this->materialRepo->find($id);
    }

    public function getMaterialWithQuestions(int $id): ?Material
    {
        return $this->materialRepo->findWithQuestionsAndAnswers($id);
    }

    public function getMaterialWithQuestionsAndAnswers(int $id): ?Material
    {
        return $this->materialRepo->findWithQuestionsAndAnswers($id);
    }

    public function createMaterial(array $data, mixed $coverImage = null): Material
    {
        $material = $this->materialRepo->create([
            'title' => $data['title'],
            'content' => $data['content'],
            'created_by' => $data['created_by'],
        ]);

        if ($coverImage) {
            $this->uploadCoverImage($material, $coverImage, $data['title']);
        }

        return $material;
    }

    public function updateMaterial(int $materialId, array $data, mixed $coverImage = null): Material
    {
        $material = $this->materialRepo->find($materialId);

        if (! $material) {
            throw new MaterialNotFoundException($materialId);
        }

        $this->materialRepo->update($material->id, [
            'title' => $data['title'],
            'content' => $data['content'] ?? $data['description'] ?? null,
        ]);

        if ($coverImage) {
            $this->deleteCoverImage($material);
            $this->uploadCoverImage($material, $coverImage, $data['title']);
        }

        return $material->fresh();
    }

    public function deleteMaterial(int $materialId): void
    {
        $material = $this->materialRepo->find($materialId);

        if (! $material) {
            throw new MaterialNotFoundException($materialId);
        }

        $mediaFiles = $this->mediaRepo->getByMaterial($material->id);

        foreach ($mediaFiles as $media) {
            $this->removeMediaFile($media->media_url);
            $this->mediaRepo->delete($media->id);
        }

        $this->materialRepo->delete($material->id);
    }

    public function deleteMedia(int $mediaId): int
    {
        $media = $this->mediaRepo->find($mediaId);

        if (! $media) {
            throw new MediaOperationException("Media dengan ID '{$mediaId}' tidak ditemukan.");
        }

        $materialId = $media->material_id;

        $this->removeMediaFile($media->media_url);
        $this->mediaRepo->delete($mediaId);

        return $materialId;
    }

    protected function uploadCoverImage(Material $material, mixed $file, string $title): void
    {
        $path = $file->store('materials', 'images');

        $this->mediaRepo->create([
            'material_id' => $material->id,
            'media_type' => 'image',
            'media_url' => '/images/' . $path,
            'media_description' => $title . ' - Cover Image',
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
        } elseif (str_starts_with($path, '/storage/')) {
            $path = str_replace('/storage/', '', $path);

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        } else {
            $path = str_replace('storage/', '', $path);

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}
