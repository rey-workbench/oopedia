<?php

namespace App\Contracts\Repositories;

interface AnswerRepositoryInterface
{
    public function all();
    
    public function find($id);
    
    public function findOrFail($id);
    
    public function create(array $data);
    
    public function update($id, array $data);
    
    public function delete($id);
    
    public function getByQuestionId($questionId);
    
    public function getCorrectAnswers($questionId);
    
    public function deleteByQuestionId($questionId);
}
