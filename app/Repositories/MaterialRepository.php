<?php

namespace App\Repositories;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Models\Material;

class MaterialRepository implements MaterialRepositoryInterface
{
    public function all()
    {
        return Material::all();
    }

    public function find($id)
    {
        return Material::find($id);
    }

    public function create(array $data)
    {
        return Material::create($data);
    }

    public function update($id, array $data)
    {
        $material = Material::find($id);
        if ($material) {
            $material->update($data);
            return $material;
        }
        return null;
    }

    public function delete($id)
    {
        $material = Material::find($id);
        if ($material) {
            return $material->delete();
        }
        return false;
    }

    public function paginate($perPage = 15)
    {
        return Material::paginate($perPage);
    }

    public function countAll()
    {
        return Material::count('*');
    }

    public function getAllWithQuestions()
    {
        return Material::with(['questions'])->get();
    }

    public function getAllWithQuestionsAndConfigs()
    {
        // QuestionBankConfigs removed - just return with questions
        return Material::with(['questions'])->get();
    }

    public function getAllWithQuestionsAndActiveConfigs()
    {
        return Material::with(['questions'])->get();
    }

    public function findBySlug($slug)
    {
        $title = str_replace('-', ' ', $slug);
        return Material::where('title', $title)->firstOrFail();
    }

    public function getAllOrdered()
    {
        return Material::with(['questions', 'media', 'creator'])->orderBy('created_at', 'asc')->get();
    }

    public function findWithQuestionsShuffled($id)
    {
        $material = Material::with(['subMaterials.questions', 'creator', 'media'])->findOrFail($id);
        
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
        return Material::with(['questions.answers'])->findOrFail($id);
    }

    public function getMaterialsForAdmin($search = null, $sort = 'created_at', $direction = 'asc')
    {
        $query = Material::query();

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

        return $query->with(['creator', 'subMaterials', 'media'])->get();
    }

    public function findWithRelations($id, array $relations = [])
    {
        $query = Material::query();
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->findOrFail($id);
    }
}
