<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\Question\QuestionCreateDTO;
use App\DTOs\Question\QuestionUpdateDTO;
use App\DTOs\Quiz\QuizContextDTO;
use App\DTOs\Quiz\QuizSubmissionDTO;
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
        ?QuestionDifficulty $questionDifficulty = null,
        ?string $materialId = null,
    ): LengthAwarePaginator;

    public function getQuestionById(string $id): ?array;

    public function getQuestionWithAnswers(string $id): ?array;

    public function createQuestion(QuestionCreateDTO $questionCreateDTO): Question;

    public function updateQuestion(string $questionId, QuestionUpdateDTO $questionUpdateDTO): Question;

    public function deleteQuestion(string $questionId): void;

    // --- Quiz Listing & Data (from QuestionListingServiceInterface) ---
    public function getQuizData(QuizContextDTO $quizContextDTO): array;

    public function getMaterialsListWithStudentCount(
        \App\DTOs\Quiz\MaterialProgressDTO $materialProgressDTO,
    ): SupportCollection;

    public function getReviewQuestions(QuizContextDTO $quizContextDTO): SupportCollection;

    public function getGuestAnsweredQuestionIds(string $materialId, array $guestProgress = [], bool $onlyCorrect = false): SupportCollection;

    public function getLevelProgress(
        Material $material,
        ?QuestionDifficulty $questionDifficulty,
        SupportCollection|Collection $answeredQuestionIds,
        bool $isGuest = false,
        ?Collection $preloadedQuestions = null,
    ): array;

    // --- Answer Logic (from QuestionAnswerServiceInterface) ---
    public function determineCorrectness(Question $question, array $data): bool;

    // --- Quiz Orchestration (from QuizOrchestratorServiceInterface) ---
    public function handleSubmission(
        QuizSubmissionDTO $quizSubmissionDTO,
    ): array;
}
