<?php

namespace App\Repositories\Interfaces;

interface QuestionRepositoryInterface extends BaseRepositoryInterface
{
    public function findWithAnswers($id);
    public function getByMaterialAndDifficulty($materialId, $difficulty = null);
}
