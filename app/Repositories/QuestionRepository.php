<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Enums\Lms\QuestionDifficulty;
use App\Models\Question;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class QuestionRepository implements QuestionRepositoryInterface
{
    public function find(string $id): ?Question
    {
        return Question::find($id);
    }

    public function create(array $data): Question
    {
        return Question::create($data);
    }

    public function update(string $id, array $data): ?Question
    {
        $question = Question::find($id);

        if (! $question) {
            return null;
        }

        $question->update($data);

        return $question;
    }

    public function delete(string $id): bool
    {
        $question = Question::find($id);

        if (! $question) {
            return false;
        }

        return (bool) $question->delete();
    }

    public function countAll(): int
    {
        return Question::count('*');
    }

    public function findWithAnswers(string $id): Question
    {
        return Question::with('answers')->findOrFail($id);
    }

    public function getByMaterialAndDifficulty(
        string $materialId,
        ?string $difficulty = null,
        ?string $subMaterialId = null,
    ): Collection {
        $query = Question::where('material_id', '=', $materialId);

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
    ): LengthAwarePaginator {
        return Question::with(['createdBy', 'answers', 'material'])
            ->when($search, function ($query) use ($search) {
                return $query->where(
                    function ($q) use ($search) {
                        $q->where('question_text', 'like', "%{$search}%")
                            ->orWhere('question_type', 'like', "%{$search}%")
                            ->orWhereHas(
                                'createdBy',
                                function ($userQuery) use ($search) {
                                    $userQuery->where('name', 'like', "%{$search}%");
                                },
                            )
                            ->orWhereHas(
                                'material',
                                function ($materialQuery) use ($search) {
                                    $materialQuery->where('title', 'like', "%{$search}%");
                                },
                            );
                    },
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

    public function countByMaterial(string $materialId): int
    {
        return Question::where('material_id', '=', $materialId)->count('*');
    }

    public function countByMaterialAndDifficulty(string $materialId, QuestionDifficulty $difficulty): int
    {
        return Question::where('material_id', '=', $materialId)
            ->where('difficulty', '=', $difficulty->value)
            ->count('*');
    }
}
