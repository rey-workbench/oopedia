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
    public function getFilteredQuestions(?string $search = null, ?string $difficulty = null, ?int $materialId = null): LengthAwarePaginator;

    /**
     * Get available questions for the question bank (not yet assigned).
     */
    public function getAvailableQuestionsForBank(int $materialId, array $excludeIds, ?string $search = null, ?string $difficulty = null): LengthAwarePaginator;

    /**
     * Get a question by its ID.
     */
    public function getQuestionById(int $id): ?Question;

    /**
     * Get a question with its answers loaded.
     */
    public function getQuestionWithAnswers(int $id): ?Question;

    /**
     * Check if a question exists for given material and difficulty.
     */
    public function existsByMaterialAndDifficulty(int $materialId, string $difficulty): bool;

    /**
     * Create a new question with its answers.
     */
    public function createQuestion(array $data): Question;

    /**
     * Update an existing question and its answers.
     */
    public function updateQuestion(int $questionId, array $data): Question;

    /**
     * Delete a question and all related data.
     */
    public function deleteQuestion(int $questionId): void;
}
