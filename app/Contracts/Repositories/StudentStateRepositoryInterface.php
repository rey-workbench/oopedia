<?php

namespace App\Contracts\Repositories;

interface StudentStateRepositoryInterface
{
    public function upsert($userId, $materialId, array $attributes);
    
    public function getByUserAndMaterial($userId, $materialId);
    
    public function updateProgress($userId, $materialId, array $progressData);
    
    public function getAll($userId);
    
    public function delete($userId, $materialId);
}
