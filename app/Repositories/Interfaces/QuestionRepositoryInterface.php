<?php

namespace App\Repositories\Interfaces;

interface QuestionRepositoryInterface extends BaseRepositoryInterface
{
    public function findWithAnswers($id);
    public function getByMaterialAndDifficulty($materialId, $difficulty = null);
    public function getFilteredQuestions($search = null, $difficulty = null, $materialId = null);
    public function getQuestionsForBank($materialId, array $excludeIds, $search = null, $difficulty = null);
}
