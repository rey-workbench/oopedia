<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\QuestionRepositoryInterface;
use App\Models\Question;

class QuestionRepository extends BaseRepository implements QuestionRepositoryInterface
{
    public function __construct(Question $model)
    {
        parent::__construct($model);
    }

    public function findWithAnswers($id)
    {
        return $this->model->with('answers')->findOrFail($id);
    }

    public function getByMaterialAndDifficulty($materialId, $difficulty = null)
    {
        $query = $this->model->where('material_id', $materialId);
        
        if ($difficulty && $difficulty !== 'all') {
            $query->where('difficulty', $difficulty);
        }
        
        return $query->get();
    }
}
