<?php

namespace App\Repositories;

use App\Models\UeqSurvey;

class UeqSurveyRepository
{
    public function all()
    {
        return UeqSurvey::all();
    }

    public function find($id)
    {
        return UeqSurvey::find($id);
    }

    public function create(array $data)
    {
        return UeqSurvey::create($data);
    }

    public function update($id, array $data)
    {
        $ueq = UeqSurvey::find($id);
        if ($ueq) {
            $ueq->update($data);
            return $ueq;
        }
        return null;
    }

    public function delete($id)
    {
        $ueq = UeqSurvey::find($id);
        if ($ueq) {
            return $ueq->delete();
        }
        return false;
    }

    public function paginate($perPage = 15)
    {
        return UeqSurvey::paginate($perPage);
    }

    public function countAll()
    {
        return UeqSurvey::count();
    }

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
