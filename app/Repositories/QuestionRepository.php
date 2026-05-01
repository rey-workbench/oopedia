<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Enums\Lms\QuestionDifficulty;
use App\Models\Question;
use Illuminate\Contracts\Database\Query\Builder;
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
    ): Collection {
        $query = Question::where('material_id', '=', $materialId);

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
            ->when($search, fn ($query) => $query->where(
                function (Builder $q) use ($search): void {
                    $q->where('question_text', 'like', sprintf('%%%s%%', $search))
                        ->orWhere('question_type', 'like', sprintf('%%%s%%', $search))
                        ->orWhereHas(
                            'createdBy',
                            function (Builder $userQuery) use ($search): void {
                                $userQuery->where('name', 'like', sprintf('%%%s%%', $search));
                            },
                        )
                        ->orWhereHas(
                            'material',
                            function (Builder $materialQuery) use ($search): void {
                                $materialQuery->where('title', 'like', sprintf('%%%s%%', $search));
                            },
                        );
                },
            ))
            ->when($difficulty, fn ($query) => $query->where('difficulty', '=', $difficulty))
            ->when($materialId, fn ($query) => $query->where('material_id', '=', $materialId))->latest()
            ->paginate(15);
    }

    public function countByMaterialAndDifficulty(string $materialId, QuestionDifficulty $questionDifficulty): int
    {
        return Question::where('material_id', '=', $materialId)
            ->where('difficulty', '=', $questionDifficulty->value)
            ->count('*');
    }
}
