<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\MaterialRepositoryInterface;
use App\Models\Material;

class MaterialRepository extends BaseRepository implements MaterialRepositoryInterface
{
    public function __construct(Material $model)
    {
        parent::__construct($model);
    }

    public function getAllWithQuestions()
    {
        return $this->model->with(['questions'])->get();
    }

    public function getAllWithQuestionsAndConfigs()
    {
        return $this->model->with(['questions', 'questionBankConfigs'])->get();
    }

    public function findBySlug($slug)
    {
        $title = str_replace('-', ' ', $slug);
        return $this->model->where('title', $title)->firstOrFail();
    }

    public function getAllOrdered()
    {
        return $this->model->with(['questions', 'media'])->orderBy('created_at', 'asc')->get();
    }

    public function findWithQuestionsShuffled($id)
    {
        $material = $this->model->findOrFail($id);
        
        // Shuffle answers for each question
        foreach ($material->questions as $question) {
            if ($question->question_type !== 'fill_in_the_blank') {
                $question->answers = $question->answers->shuffle();
            }
        }
        
        return $material;
    }

    public function findWithQuestionsAndAnswers($id)
    {
        return $this->model->with(['questions.answers'])->findOrFail($id);
    }

    public function getMaterialsForAdmin($search = null, $sort = 'created_at', $direction = 'asc')
    {
        $query = $this->model->query();

        // Handle search
        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        // Validate sort field
        $allowedSortFields = ['title', 'created_at'];
        if (in_array($sort, $allowedSortFields)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', 'asc');
        }

        return $query->with('creator')->get();
    }
}
