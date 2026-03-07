<?php

namespace App\Repositories;

use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Models\Question;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class QuestionRepository implements QuestionRepositoryInterface
{
    /** @return Collection<int, Question> */
    public function all(): Collection
    {
        return Question::all();
    }

    public function find(string $id): ?Question
    {
        return Question::query()->find($id, ['*']);
    }

    public function create(array $data): Question
    {
        return Question::query()->create($data);
    }

    public function update(string $id, array $data): ?Question
    {
        $question = Question::query()->find($id, ['*']);

        if ($question) {
            $question->update($data);

            return $question;
        }

        return null;
    }

    public function delete(string $id): bool
    {
        $question = Question::query()->find($id, ['*']);

        if ($question) {
            return (bool)$question->delete();
        }

        return false;
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Question::query()->paginate($perPage, ['*'], 'page', null);
    }

    public function countAll(): int
    {
        return Question::query()->count('*');
    }

    public function findWithAnswers(string $id): Question
    {
        return Question::query()->with('answers')->findOrFail($id);
    }

    /** @return Collection<int, Question> */
    public function getByMaterialAndDifficulty(string $materialId, ?string $difficulty = null, ?string $subMaterialId = null): Collection
    {
        $query = Question::query()->where('material_id', '=', $materialId);

        if ($subMaterialId) {
            $query->where('sub_material_id', '=', $subMaterialId);
        }

        if ($difficulty && $difficulty !== 'all') {
            $query->where('difficulty', '=', $difficulty);
        }

        return $query->orderBy('id')->get();
    }

    public function getFilteredQuestions(
        ?string $search = null,
        ?string $difficulty = null,
        ?string $materialId = null,
        ): LengthAwarePaginator
    {
        return Question::query()->with(['createdBy', 'answers', 'material'])
            ->when($search, function ($query) use ($search) {
            return $query->where(function ($q) use ($search) {
                    $q->where('question_text', 'like', "%{$search}%")
                        ->orWhere('question_type', 'like', "%{$search}%")
                        ->orWhereHas('createdBy', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                }
                )
                    ->orWhereHas('material', function ($materialQuery) use ($search) {
                    $materialQuery->where('title', 'like', "%{$search}%");
                }
                );
            }
            );
        })
            ->when($difficulty, function ($query) use ($difficulty) {
            return $query->where('difficulty', '=', $difficulty);
        })
            ->when($materialId, function ($query) use ($materialId) {
            return $query->where('material_id', '=', $materialId);
        })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function getQuestionsForBank(
        string $materialId,
        array $excludeIds,
        ?string $search = null,
        ?string $difficulty = null,
        ): LengthAwarePaginator
    {
        return Question::query()->with(['material', 'answers'])
            ->where('material_id', '=', $materialId)
            ->whereNotIn('id', $excludeIds)
            ->when($search, function ($query) use ($search) {
            $query->where('question_text', 'like', "%{$search}%");
        })
            ->when($difficulty, function ($query) use ($difficulty) {
            $query->where('difficulty', '=', $difficulty);
        })
            ->paginate(10);
    }

    public function countByMaterialAndDifficulty(string $materialId, string $difficulty): int
    {
        return Question::query()->where('material_id', '=', $materialId)
            ->where('difficulty', '=', $difficulty)
            ->count('*');
    }

    public function existsByMaterialAndDifficulty(string $materialId, string $difficulty): bool
    {
        return Question::query()->where('material_id', '=', $materialId)
            ->where('difficulty', '=', $difficulty)
            ->exists();
    }

    public function countByMaterial(string $materialId): int
    {
        return Question::query()->where('material_id', '=', $materialId)->count('*');
    }
}
