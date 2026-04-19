<?php

namespace App\Contracts\Services;

use App\Enums\Lms\QuestionDifficulty;
use App\Models\Question;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface QuestionServiceInterface
{
    public function getFilteredQuestions(
        ?string $search = null,
        ?QuestionDifficulty $difficulty = null,
        ?string $materialId = null,
    ): LengthAwarePaginator;

    public function getQuestionById(string $id): ?Question;

    public function getQuestionWithAnswers(string $id): ?Question;

    public function createQuestion(array $data): Question;

    public function updateQuestion(string $questionId, array $data): Question;

    public function deleteQuestion(string $questionId): void;
}
