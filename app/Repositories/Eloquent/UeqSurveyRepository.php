<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\UeqSurveyRepositoryInterface;
use App\Models\UeqSurvey;

class UeqSurveyRepository extends BaseRepository implements UeqSurveyRepositoryInterface
{
    public function __construct(UeqSurvey $model)
    {
        parent::__construct($model);
    }

    public function getAllWithUser($class = null)
    {
        $query = $this->model->with('user');
        
        if ($class) {
            $query->where('class', $class);
        }
        
        return $query->get();
    }

    public function getDistinctClasses()
    {
        return $this->model->distinct()->pluck('class')->filter()->values();
    }

    public function findByUserId($userId)
    {
        return $this->model->where('user_id', $userId)->firstOrFail();
    }

    public function findSurveyByUser($userId)
    {
        return $this->model->where('user_id', $userId)->first();
    }
}
