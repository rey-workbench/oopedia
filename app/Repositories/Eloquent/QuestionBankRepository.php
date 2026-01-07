<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\QuestionBankRepositoryInterface;
use App\Models\QuestionBank;

class QuestionBankRepository extends BaseRepository implements QuestionBankRepositoryInterface
{
    public function __construct(QuestionBank $model)
    {
        parent::__construct($model);
    }

    public function getWithCreator($search = null, $perPage = 10)
    {
        $query = $this->model->query()->with('creator');

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        return $query->latest()->paginate($perPage);
    }

    public function getWithRelations($id, array $relations)
    {
        return $this->model->with($relations)->findOrFail($id);
    }
}
