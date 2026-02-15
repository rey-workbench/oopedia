<?php

namespace App\Contracts\Repositories;

interface MaterialRepositoryInterface
{
    public function all();
    
    public function find($id);
    
    public function create(array $data);
    
    public function update($id, array $data);
    
    public function delete($id);
    
    public function paginate($perPage = 15);
    
    public function countAll();
    
    public function getAllWithQuestions();
    
    public function getAllWithQuestionsAndConfigs();
    
    public function getAllWithQuestionsAndActiveConfigs();
    
    public function findBySlug($slug);
    
    public function getAllOrdered();
    
    public function findWithQuestionsShuffled($id);
    
    public function findWithQuestionsAndAnswers($id);
    
    public function getMaterialsForAdmin($search = null, $sort = 'created_at', $direction = 'asc');
    
    public function findWithRelations($id, array $relations = []);
}
