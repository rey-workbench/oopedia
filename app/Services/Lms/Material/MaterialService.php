<?php

namespace App\Services\Lms\Material;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\MediaRepositoryInterface;
use App\Contracts\Services\MaterialServiceInterface;
use App\Models\Material;
use Illuminate\Support\Facades\Storage;
use Exception;

class MaterialService implements MaterialServiceInterface
{
    protected $materialRepo;
    protected $mediaRepo;

    public function __construct(
        MaterialRepositoryInterface $materialRepo,
        MediaRepositoryInterface $mediaRepo
    )
    {
        $this->materialRepo = $materialRepo;
        $this->mediaRepo = $mediaRepo;
    }

    public function getAllMaterials($search = null, $sort = 'created_at', $direction = 'asc')
    {
        return $this->materialRepo->getMaterialsForAdmin($search, $sort, $direction);
    }

    public function getAllOrdered()
    {
        return $this->materialRepo->getAllOrdered();
    }

    public function getMaterialById($id)
    {
        return $this->materialRepo->find($id);
    }

    public function getMaterialWithQuestions($id)
    {
        return $this->materialRepo->findWithQuestionsAndAnswers($id);
    }

    public function getMaterialWithQuestionsAndAnswers($id)
    {
        return $this->materialRepo->findWithQuestionsAndAnswers($id);
    }

    public function createMaterial(array $data, $coverImage = null)
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

    public function updateMaterial($materialId, array $data, $coverImage = null)
    {
        $material = $this->materialRepo->find($materialId);
        if (!$material) {
            throw new Exception("Material not found");
        }

        $this->materialRepo->update($material->id, [
            'title' => $data['title'],
            'content' => $data['content'] ?? $data['description'] ?? null,
        ]);

        if ($coverImage) {
            // Delete existing cover image if any
            $this->deleteCoverImage($material);
            
            // Upload new cover image
            $this->uploadCoverImage($material, $coverImage, $data['title']);
        }

        return $material->fresh();
    }

    public function deleteMaterial($materialId)
    {
        $material = $this->materialRepo->find($materialId);
        if (!$material) {
            throw new Exception("Material not found");
        }

        $mediaFiles = $this->mediaRepo->getByMaterial($material->id);
        foreach($mediaFiles as $media) {
            $this->removeMediaFile($media->media_url);
            $this->mediaRepo->delete($media->id); 
        }
                
        return $this->materialRepo->delete($material->id);
    }

    public function deleteMedia($mediaId)
    {
        $media = $this->mediaRepo->find($mediaId);
        if (!$media) {
            throw new Exception("Media not found");
        }

        $materialId = $media->material_id;
        
        $this->removeMediaFile($media->media_url);
        $this->mediaRepo->delete($mediaId);
        
        return $materialId;
    }

    protected function uploadCoverImage($material, $file, $title)
    {
        // Simpan file ke direktori images
        $path = $file->store('materials', 'images');
        
        $this->mediaRepo->create([
            'material_id' => $material->id,
            'media_type' => 'image',
            'media_url' => '/images/' . $path,
            'media_description' => $title . ' - Cover Image'
        ]);
    }

    protected function deleteCoverImage(Material $material)
    {
        $existingMedia = $this->mediaRepo->findByMaterialAndType($material->id, 'image');
        
        if ($existingMedia) {
            $this->removeMediaFile($existingMedia->media_url);
            $this->mediaRepo->delete($existingMedia->id);
        }
    }

    protected function removeMediaFile($path)
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
        } else {
             // Handle generic storage path if 'storage/' prefix is present without leading slash
             $path = str_replace('storage/', '', $path);
             if (Storage::disk('public')->exists($path)) {
                 Storage::disk('public')->delete($path);
             }
        }
    }
}
