<?php

namespace App\Contracts\Services;

interface ProgressServiceInterface
{
    public function getAttemptCount($userId, $materialId, $questionId);
    
    public function getAnsweredQuestionIds($userId, $materialId);
    
    public function saveProgress(array $data);
}
