<?php

namespace App\Repositories;

use App\Models\Answer;

class AnswerRepository
{


    public function getCorrectAnswer($questionId)
    {
        return Answer::where('question_id', $questionId)
            ->where('is_correct', true)
            ->first();
    }
}
