<?php

declare(strict_types=1);

namespace App\Services\Lms;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Contracts\Services\QuizServiceInterface;
use App\DTOs\Quiz\MaterialProgressDTO;
use App\DTOs\Quiz\QuizContextDTO;
use App\Enums\Lms\QuestionDifficulty;
use App\Helpers\ProgressHelper;
use App\Http\Resources\MaterialResource;
use App\Http\Resources\QuestionResource;
use App\Models\Material;
use App\Models\Question;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

final readonly class QuizService implements QuizServiceInterface
{
    public function __construct(
        private QuestionRepositoryInterface $questionRepository,
        private MaterialRepositoryInterface $materialRepository,
        private ProgressRepositoryInterface $progressRepository,
        private PerformanceServiceInterface $performanceService,
        private GuestProgressServiceInterface $guestProgressService,
    ) {}

    public function getQuizData(QuizContextDTO $quizContextDTO): array
    {
        $material         = $quizContextDTO->material;
        $difficulty       = $quizContextDTO->difficulty;
        $userId           = $quizContextDTO->userId;
        $isGuest          = $quizContextDTO->isGuest;
        $guestProgress    = $quizContextDTO->guestProgress;
        $targetDifficulty = $quizContextDTO->targetDifficulty;

        $answeredQuestionIds = $isGuest
            ? $this->getGuestAnsweredQuestionIds($material->id, $guestProgress, true)
            : $this->progressRepository->getAnsweredQuestionIds($userId, $material->id);

        $filteredData           = $this->getFilteredQuestionsForQuiz($material, $difficulty, $isGuest);
        $questions              = $filteredData['questions'];
        $allQuestions           = $questions;
        $totalFilteredQuestions = $filteredData['totalFilteredQuestions'];

        $appliedTargetFilter         = false;
        $shouldApplyTargetDifficulty = ! $difficulty instanceof QuestionDifficulty && $targetDifficulty instanceof QuestionDifficulty && ! $isGuest;

        if ($shouldApplyTargetDifficulty) {
            $difficultyOrder = Question::DIFFICULTY_ORDER;
            $targetLevel     = $difficultyOrder[$targetDifficulty->value] ?? 1;

            $answeredArray = $answeredQuestionIds->toArray();

            $attemptedIds = $isGuest
                ? $this->getGuestAnsweredQuestionIds($material->id, $guestProgress, false)->toArray()
                : $this->progressRepository->getAttemptedQuestionIds($userId, $material->id)->toArray();

            $questions = $questions->reject(function ($q) use ($answeredArray, $attemptedIds, $targetLevel, $difficultyOrder): bool {
                $qLevel      = $difficultyOrder[$q->difficulty->value] ?? 1;
                $isCorrect   = in_array($q->id, $answeredArray);
                $isAttempted = in_array($q->id, $attemptedIds);
                if ($isCorrect) {
                    return true;
                }

                return ! $isAttempted && $qLevel < $targetLevel;
            });
            $appliedTargetFilter = true;
        }

        if (! $isGuest) {
            $state         = $this->performanceService->findOrCreateStudentState($userId);
            $adaptiveState = $state->adaptive_state ?? [];

            if (($adaptiveState['forced_easy_count'] ?? 0) > 0) {
                $answeredArray  = $answeredQuestionIds->toArray();
                $easyUnanswered = $allQuestions
                    ->where('difficulty', QuestionDifficulty::BEGINNER)
                    ->reject(fn ($q): bool => in_array($q->id, $answeredArray))
                    ->take($adaptiveState['forced_easy_count']);

                if ($easyUnanswered->isNotEmpty()) {
                    $questions           = $easyUnanswered;
                    $appliedTargetFilter = true;
                }

                $adaptiveState['forced_easy_count'] = max(0, $adaptiveState['forced_easy_count'] - 1);
                $state->adaptive_state              = $adaptiveState;
                $state->save();
            }
        }

        $currentQuestion = $this->getCurrentQuestion($questions, $answeredQuestionIds);

        if ($appliedTargetFilter && ! $currentQuestion instanceof Question) {
            $fallbackQuestion = $this->getCurrentQuestion($allQuestions, $answeredQuestionIds);
            if ($fallbackQuestion instanceof Question) {
                $questions       = $allQuestions;
                $currentQuestion = $fallbackQuestion;
            }
        }

        $levelProgress     = $this->getLevelProgress($material, $difficulty, $answeredQuestionIds, $isGuest, $questions);
        $shuffledQuestions = $questions->groupBy('difficulty')->map(fn ($g) => $g->shuffle())->flatten(1);
        $questions         = new Collection($shuffledQuestions->all());

        if (! $currentQuestion instanceof Question) {
            $currentQuestion = $this->getCurrentQuestion($questions, $answeredQuestionIds);
        }

        $answeredArray       = $answeredQuestionIds->toArray();
        $actualAnsweredCount = $allQuestions->filter(fn ($q): bool => in_array($q->id, $answeredArray))->count();

        return [
            'material'                => new MaterialResource($material)->resolve(),
            'current_question'        => $currentQuestion instanceof Question ? new QuestionResource($currentQuestion)->resolve() : null,
            'current_question_number' => $actualAnsweredCount + 1,
            'total_questions'         => $totalFilteredQuestions,
            'answered_count'          => $actualAnsweredCount,
            'material_answered_count' => $answeredQuestionIds->count(),
            'level_progress'          => $levelProgress,
            'difficulty'              => $difficulty instanceof QuestionDifficulty ? $difficulty->value : 'all',
            'is_guest'                => $isGuest,
            'student_state'           => $isGuest ? $this->guestProgressService->getStudentSessionState() : $this->performanceService->getStudentSessionState($userId),
        ];
    }

    public function getMaterialsListWithStudentCount(
        MaterialProgressDTO $materialProgressDTO,
    ): SupportCollection {
        $userId        = $materialProgressDTO->userId;
        $isGuest       = $materialProgressDTO->isGuest;
        $guestProgress = $materialProgressDTO->guestProgress;

        $progressStats = $isGuest ? collect([]) : $this->progressRepository->getUserProgressStats($userId);
        $allMaterials  = $this->materialRepository->getAllWithQuestions();

        if ($isGuest) {
            $allMaterials = $allMaterials->take((int) ceil($allMaterials->count() / 2));
        }

        $studentCounts = $this->progressRepository->getStudentCountByMaterial();

        return $allMaterials->map(function ($material) use ($progressStats, $isGuest, $studentCounts, $guestProgress): array {
            $configuredTotalQuestions = ProgressHelper::calculateMaterialQuestionCounts($material, $isGuest)['total'];

            if ($isGuest) {
                $answeredCount = 0;
                foreach ($guestProgress as $key => $progress) {
                    if (str_starts_with($key, $material->id . '_') && (isset($progress['is_correct']) || isset($progress['attempt_number']))) {
                        $answeredCount++;
                    }
                }
            } else {
                $materialProgress = $progressStats->firstWhere('material_id', $material->id);
                $answeredCount    = $materialProgress ? $materialProgress->answered_questions : 0;
            }

            $material->progress_percentage = ProgressHelper::calculateProgressPercentage($answeredCount, $configuredTotalQuestions);
            $material->total_questions     = $configuredTotalQuestions;
            $material->completed_questions = $answeredCount;
            $material->student_count       = $studentCounts->firstWhere('material_id', $material->id)?->student_count ?? 0;
            $material->is_locked           = false;

            return new MaterialResource($material)->resolve();
        });
    }

    public function getReviewQuestions(QuizContextDTO $quizContextDTO): SupportCollection
    {
        $material      = $quizContextDTO->material;
        $difficulty    = $quizContextDTO->difficulty;
        $userId        = $quizContextDTO->userId;
        $isGuest       = $quizContextDTO->isGuest;
        $guestProgress = $quizContextDTO->guestProgress;

        $questions = $material->questions;
        if ($difficulty instanceof QuestionDifficulty) {
            $questions = $questions->where('difficulty', $difficulty->value);
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
            $answeredQuestionIds = $this->progressRepository->getAnsweredQuestionIds($userId, $material->id);
            $questions           = $questions->whereIn('id', $answeredQuestionIds->toArray());
            $latestAttempts      = $this->progressRepository->getLatestAttemptsForQuestions($userId, $answeredQuestionIds->toArray());
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

        return collect(QuestionResource::collection($questions->values())->resolve());
    }

    public function getGuestAnsweredQuestionIds(string $materialId, array $guestProgress = [], bool $onlyCorrect = false): SupportCollection
    {
        $answeredQuestionIds = collect([]);
        foreach ($guestProgress as $key => $progress) {
            if (! is_array($progress)) {
                continue;
            }

            if ($onlyCorrect && ! ($progress['is_correct'] ?? false)) {
                continue;
            }

            if (! isset($progress['is_correct']) && ! isset($progress['attempt_number'])) {
                continue;
            }

            $parts = explode('_', (string) $key);
            if (count($parts) < 2) {
                continue;
            }

            if ($parts[0] != $materialId) {
                continue;
            }

            if ($answeredQuestionIds->doesntContain($parts[1])) {
                $answeredQuestionIds->push($parts[1]);
            }
        }

        return $answeredQuestionIds;
    }

    public function getLevelProgress(
        Material $material,
        ?QuestionDifficulty $questionDifficulty,
        SupportCollection|Collection $answeredQuestionIds,
        bool $isGuest = false,
        ?Collection $preloadedQuestions = null,
    ): array {
        $questions = $preloadedQuestions ?? $this->questionRepository->getByMaterialAndDifficulty($material->id, $questionDifficulty instanceof QuestionDifficulty ? $questionDifficulty->value : 'all');
        if (! $preloadedQuestions instanceof Collection && $isGuest) {
            $questions = $questions->take($questionDifficulty instanceof QuestionDifficulty ? 3 : 9);
        }

        $answeredArray = $answeredQuestionIds->toArray();
        $completed     = $questions->filter(fn ($item): bool => in_array($item->id, $answeredArray))->values();
        $remaining     = $questions->reject(fn ($item): bool => in_array($item->id, $answeredArray))->values();

        $levels = [];
        $index  = 1;

        foreach ($completed as $question) {
            $levels[] = [
                'level'       => $index++,
                'question_id' => $question->id,
                'status'      => 'completed',
            ];
        }

        $isFirst = true;
        foreach ($remaining as $questionItem) {
            $levels[] = [
                'level'       => $index++,
                'question_id' => $questionItem->id,
                'status'      => $isFirst ? 'unlocked' : 'locked',
            ];
            $isFirst = false;
        }

        return $levels;
    }

    private function getFilteredQuestionsForQuiz(Material $material, ?QuestionDifficulty $questionDifficulty, bool $isGuest): array
    {
        $questions = $this->questionRepository->getByMaterialAndDifficulty($material->id, $questionDifficulty instanceof QuestionDifficulty ? $questionDifficulty->value : 'all');
        if ($isGuest) {
            if (! $questionDifficulty instanceof QuestionDifficulty) {
                $questions = $questions->where('difficulty', QuestionDifficulty::BEGINNER->value)->take(3)
                    ->concat($questions->where('difficulty', QuestionDifficulty::MEDIUM->value)->take(3))
                    ->concat($questions->where('difficulty', QuestionDifficulty::HARD->value)->take(3));
                $total = 9;
            } else {
                $questions = $questions->take(3);
                $total     = 3;
            }
        } else {
            $total = $questions->count();
        }

        return ['questions' => $questions, 'totalFilteredQuestions' => $total];
    }

    private function getCurrentQuestion(Collection $questions, SupportCollection $supportCollection): ?Question
    {
        $answeredArray = $supportCollection->toArray();
        $current       = $questions->reject(fn ($q): bool => in_array($q->id, $answeredArray))->first();
        if ($current instanceof Question) {
            if (! $current->relationLoaded('answers')) {
                $current->load('answers');
            }

            $current->setRelation('answers', $current->answers->shuffle());
        }

        return $current;
    }
}
