<?php

namespace App\Contracts\Services;

interface DashboardServiceInterface
{
    public function getAllMaterials();
    
    public function getDashboardIndexData($userId, $isGuest);
    
    public function getInProgressData($userId, $isGuest);
    
    public function getCompletedData($userId, $isGuest);
}
