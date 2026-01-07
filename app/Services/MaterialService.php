<?php

namespace App\Services;

use App\Repositories\Interfaces\MaterialRepositoryInterface;
use App\Models\Media;
use App\Models\Material;
use Illuminate\Support\Facades\Storage;
use Exception;

class MaterialService extends BaseService
{
    protected $materialRepo;

    public function __construct(MaterialRepositoryInterface $materialRepo)
    {
        $this->materialRepo = $materialRepo;
    }

    public function getAllMaterials($search = null, $sort = 'created_at', $direction = 'asc')
    {
        return $this->materialRepo->getMaterialsForAdmin($search, $sort, $direction);
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

    public function updateMaterial(Material $material, array $data, $coverImage = null)
    {
        $this->materialRepo->update($material->id, [
            'title' => $data['title'],
            'content' => $data['content'],
        ]);

        if ($coverImage) {
            // Delete existing cover image if any
            $this->deleteCoverImage($material);
            
            // Upload new cover image
            $this->uploadCoverImage($material, $coverImage, $data['title']);
        }

        return $material;
    }

    public function deleteMaterial(Material $material)
    {
        // Delete related question_banks records
        $material->questionBanks()->delete();
        
        // Delete related question bank configs
        if (method_exists($material, 'questionBankConfigs')) {
            $material->questionBankConfigs()->delete();
        }
        
        // Delete associated media files
        foreach($material->media as $media) {
            $this->removeMediaFile($media->media_url);
            $media->delete(); 
        }
        
        // Delete the material
        return $this->materialRepo->delete($material->id);
    }

    public function deleteMedia($mediaId)
    {
        $media = Media::findOrFail($mediaId);
        $materialId = $media->material_id;
        
        $this->removeMediaFile($media->media_url);
        $media->delete();
        
        return $materialId;
    }

    protected function uploadCoverImage($material, $file, $title)
    {
        // Simpan file ke direktori images
        $path = $file->store('materials', 'images');
        
        Media::create([
            'material_id' => $material->id,
            'media_type' => 'image',
            'media_url' => '/images/' . $path,
            'media_description' => $title . ' - Cover Image'
        ]);
    }

    protected function deleteCoverImage(Material $material)
    {
        $existingMedia = $material->media()->where('media_type', 'image')->first();
        
        if ($existingMedia) {
            $this->removeMediaFile($existingMedia->media_url);
            $existingMedia->delete();
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
