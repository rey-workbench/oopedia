<?php

namespace App\Contracts\Services;

use App\Models\Question;

interface QuestionAnswerServiceInterface
{
    public function checkAnswer(array $data, string $userId, bool $isGuest): array;

    public function checkAllAnswers(array $data, string $userId): array;

    public function determineCorrectness(Question $question, array $data): bool;
}
