<?php

namespace App\Contracts\Services;

interface QuestionAnswerServiceInterface
{
    public function checkAnswer($data, $userId, $isGuest);
    
    public function checkAllAnswers($data, $userId);
    
    public function determineCorrectness($question, array $data): bool;
}
