<?php

namespace App\Contracts\Repositories;

interface MediaRepositoryInterface
{
    public function all();
    
    public function find($id);
    
    public function create(array $data);
    
    public function update($id, array $data);
    
    public function delete($id);
    
    public function getByMaterial($materialId);
    
    public function deleteByMaterial($materialId);
    
    public function findByMaterialAndType($materialId, $mediaType);
}
