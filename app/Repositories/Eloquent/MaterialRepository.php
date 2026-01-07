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
}
