<?php

namespace App\Contracts\Services;

use App\Models\Question;

interface QuestionAnswerServiceInterface
{
    public function determineCorrectness(Question $question, array $data): bool;
}
