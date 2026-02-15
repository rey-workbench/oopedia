<?php

namespace App\Contracts\Repositories;

interface QuizAttemptRepositoryInterface
{
    public function create(array $data);
    
    public function find($id);
    
    public function getByUser($userId);
    
    public function getByUserAndQuestion($userId, $questionId);
    
    public function getByMaterial($materialId);
    
    public function getBestAttempt($userId, $questionId);
    
    public function getLatestAttempt($userId, $questionId);
    
    public function countAttempts($userId, $questionId);
    
    public function getCorrectAttempts($userId);
    
    public function getUserStats($userId);
}
