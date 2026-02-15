<?php

namespace App\Contracts\Repositories;

interface SubMaterialRepositoryInterface
{
    public function all();
    
    public function find($id);
    
    public function create(array $data);
    
    public function update($id, array $data);
    
    public function delete($id);
    
    public function getAllByMaterial($materialId);
    
    public function findByMaterial($materialId);
    
    public function reorder($materialId, array $orderData);
    
    public function findWithQuestions($id);
}
