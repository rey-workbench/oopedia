<?php

namespace App\Repositories;

use App\Models\UeqSurvey;

class UeqSurveyRepository
{
    public function getAllWithUser($class = null)
    {
        $query = UeqSurvey::with('user');
        
        if ($class) {
            $query->where('class', $class);
        }
        
        return $query->get();
    }

    public function getDistinctClasses()
    {
        return UeqSurvey::distinct()->pluck('class')->filter()->values();
    }

    public function findByUserId($userId)
    {
        return UeqSurvey::where('user_id', $userId)->firstOrFail();
    }

    public function findSurveyByUser($userId)
    {
        return UeqSurvey::where('user_id', $userId)->first();
    }
}
