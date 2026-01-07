<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\AnswerRepositoryInterface;
use App\Models\Answer;

class AnswerRepository extends BaseRepository implements AnswerRepositoryInterface
{
    public function __construct(Answer $model)
    {
        parent::__construct($model);
    }

    public function getCorrectAnswer($questionId)
    {
        return $this->model
            ->where('question_id', $questionId)
            ->where('is_correct', true)
            ->first();
    }
}
