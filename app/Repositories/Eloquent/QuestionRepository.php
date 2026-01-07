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

    public function getFilteredQuestions($search = null, $difficulty = null, $materialId = null)
    {
        return $this->model->with(['createdBy', 'answers', 'material'])
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('question_text', 'like', "%{$search}%")
                        ->orWhere('question_type', 'like', "%{$search}%")
                        ->orWhereHas('createdBy', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('material', function ($materialQuery) use ($search) {
                            $materialQuery->where('title', 'like', "%{$search}%");
                        });
                });
            })
            ->when($difficulty, function ($query) use ($difficulty) {
                return $query->where('difficulty', $difficulty);
            })
            ->when($materialId, function ($query) use ($materialId) {
                return $query->where('material_id', $materialId);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getQuestionsForBank($materialId, array $excludeIds, $search = null, $difficulty = null)
    {
        return $this->model->with(['material', 'answers'])
            ->where('material_id', $materialId)
            ->whereNotIn('id', $excludeIds)
            ->when($search, function ($query) use ($search) {
                $query->where('question_text', 'like', "%{$search}%");
            })
            ->when($difficulty, function ($query) use ($difficulty) {
                $query->where('difficulty', $difficulty);
            })
            ->paginate(10);
    }
}
