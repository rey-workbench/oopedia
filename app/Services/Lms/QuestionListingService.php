<?php

namespace App\Services\Lms;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Services\QuestionListingServiceInterface;
use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use App\Helpers\ProgressHelper;

class QuestionListingService implements QuestionListingServiceInterface
{

    public function __construct(protected
        MaterialRepositoryInterface $materialRepo, protected
        ProgressRepositoryInterface $progressRepo, protected
        QuestionRepositoryInterface $questionRepo,
        )
    {
    }

    /** @return array<string, mixed> */
    public function getQuizData(Material $material, string $difficulty, string $userId, bool $isGuest, array $guestProgress = [], ?string $subMaterialId = null, ?string $targetDifficulty = null): array
    {
        $answeredQuestionIds = $isGuest
            ? $this->getGuestAnsweredQuestionIds($material->id, $guestProgress)
            : $this->progressRepo->getAnsweredQuestionIds($userId, $material->id);

        $filteredData = $this->getFilteredQuestions($material, $difficulty, $isGuest, $subMaterialId);
        $questions = $filteredData['questions'];
        $totalFilteredQuestions = $filteredData['totalFilteredQuestions'];

        // Calculate skipped questions
        $originalQuestionsCount = $questions->count();
        $skippedCount = 0;

        // Enforce rule-driven target difficulty by skipping easier, unanswered questions
        if ($difficulty === 'all' && $targetDifficulty) {
            $difficultyOrder = ['beginner' => 1, 'medium' => 2, 'hard' => 3];
            $targetLevel = $difficultyOrder[$targetDifficulty] ?? 1;

            $questions = $questions->reject(function ($q) use ($answeredQuestionIds, $difficultyOrder, $targetLevel) {
                $qLevel = $difficultyOrder[$q->difficulty] ?? 1;
                return !$answeredQuestionIds->contains($q->id) && $qLevel < $targetLevel;
            });

            $skippedCount = $originalQuestionsCount - $questions->count();
        }

        $levelProgress = $this->getLevelProgress($material, $difficulty, $answeredQuestionIds, $isGuest, $questions);

        // Group and structure questions by difficulty instead of absolute shuffle
        // This ensures Beginner -> Medium -> Hard progression in 'all' mode
        $questions = new Collection($questions->groupBy('difficulty')->map(function ($group) {
            return $group->shuffle();
        })->flatten()->all());

        $currentQuestion = $this->getCurrentQuestion($questions, $answeredQuestionIds);

        // Calculate actual answered count from the remaining pool
        $actualAnsweredCount = $questions->filter(fn($q) => $answeredQuestionIds->contains($q->id))->count();

        // Logical "completed" count (answered + skipped)
        $effectiveAnsweredCount = $actualAnsweredCount + $skippedCount;

        return [
            'material' => $material,
            'questions' => $questions,
            'currentQuestion' => $currentQuestion,
            'currentQuestionNumber' => $effectiveAnsweredCount + 1,
            'totalQuestions' => $totalFilteredQuestions,
            'answeredCount' => $effectiveAnsweredCount,
            'materialAnsweredCount' => $answeredQuestionIds->count(),
            'levelProgress' => $levelProgress,
            'difficulty' => $difficulty,
        ];
    }

    /** @return Collection<int, Material> */
    public function getMaterialsListWithStudentCount(string $userId, bool $isGuest, array $guestProgress = [], array $unlockedModules = []): Collection
    {
        $progressStats = $isGuest ? collect([]) : $this->progressRepo->getUserProgressStats($userId);
        $allMaterials = $this->materialRepo->getAllWithQuestions();

        if ($isGuest) {
            $totalMaterials = $allMaterials->count();
            $materialsToShow = ceil($totalMaterials / 2);
            $allMaterials = $allMaterials->take($materialsToShow);
        }

        $studentCounts = $this->progressRepo->getStudentCountByMaterial();

        // Determine the first module_id to always be unlocked
        $firstModuleId = $allMaterials->whereNotNull('module_id')->min('module_id');

        $materials = $allMaterials->map(function ($material) use ($progressStats, $isGuest, $studentCounts, $guestProgress, $unlockedModules, $firstModuleId) {
            $configuredTotalQuestions = ProgressHelper::calculateMaterialQuestionCounts($material, $isGuest)['total'];

            if ($isGuest) {
                $answeredCount = 0;
                foreach ($guestProgress as $key => $progress) {
                    if (strpos($key, $material->id . '_') === 0 && (isset($progress['is_correct']) || isset($progress['attempt_number']))) {
                        $answeredCount++;
                    }
                }
            }
            else {
                $materialProgress = $progressStats->firstWhere('material_id', $material->id);
                $answeredCount = $materialProgress ? $materialProgress->answered_questions : 0;
            }

            $progressPercentage = ProgressHelper::calculateProgressPercentage($answeredCount, $configuredTotalQuestions);

            $studentCount = isset($studentCounts[$material->id]) ? $studentCounts[$material->id]->student_count : 0;

            $material->progress_percentage = $progressPercentage;
            $material->total_questions = $configuredTotalQuestions;
            $material->completed_questions = $answeredCount;
            $material->student_count = $studentCount;

            // Gating: locked if not guest AND module_id is set AND not in unlocked list AND not the first module
            $moduleId = $material->module_id;
            $isFirstModule = ($moduleId !== null && $moduleId === $firstModuleId);
            $isUnlocked = $isGuest || $isFirstModule || empty($moduleId) || in_array($moduleId, $unlockedModules);
            $material->is_locked = !$isUnlocked;

            return $material;
        });

        return $materials;
    }

    /** @return Collection<int, \App\Models\Question> */
    public function getReviewQuestions(Material $material, ?string $difficulty, string $userId, bool $isGuest, array $guestProgress = []): Collection
    {
        $questions = $material->questions;

        if ($difficulty && $difficulty !== 'all') {
            $dbDifficulty = ($difficulty === 'advanced') ? 'hard' : $difficulty;
            $questions = $questions->where('difficulty', $dbDifficulty);
        }

        if ($isGuest) {
            $answeredQuestionIds = $this->getGuestAnsweredQuestionIds($material->id, $guestProgress);
            $questions = $questions->whereIn('id', $answeredQuestionIds->toArray());

            foreach ($questions as $question) {
                $key = $material->id . '_' . $question->id;
                if (isset($guestProgress[$key])) {
                    $question->user_attempt = $guestProgress[$key];
                }
            }
        }
        else {
            $answeredQuestionIds = $this->progressRepo->getAnsweredQuestionIds($userId, $material->id);
            $questions = $questions->whereIn('id', $answeredQuestionIds->toArray());

            $latestAttempts = $this->progressRepo->getLatestAttemptsForQuestions(
                $userId,
                $answeredQuestionIds->toArray(),
            );

            foreach ($questions as $question) {
                $attempt = $latestAttempts->get($question->id);
                if ($attempt) {
                    $question->user_attempt = [
                        'score' => $attempt->score,
                        'is_correct' => $attempt->is_correct,
                        'answer_id' => $attempt->answer_id,
                        'user_response' => $attempt->user_response,
                        'attempt_number' => $attempt->attempt_number,
                        'time_spent' => $attempt->time_spent,
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
            if (is_array($progress) && (isset($progress['is_correct']) || isset($progress['attempt_number']))) {
                $parts = explode('_', $key);
                if (count($parts) >= 2 && $parts[0] == $materialId) {
                    $questionId = $parts[1];
                    if (!$answeredQuestionIds->contains($questionId)) {
                        $answeredQuestionIds->push($questionId);
                    }
                }
            }
        }

        return $answeredQuestionIds;
    }

    public function getFilteredQuestions(Material $material, string $difficulty, bool $isGuest, ?string $subMaterialId = null): array
    {
        $questions = $this->questionRepo->getByMaterialAndDifficulty($material->id, $difficulty, $subMaterialId);

        if ($isGuest) {
            if ($difficulty === 'all') {
                $beginnerQuestions = $questions->where('difficulty', 'beginner')->take(3);
                $mediumQuestions = $questions->where('difficulty', 'medium')->take(3);
                $hardQuestions = $questions->where('difficulty', 'hard')->take(3);
                $questions = $beginnerQuestions->concat($mediumQuestions)->concat($hardQuestions);
                $totalFilteredQuestions = 9;
            }
            else {
                $questions = $questions->take(3);
                $totalFilteredQuestions = 3;
            }
        }
        else {
            $totalFilteredQuestions = $questions->count();
        }

        return [
            'questions' => $questions,
            'totalFilteredQuestions' => $totalFilteredQuestions,
        ];
    }

    public function getCurrentQuestion(Collection $questions, SupportCollection $answeredQuestionIds): ?\App\Models\Question
    {
        $currentQuestion = $questions->reject(function ($question) use ($answeredQuestionIds) {
            return $answeredQuestionIds->contains($question->id);
        })->first();

        if ($currentQuestion && $currentQuestion->question_type !== 'fill_in_the_blank') {
            if (!$currentQuestion->relationLoaded('answers')) {
                $currentQuestion->load('answers');
            }
            $currentQuestion->setRelation('answers', $currentQuestion->answers->shuffle());
        }

        return $currentQuestion;
    }

    /** @return array<int, array<string, mixed>> */
    public function getLevelProgress(Material $material, string $difficulty, SupportCollection $answeredQuestionIds, bool $isGuest = false, ?Collection $preloadedQuestions = null): array
    {
        if ($preloadedQuestions !== null) {
            $questions = $preloadedQuestions;
        }
        else {
            $questions = $this->questionRepo->getByMaterialAndDifficulty($material->id, $difficulty);
            if ($isGuest) {
                $questions = $questions->take($difficulty === 'all' ? 9 : 3);
            }
        }

        $completed = $questions->filter(fn($q) => $answeredQuestionIds->contains($q->id));
        $remaining = $questions->reject(fn($q) => $answeredQuestionIds->contains($q->id));

        $levels = [];
        $index = 1;

        foreach ($completed as $question) {
            $levels[] = [
                'level' => $index++,
                'question_id' => $question->id,
                'status' => 'completed',
            ];
        }

        $isFirst = true;
        foreach ($remaining as $question) {
            $levels[] = [
                'level' => $index++,
                'question_id' => $question->id,
                'status' => $isFirst ? 'unlocked' : 'locked',
            ];
            $isFirst = false;
        }

        return $levels;
    }
}
