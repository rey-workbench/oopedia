<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\AnswerRepositoryInterface;
use App\Models\Answer;
use Illuminate\Database\Eloquent\Collection;

final class AnswerRepository implements AnswerRepositoryInterface
{
    /** @return Collection<string, Answer> */
    public function all(): Collection
    {
        return Answer::all();
    }

    public function find(string $id): ?Answer
    {
        return Answer::find($id);
    }

    public function findOrFail(string $id): Answer
    {
        return Answer::findOrFail($id);
    }

    public function create(array $data): Answer
    {
        return Answer::create($data);
    }

    public function update(string $id, array $data): ?Answer
    {
        $answer = $this->findOrFail($id);
        $answer->update($data);

        return $answer;
    }

    public function delete(string $id): bool
    {
        $answer = $this->findOrFail($id);

        return (bool) $answer->delete();
    }

    /** @return Collection<string, Answer> */
    public function getByQuestionId(string $questionId): Collection
    {
        return Answer::where('question_id', '=', $questionId)->get();
    }

    /** @return Collection<string, Answer> */
    public function getCorrectAnswers(string $questionId): Collection
    {
        return Answer::where('question_id', '=', $questionId)
            ->where('is_correct', '=', true)
            ->get();
    }

    public function deleteByQuestionId(string $questionId): bool
    {
        return (bool) Answer::where('question_id', '=', $questionId)->delete();
    }
}
