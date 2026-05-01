<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\AnswerRepositoryInterface;
use App\Models\Answer;
use Illuminate\Database\Eloquent\Collection;

final class AnswerRepository implements AnswerRepositoryInterface
{
    public function all(): Collection
    {
        return Answer::select(['id', 'question_id', 'is_correct', 'answer_text'])->get();
    }

    public function create(array $data): Answer
    {
        return Answer::create($data);
    }

    public function deleteByQuestionId(string $questionId): bool
    {
        return (bool) Answer::where('question_id', $questionId)->delete();
    }
}
