<?php

namespace App\Repositories;

use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Models\Question;

class QuestionRepository implements QuestionRepositoryInterface
{
    public function all()
    {
        return Question::all();
    }

    public function find($id)
    {
        return Question::find($id);
    }

    public function create(array $data)
    {
        return Question::create($data);
    }

    public function update($id, array $data)
    {
        $question = Question::find($id);
        if ($question) {
            $question->update($data);
            return $question;
        }
        return null;
    }

    public function delete($id)
    {
        $question = Question::find($id);
        if ($question) {
            return $question->delete();
        }
        return false;
    }

    public function paginate($perPage = 15)
    {
        return Question::paginate($perPage);
    }

    public function countAll()
    {
        return Question::count();
    }

    public function findWithAnswers($id)
    {
        return Question::with('answers')->findOrFail($id);
    }

    public function getByMaterialAndDifficulty($materialId, $difficulty = null)
    {
        $query = Question::where('material_id', $materialId);
        
        if ($difficulty && $difficulty !== 'all') {
            $query->where('difficulty', $difficulty);
        }
        
        return $query->get();
    }

    public function getFilteredQuestions($search = null, $difficulty = null, $materialId = null)
    {
        return Question::with(['createdBy', 'answers', 'material'])
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
            ->paginate(15);
    }

    public function getQuestionsForBank($materialId, array $excludeIds, $search = null, $difficulty = null)
    {
        return Question::with(['material', 'answers'])
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

    public function countByMaterialAndDifficulty($materialId, $difficulty)
    {
        return Question::where('material_id', $materialId)
            ->where('difficulty', $difficulty)
            ->count();
    }

    public function existsByMaterialAndDifficulty($materialId, $difficulty)
    {
        return Question::where('material_id', $materialId)
            ->where('difficulty', $difficulty)
            ->exists();
    }
}
