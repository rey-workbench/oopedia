<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Enums\Lms\QuestionDifficulty;
use App\Models\Material;
use App\Models\Question;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface QuizServiceInterface
{
    // --- Question Management (from QuestionServiceInterface) ---
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

    // --- Quiz Listing & Data (from QuestionListingServiceInterface) ---
    public function getQuizData(
        Material $material,
        ?QuestionDifficulty $difficulty,
        string $userId,
        bool $isGuest,
        array $guestProgress = [],
        ?QuestionDifficulty $targetDifficulty = null,
    ): array;

    public function getMaterialsListWithStudentCount(
        string $userId,
        bool $isGuest,
        array $guestProgress = [],
    ): Collection;

    public function getReviewQuestions(
        Material $material,
        ?QuestionDifficulty $difficulty,
        string $userId,
        bool $isGuest,
        array $guestProgress = [],
    ): Collection;

    public function getGuestAnsweredQuestionIds(string $materialId, array $guestProgress = []): SupportCollection;

    public function getLevelProgress(
        Material $material,
        ?QuestionDifficulty $difficulty,
        SupportCollection|Collection $answeredQuestionIds,
        bool $isGuest = false,
        ?Collection $preloadedQuestions = null,
    ): array;

    // --- Answer Logic (from QuestionAnswerServiceInterface) ---
    public function determineCorrectness(Question $question, array $data): bool;

    // --- Quiz Orchestration (from QuizOrchestratorServiceInterface) ---
    public function handleSubmission(
        string $userId,
        string $materialId,
        string $questionId,
        array $validatedData,
    ): array;
}
