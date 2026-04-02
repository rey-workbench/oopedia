<?php

namespace App\Contracts\Services;

use App\Models\Question;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Contract for questions management service.
 */
interface QuestionServiceInterface
{
    /**
     * Get paginated, filtered questions.
     */
    public function getFilteredQuestions(
        ?string $search = null,
        ?string $difficulty = null,
        ?string $materialId = null,
    ): LengthAwarePaginator;

    /**
     * Get available questions for the question bank (not yet assigned).
     */
    public function getAvailableQuestionsForBank(
        string $materialId,
        array $excludeIds,
        ?string $search = null,
        ?string $difficulty = null,
    ): LengthAwarePaginator;

    /**
     * Get a question by its ID.
     */
    public function getQuestionById(string $id): ?Question;

    /**
     * Get a question with its answers loaded.
     */
    public function getQuestionWithAnswers(string $id): ?Question;

    /**
     * Check if a question exists for given material and difficulty.
     */
    public function existsByMaterialAndDifficulty(string $materialId, string $difficulty): bool;

    /**
     * Create a new question with its answers.
     */
    public function createQuestion(array $data): Question;

    /**
     * Update an existing question and its answers.
     */
    public function updateQuestion(string $questionId, array $data): Question;

    /**
     * Delete a question and all related data.
     */
    public function deleteQuestion(string $questionId): void;
}
