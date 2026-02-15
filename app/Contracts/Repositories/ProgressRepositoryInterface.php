<?php

namespace App\Contracts\Repositories;

interface ProgressRepositoryInterface
{
    public function getUserProgressStats($userId);
    
    public function getUserMaterialProgress($userId);
    
    public function getRecentActivities($userId, $limit = 5);
    
    public function getDetailedUserProgress($userId);
    
    public function getCorrectAnswersWithAttempts($roleId = 3);
    
    public function getLeaderboardStats($roleId = 3);
    
    public function getAttemptCount($userId, $materialId, $questionId);
    
    public function saveProgress(array $data);
    
    public function updateStudentState($userId, array $attributes);
    
    public function getStudentState($userId);
    
    public function getOrCreateStudentState($userId);
    
    public function getUserMaterialProgressWithState($userId, $materialId);
}
