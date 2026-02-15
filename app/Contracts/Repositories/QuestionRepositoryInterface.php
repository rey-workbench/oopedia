<?php

namespace App\Contracts\Repositories;

interface QuestionRepositoryInterface
{
    public function all();
    
    public function find($id);
    
    public function create(array $data);
    
    public function update($id, array $data);
    
    public function delete($id);
    
    public function paginate($perPage = 15);
    
    public function countAll();
    
    public function findWithAnswers($id);
    
    public function getByMaterialAndDifficulty($materialId, $difficulty = null);
    
    public function getFilteredQuestions($search = null, $difficulty = null, $materialId = null);
    
    public function getQuestionsForBank($materialId, array $excludeIds, $search = null, $difficulty = null);
    
    public function countByMaterialAndDifficulty($materialId, $difficulty);
    
    public function existsByMaterialAndDifficulty($materialId, $difficulty);
}
