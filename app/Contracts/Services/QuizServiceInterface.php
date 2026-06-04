<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\Quiz\MaterialProgressDTO;
use App\DTOs\Quiz\QuizContextDTO;
use App\Enums\Lms\QuestionDifficulty;
use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface QuizServiceInterface
{
    public function getQuizData(QuizContextDTO $quizContextDTO): array;

    public function getMaterialsListWithStudentCount(
        MaterialProgressDTO $materialProgressDTO,
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
}
