<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\Question\QuestionCreateDTO;
use App\DTOs\Question\QuestionUpdateDTO;
use App\Enums\Lms\QuestionDifficulty;
use App\Models\Question;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface QuestionServiceInterface
{
    public function getFilteredQuestions(
        ?string $search = null,
        ?QuestionDifficulty $questionDifficulty = null,
        ?string $materialId = null,
    ): LengthAwarePaginator;

    public function getQuestionById(string $id): ?array;

    public function getQuestionWithAnswers(string $id): ?array;

    public function createQuestion(QuestionCreateDTO $questionCreateDTO): Question;

    public function updateQuestion(string $questionId, QuestionUpdateDTO $questionUpdateDTO): Question;

    public function deleteQuestion(string $questionId): void;
}
