<?php

namespace App\Contracts\Services;

use App\Enums\Lms\QuestionDifficulty;
use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface QuestionListingServiceInterface
{
    public function getQuizData(
        Material $material,
        ?QuestionDifficulty $difficulty,
        string $userId,
        bool $isGuest,
        array $guestProgress = [],
        ?string $subMaterialId = null,
        ?QuestionDifficulty $targetDifficulty = null,
    ): array;

    public function getMaterialsListWithStudentCount(
        string $userId,
        bool $isGuest,
        array $guestProgress = [],
        array $unlockedModules = [],
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
        SupportCollection $answeredQuestionIds,
        bool $isGuest = false,
        ?Collection $preloadedQuestions = null,
    ): array;
}
