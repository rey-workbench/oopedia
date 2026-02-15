<?php

namespace App\Contracts\Services;

interface QuestionListingServiceInterface
{
    public function getQuizData($material, $difficulty, $userId, $isGuest, $guestProgress = []);
    
    public function getMaterialsListWithStudentCount($userId, $isGuest, $guestProgress = []);
    
    public function getReviewQuestions($material, $difficulty, $userId, $isGuest, $guestProgress = []);
    
    public function getGuestAnsweredQuestionIds($materialId, $guestProgress = []);
    
    public function getLevelProgress($material, $difficulty, $answeredQuestionIds, $isGuest = false);
}
