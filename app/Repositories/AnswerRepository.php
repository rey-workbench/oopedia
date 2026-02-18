<?php

namespace App\Repositories;

use App\Contracts\Repositories\AnswerRepositoryInterface;
use App\Models\Answer;
use Illuminate\Database\Eloquent\Collection;

class AnswerRepository implements AnswerRepositoryInterface
{
    /** @return Collection<int, Answer> */
    public function all(): Collection
    {
        return Answer::all();
    }

    public function find(int $id): ?Answer
    {
        return Answer::find($id);
    }

    public function findOrFail(int $id): Answer
    {
        return Answer::findOrFail($id);
    }

    public function create(array $data): Answer
    {
        return Answer::create($data);
    }

    public function update(int $id, array $data): ?Answer
    {
        $answer = $this->findOrFail($id);
        $answer->update($data);

        return $answer;
    }

    public function delete(int $id): bool
    {
        $answer = $this->findOrFail($id);

        return (bool) $answer->delete();
    }

    /** @return Collection<int, Answer> */
    public function getByQuestionId(int $questionId): Collection
    {
        return Answer::where('question_id', $questionId)->get();
    }

    /** @return Collection<int, Answer> */
    public function getCorrectAnswers(int $questionId): Collection
    {
        return Answer::where('question_id', $questionId)
            ->where('is_correct', true)
            ->get();
    }

    public function deleteByQuestionId(int $questionId): bool
    {
        return (bool) Answer::where('question_id', $questionId)->delete();
    }
}
