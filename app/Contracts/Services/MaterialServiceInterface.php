<?php

namespace App\Contracts\Services;

interface MaterialServiceInterface
{
    public function getAllMaterials($search = null, $sort = 'created_at', $direction = 'asc');
    
    public function getAllOrdered();
    
    public function getMaterialById($id);
    
    public function getMaterialWithQuestions($id);
    
    public function getMaterialWithQuestionsAndAnswers($id);
    
    public function createMaterial(array $data, $coverImage = null);
    
    public function updateMaterial($materialId, array $data, $coverImage = null);
    
    public function deleteMaterial($materialId);
    
    public function deleteMedia($mediaId);
}
