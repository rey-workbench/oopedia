<?php

declare(strict_types=1);

namespace App\Services\Lms;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Services\QuestionListingServiceInterface;
use App\Enums\Lms\QuestionDifficulty;
use App\Enums\Lms\QuestionType;
use App\Helpers\ProgressHelper;
use App\Models\Material;
use App\Models\Question;
use App\Schemas\StudentStateSchema;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

final class QuestionListingService implements QuestionListingServiceInterface
{
    public function __construct(
        public readonly MaterialRepositoryInterface $materialRepo,
        public readonly ProgressRepositoryInterface $progressRepo,
        public readonly QuestionRepositoryInterface $questionRepo,
    ) {}

    public function getQuizData(
        Material $material,
        ?QuestionDifficulty $difficulty,
        string $userId,
        bool $isGuest,
        array $guestProgress = [],
        ?string $subMaterialId = null,
        ?QuestionDifficulty $targetDifficulty = null,
    ): array {
        $answeredQuestionIds = $isGuest
            ? $this->getGuestAnsweredQuestionIds($material->id, $guestProgress)
            : $this->progressRepo->getAnsweredQuestionIds($userId, $material->id);

        $filteredData           = $this->getFilteredQuestions($material, $difficulty, $isGuest, $subMaterialId);
        $questions              = $filteredData['questions'];
        $allQuestions           = $questions;
        $totalFilteredQuestions = $filteredData['totalFilteredQuestions'];

        $appliedTargetFilter = false;

        $shouldApplyTargetDifficulty = $difficulty === null && $targetDifficulty && ! $isGuest;

        if ($shouldApplyTargetDifficulty) {
            $attemptedCount = $this->progressRepo->getAttemptedQuestionIds($userId, $material->id)->count();
            $totalQuestions = $this->questionRepo->countByMaterial($material->id);

            $progressPercentage = $totalQuestions > 0
                ? ($attemptedCount / $totalQuestions) * 100
                : 0;

            // Only enforce fast-track filtering when learner is ready for module progression (G26).
            $shouldApplyTargetDifficulty = $progressPercentage >= StudentStateSchema::THRESHOLD_SATISFACTORY_PROGRESS;
        }

        if ($shouldApplyTargetDifficulty) {
            $difficultyOrder = Question::DIFFICULTY_ORDER;
            $targetLevel     = $difficultyOrder[$targetDifficulty->value] ?? 1;

            $answeredArray = $answeredQuestionIds->toArray();
            $questions     = $questions->reject(function ($q) use ($answeredArray, $difficultyOrder, $targetLevel) {
                $qLevel = $difficultyOrder[$q->difficulty->value] ?? 1;

                return ! in_array($q->id, $answeredArray) && $qLevel < $targetLevel;
            });
            $appliedTargetFilter = true;
        }

        $currentQuestion = $this->getCurrentQuestion($questions, $answeredQuestionIds);

        // Fallback: if target filtering hides all remaining questions, show unanswered questions from the full set.
        if ($appliedTargetFilter && $currentQuestion === null) {
            $fallbackQuestion = $this->getCurrentQuestion($allQuestions, $answeredQuestionIds);

            if ($fallbackQuestion !== null) {
                $questions       = $allQuestions;
                $currentQuestion = $fallbackQuestion;
            }
        }

        $levelProgress = $this->getLevelProgress($material, $difficulty, $answeredQuestionIds, $isGuest, $questions);

        $shuffledQuestions = $questions->groupBy('difficulty')->map(function ($group) {
            return $group->shuffle();
        })->flatten(1);

        $questions = new Collection($shuffledQuestions->all());

        if ($currentQuestion === null) {
            $currentQuestion = $this->getCurrentQuestion($questions, $answeredQuestionIds);
        }

        $answeredArray       = $answeredQuestionIds->toArray();
        $actualAnsweredCount = $allQuestions->filter(fn ($q) => in_array($q->id, $answeredArray))->count();

        return [
            'material'              => $material,
            'questions'             => $questions,
            'currentQuestion'       => $currentQuestion,
            'currentQuestionNumber' => $actualAnsweredCount + 1,
            'totalQuestions'        => $totalFilteredQuestions,
            'answeredCount'         => $actualAnsweredCount,
            'materialAnsweredCount' => $answeredQuestionIds->count(),
            'levelProgress'         => $levelProgress,
            'difficulty'            => $difficulty ? $difficulty->value : 'all',
        ];
    }

    public function getMaterialsListWithStudentCount(
        string $userId,
        bool $isGuest,
        array $guestProgress = [],
        array $unlockedModules = [],
    ): Collection {
        $progressStats = $isGuest ? collect([]) : $this->progressRepo->getUserProgressStats($userId);
        $allMaterials  = $this->materialRepo->getAllWithQuestions();

        if ($isGuest) {
            $totalMaterials  = $allMaterials->count();
            $materialsToShow = (int) ceil($totalMaterials / 2);
            $allMaterials    = $allMaterials->take($materialsToShow);
        }

        $studentCounts = $this->progressRepo->getStudentCountByMaterial();

        $firstModuleId = $allMaterials->whereNotNull('module_id')->min('module_id');

        $materials = $allMaterials->map(function ($material) use (
            $progressStats,
            $isGuest,
            $studentCounts,
            $guestProgress,
            $unlockedModules,
            $firstModuleId
        ) {
            $configuredTotalQuestions = ProgressHelper::calculateMaterialQuestionCounts($material, $isGuest)['total'];

            if ($isGuest) {
                $answeredCount = 0;
                foreach ($guestProgress as $key => $progress) {
                    if (
                        strpos($key, $material->id . '_') === 0 &&
                        (isset($progress['is_correct']) || isset($progress['attempt_number']))
                    ) {
                        $answeredCount++;
                    }
                }
            } else {
                $materialProgress = $progressStats->firstWhere('material_id', $material->id);
                $answeredCount    = $materialProgress ? $materialProgress->answered_questions : 0;
            }

            $progressPercentage = ProgressHelper::calculateProgressPercentage(
                $answeredCount,
                $configuredTotalQuestions,
            );

            $studentCount = $studentCounts->firstWhere('material_id', $material->id)?->student_count ?? 0;

            $material->progress_percentage = $progressPercentage;
            $material->total_questions     = $configuredTotalQuestions;
            $material->completed_questions = $answeredCount;
            $material->student_count       = $studentCount;

            $moduleId      = $material->module_id;
            $isFirstModule = $moduleId !== null && (string) $moduleId === (string) $firstModuleId;
            $isUnlocked    = $isGuest ||
                $isFirstModule        ||
                empty($moduleId)      ||
                in_array((string) $moduleId, array_map('strval', $unlockedModules));
            $material->is_locked = ! $isUnlocked;

            return $material;
        });

        return $materials;
    }

    public function getReviewQuestions(
        Material $material,
        ?QuestionDifficulty $difficulty,
        string $userId,
        bool $isGuest,
        array $guestProgress = [],
    ): Collection {
        $questions = $material->questions;

        if ($difficulty !== null) {
            $dbDifficulty = $difficulty->value;
            $questions    = $questions->where('difficulty', $dbDifficulty);
        }

        if ($isGuest) {
            $answeredQuestionIds = $this->getGuestAnsweredQuestionIds($material->id, $guestProgress);
            $questions           = $questions->whereIn('id', $answeredQuestionIds->toArray());

            foreach ($questions as $question) {
                $key = $material->id . '_' . $question->id;
                if (isset($guestProgress[$key])) {
                    $question->user_attempt = $guestProgress[$key];
                }
            }
        } else {
            $answeredQuestionIds = $this->progressRepo->getAnsweredQuestionIds($userId, $material->id);
            $questions           = $questions->whereIn('id', $answeredQuestionIds->toArray());

            $latestAttempts = $this->progressRepo->getLatestAttemptsForQuestions(
                $userId,
                $answeredQuestionIds->toArray(),
            );

            foreach ($questions as $question) {
                $attempt = $latestAttempts->get($question->id);
                if ($attempt) {
                    $question->user_attempt = [
                        'score'          => $attempt->score,
                        'is_correct'     => $attempt->is_correct,
                        'answer_id'      => $attempt->answer_id,
                        'user_response'  => $attempt->user_response,
                        'attempt_number' => $attempt->attempt_number,
                        'time_spent'     => $attempt->time_spent,
                    ];
                }
            }
        }

        return $questions->values();
    }

    public function getGuestAnsweredQuestionIds(string $materialId, array $guestProgress = []): SupportCollection
    {
        $answeredQuestionIds = collect([]);

        foreach ($guestProgress as $key => $progress) {
            if (! is_array($progress) || (! isset($progress['is_correct']) && ! isset($progress['attempt_number']))) {
                continue;
            }

            $parts = explode('_', $key);
            if (count($parts) < 2 || $parts[0] != $materialId) {
                continue;
            }

            $questionId = $parts[1];
            if (! $answeredQuestionIds->contains($questionId)) {
                $answeredQuestionIds->push($questionId);
            }
        }

        return $answeredQuestionIds;
    }

    public function getFilteredQuestions(
        Material $material,
        ?QuestionDifficulty $difficulty,
        bool $isGuest,
        ?string $subMaterialId = null,
    ): array {
        $diffValue = $difficulty ? $difficulty->value : 'all';
        $questions = $this->questionRepo->getByMaterialAndDifficulty($material->id, $diffValue, $subMaterialId);

        if ($isGuest) {
            if ($difficulty === null) {
                $beginnerQuestions      = $questions->where('difficulty', QuestionDifficulty::BEGINNER->value)->take(3);
                $mediumQuestions        = $questions->where('difficulty', QuestionDifficulty::MEDIUM->value)->take(3);
                $hardQuestions          = $questions->where('difficulty', QuestionDifficulty::HARD->value)->take(3);
                $questions              = $beginnerQuestions->concat($mediumQuestions)->concat($hardQuestions);
                $totalFilteredQuestions = 9;
            } else {
                $questions              = $questions->take(3);
                $totalFilteredQuestions = 3;
            }
        } else {
            $totalFilteredQuestions = $questions->count();
        }

        return [
            'questions'              => $questions,
            'totalFilteredQuestions' => $totalFilteredQuestions,
        ];
    }

    public function getCurrentQuestion(Collection $questions, SupportCollection $answeredQuestionIds): ?Question
    {
        $answeredArray   = $answeredQuestionIds->toArray();
        /** @var Question|null $currentQuestion */
        $currentQuestion = $questions->reject(function ($question) use ($answeredArray) {
            return in_array($question->id, $answeredArray);
        })->first();

        $isNotFillInTheBlank = $currentQuestion instanceof Question &&
            $currentQuestion->question_type !== QuestionType::FILL_IN_THE_BLANK;

        if ($isNotFillInTheBlank) {
            if (! $currentQuestion->relationLoaded('answers')) {
                $currentQuestion->load('answers');
            }
            $currentQuestion->setRelation('answers', $currentQuestion->answers->shuffle());
        }

        return $currentQuestion;
    }

    public function getLevelProgress(
        Material $material,
        ?QuestionDifficulty $difficulty,
        SupportCollection|Collection $answeredQuestionIds,
        bool $isGuest = false,
        SupportCollection|Collection|null $preloadedQuestions = null,
    ): array {
        $questions = $preloadedQuestions !== null
            ? $preloadedQuestions
            : $this->questionRepo->getByMaterialAndDifficulty($material->id, $difficulty ? $difficulty->value : 'all');

        if ($preloadedQuestions === null && $isGuest) {
            $questions = $questions->take($difficulty === null ? 9 : 3);
        }

        $answeredArray = $answeredQuestionIds->toArray();
        $completed     = $questions->filter(fn ($q) => in_array($q->id, $answeredArray));
        $remaining     = $questions->reject(fn ($q) => in_array($q->id, $answeredArray));

        $levels = [];
        $index  = 1;

        foreach ($completed as $question) {
            if (! $question instanceof Question) {
                continue;
            }

            $levels[] = [
                'level'       => $index++,
                'question_id' => $question->id,
                'status'      => 'completed',
            ];
        }

        $isFirst = true;

        foreach ($remaining as $question) {
            if (! $question instanceof Question) {
                continue;
            }

            $levels[] = [
                'level'       => $index++,
                'question_id' => $question->id,
                'status'      => $isFirst ? 'unlocked' : 'locked',
            ];
            $isFirst = false;
        }

        return $levels;
    }
}
