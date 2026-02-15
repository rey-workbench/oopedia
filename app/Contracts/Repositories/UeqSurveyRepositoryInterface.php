<?php

namespace App\Contracts\Repositories;

interface UeqSurveyRepositoryInterface
{
    public function all();
    
    public function find($id);
    
    public function create(array $data);
    
    public function update($id, array $data);
    
    public function delete($id);
    
    public function paginate($perPage = 15);
    
    public function countAll();
    
    public function getAllWithUser($class = null);
    
    public function getDistinctClasses();
    
    public function findByUserId($userId);
    
    public function findSurveyByUser($userId);
}
