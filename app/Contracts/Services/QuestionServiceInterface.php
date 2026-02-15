<?php

namespace App\Contracts\Services;

interface QuestionServiceInterface
{
    public function getFilteredQuestions($search = null, $difficulty = null, $materialId = null);
    
    public function getAvailableQuestionsForBank($materialId, array $excludeIds, $search = null, $difficulty = null);
    
    public function getQuestionById($id);
    
    public function getQuestionWithAnswers($id);
    
    public function existsByMaterialAndDifficulty($materialId, $difficulty);
    
    public function createQuestion(array $data);
    
    public function updateQuestion($questionId, array $data);
    
    public function deleteQuestion($questionId);
}
