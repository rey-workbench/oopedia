<?php

namespace App\Contracts\Services;

use App\Models\Question;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface QuestionServiceInterface
{
    public function getFilteredQuestions(
        ?string $search = null,
        ?string $difficulty = null,
        ?string $materialId = null,
    ): LengthAwarePaginator;

    public function getAvailableQuestionsForBank(
        string $materialId,
        array $excludeIds,
        ?string $search = null,
        ?string $difficulty = null,
    ): LengthAwarePaginator;

    public function getQuestionById(string $id): ?Question;

    public function getQuestionWithAnswers(string $id): ?Question;

    public function existsByMaterialAndDifficulty(string $materialId, string $difficulty): bool;

    public function createQuestion(array $data): Question;

    public function updateQuestion(string $questionId, array $data): Question;

    public function deleteQuestion(string $questionId): void;
}
