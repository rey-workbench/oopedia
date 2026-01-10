<?php

namespace App\Repositories;

use App\Models\QuestionBankConfig;

class QuestionBankRepository
{

    public function getWithCreator($search = null, $perPage = 10)
    {
        $query = QuestionBankConfig::query()->with('creator');

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        return $query->latest()->paginate($perPage);
    }

    public function getWithRelations($id, array $relations)
    {
        return QuestionBankConfig::with($relations)->findOrFail($id);
    }
}
