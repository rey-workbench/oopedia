<?php

namespace App\Repositories\Interfaces;

interface ProgressRepositoryInterface
{
    public function getUserProgressStats($userId);
    public function getUserMaterialProgress($userId);
    public function getDetailedUserProgress($userId);
    public function getRecentActivities($userId, $limit = 5);
    public function getCorrectAnswersWithAttempts($roleId = 3);
    public function getLeaderboardStats($roleId = 3);
    public function getAttemptCount($userId, $materialId, $questionId);
    public function saveProgress(array $data);
    public function updateOrCreateProgress(array $conditions, array $values);
    public function getAnsweredQuestionIds($userId, $materialId);
    public function resetProgress($userId, $materialId);
    public function getStudentCountByMaterial();
    public function getLastAccessTime($userId, $materialId);
    public function getRecentSystemProgress($limit);
    public function getMaterialPerformanceStats();
    public function getPopularMaterials($limit);
}
