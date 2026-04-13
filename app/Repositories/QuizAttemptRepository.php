<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\QuizAttemptRepositoryInterface;
use App\Models\QuizAttempt;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class QuizAttemptRepository implements QuizAttemptRepositoryInterface
{
    public function create(array $data): QuizAttempt
    {
        return DB::transaction(function () use ($data) {
            $data['attempt_number'] ??= QuizAttempt::where('user_id', $data['user_id'])
                ->where('question_id', $data['question_id'])
                ->lockForUpdate()
                ->count() + 1;

            return QuizAttempt::create($data);
        });
    }

    public function find(string $id): ?QuizAttempt
    {
        return QuizAttempt::find($id);
    }

    public function getByUser(string $userId): Collection
    {
        return QuizAttempt::where('user_id', $userId)
            ->with(['question', 'answer'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByMaterial(string $materialId): Collection
    {
        return QuizAttempt::whereHas('question', fn ($q) => $q->where('material_id', $materialId))
            ->with(['question', 'user'])
            ->get();
    }
}
