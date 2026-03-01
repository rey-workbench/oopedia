<?php

namespace App\Services\Lms\Question;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Services\QuestionListingServiceInterface;
use App\Models\Material;
use App\Traits\CalculatesConfiguredQuestions;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class QuestionListingService implements QuestionListingServiceInterface
{
    use CalculatesConfiguredQuestions;

    public function __construct(
        protected MaterialRepositoryInterface $materialRepo,
        protected ProgressRepositoryInterface $progressRepo,
        protected QuestionRepositoryInterface $questionRepo,
    ) {}

    /** @return array<string, mixed> */
    public function getQuizData(Material $material, string $difficulty, int|string|null $userId, bool $isGuest, array $guestProgress = []): array
    {
        $answeredQuestionIds = $isGuest
            ? $this->getGuestAnsweredQuestionIds($material->id, $guestProgress)
            : $this->progressRepo->getAnsweredQuestionIds($userId, $material->id);

        $filteredData           = $this->getFilteredQuestions($material, $difficulty, $isGuest);
        $questions              = $filteredData['questions'];
        $totalFilteredQuestions = $filteredData['totalFilteredQuestions'];

        $currentQuestion = $this->getCurrentQuestion($questions, $answeredQuestionIds);

        $levelProgress = $this->getLevelProgress($material, $difficulty, $answeredQuestionIds, $isGuest);

        // Calculate answeredCount relative ONLY to the currently filtered question set
        $answeredCount = $questions->filter(function ($q) use ($answeredQuestionIds) {
            return $answeredQuestionIds->contains($q->id);
        })->count();

        return [
            'material'              => $material,
            'questions'             => $questions,
            'currentQuestion'       => $currentQuestion,
            'currentQuestionNumber' => $answeredCount + 1,
            'totalQuestions'        => $totalFilteredQuestions,
            'answeredCount'         => $answeredCount,
            'levelProgress'         => $levelProgress,
            'difficulty'            => $difficulty,
        ];
    }

    /** @return Collection<int, Material> */
    public function getMaterialsListWithStudentCount(int|string|null $userId, bool $isGuest, array $guestProgress = []): Collection
    {
        $progressStats = $isGuest ? collect([]) : $this->progressRepo->getUserProgressStats($userId);
        $allMaterials  = $this->materialRepo->getAllWithQuestions();

        if ($isGuest) {
            $totalMaterials  = $allMaterials->count();
            $materialsToShow = ceil($totalMaterials / 2);
            $allMaterials    = $allMaterials->take($materialsToShow);
        }

        $studentCounts = $this->progressRepo->getStudentCountByMaterial();

        $materials = $allMaterials->map(function ($material) use ($progressStats, $isGuest, $studentCounts, $guestProgress) {
            $configuredTotalQuestions = $this->calculateConfiguredQuestions($material, $isGuest);

            if ($isGuest) {
                $correctAnswers = 0;
                foreach ($guestProgress as $key => $progress) {
                    if (strpos($key, $material->id . '_') === 0 && isset($progress['is_correct']) && $progress['is_correct']) {
                        $correctAnswers++;
                    }
                }
            } else {
                $materialProgress = $progressStats->firstWhere('material_id', $material->id);
                $correctAnswers   = $materialProgress ? $materialProgress->correct_answers : 0;
            }

            $progressPercentage = $configuredTotalQuestions > 0
                ? min(100, round(($correctAnswers / $configuredTotalQuestions) * 100))
                : 0;

            $studentCount = isset($studentCounts[$material->id]) ? $studentCounts[$material->id]->student_count : 0;

            $material->progress_percentage = $progressPercentage;
            $material->total_questions     = $configuredTotalQuestions;
            $material->completed_questions = $correctAnswers;
            $material->student_count       = $studentCount;

            return $material;
        });

        return $materials;
    }

    /** @return Collection<int, \App\Models\Question> */
    public function getReviewQuestions(Material $material, ?string $difficulty, int|string|null $userId, bool $isGuest, array $guestProgress = []): Collection
    {
        $questions = $material->questions;

        if ($difficulty && $difficulty !== 'all') {
            // Normalize difficulty: 'advanced' from UI mapping to 'hard' in DB
            $dbDifficulty = ($difficulty === 'advanced') ? 'hard' : $difficulty;
            $questions    = $questions->where('difficulty', $dbDifficulty);
        }

        if ($isGuest) {
            $answeredQuestionIds = $this->getGuestAnsweredQuestionIds($material->id, $guestProgress);
            $questions           = $questions->whereIn('id', $answeredQuestionIds->toArray());

            // Map guest attempts
            foreach ($questions as $question) {
                $key = $material->id . '_' . $question->id;
                if (isset($guestProgress[$key])) {
                    $question->user_attempt = $guestProgress[$key];
                }
            }
        } else {
            $questionIds = $questions->pluck('id');
            $attempts    = \App\Models\QuizAttempt::where('user_id', $userId)
                ->whereIn('question_id', $questionIds)
                ->get()
                ->groupBy('question_id');

            foreach ($questions as $question) {
                $latestAttempt = $attempts->get($question->id)?->sortByDesc('created_at')->first();

                if ($latestAttempt) {
                    $question->user_attempt = [
                        'score'          => $latestAttempt->score,
                        'is_correct'     => $latestAttempt->is_correct,
                        'answer_id'      => $latestAttempt->answer_id,
                        'user_response'  => $latestAttempt->user_response,
                        'attempt_number' => $latestAttempt->attempt_number,
                        'time_spent'     => $latestAttempt->time_spent,
                    ];
                }
            }
        }

        return $questions;
    }

    public function getGuestAnsweredQuestionIds(int $materialId, array $guestProgress = []): SupportCollection
    {
        $answeredQuestionIds = collect([]);

        foreach ($guestProgress as $key => $progress) {
            if (is_array($progress) && (isset($progress['is_correct']) || isset($progress['attempt_number']))) {
                $parts = explode('_', $key);
                if (count($parts) >= 2 && $parts[0] == $materialId) {
                    $questionId = (int) $parts[1];
                    if (! $answeredQuestionIds->contains($questionId)) {
                        $answeredQuestionIds->push($questionId);
                    }
                }
            }
        }

        return $answeredQuestionIds;
    }

    /** @return array<string, mixed> */
    public function getFilteredQuestions(Material $material, string $difficulty, bool $isGuest): array
    {
        $questions = $this->questionRepo->getByMaterialAndDifficulty($material->id, $difficulty);

        if ($isGuest) {
            if ($difficulty === 'all') {
                $beginnerQuestions      = $questions->where('difficulty', 'beginner')->take(3);
                $mediumQuestions        = $questions->where('difficulty', 'medium')->take(3);
                $hardQuestions          = $questions->where('difficulty', 'hard')->take(3);
                $questions              = $beginnerQuestions->concat($mediumQuestions)->concat($hardQuestions);
                $totalFilteredQuestions = 9;
            } else {
                $questions              = $questions->take(3);
                $totalFilteredQuestions = 3;
            }
        } else {
            $totalFilteredQuestions = $questions->count();
        }

        // Shuffle questions for randomness as requested by user
        $questions = $questions->shuffle();

        return [
            'questions'              => $questions,
            'totalFilteredQuestions' => $totalFilteredQuestions,
        ];
    }

    public function getCurrentQuestion(Collection $questions, SupportCollection $answeredQuestionIds): ?\App\Models\Question
    {
        $currentQuestion = $questions->reject(function ($question) use ($answeredQuestionIds) {
            return $answeredQuestionIds->contains($question->id);
        })->first();

        if ($currentQuestion && $currentQuestion->question_type !== 'fill_in_the_blank') {
            if (! $currentQuestion->relationLoaded('answers')) {
                $currentQuestion->load('answers');
            }
            // Ensure answers are always shuffled when loaded
            $currentQuestion->setRelation('answers', $currentQuestion->answers->shuffle());
        }

        return $currentQuestion;
    }

    /** @return array<int, array<string, mixed>> */
    public function getLevelProgress(Material $material, string $difficulty, SupportCollection $answeredQuestionIds, bool $isGuest = false): array
    {
        $questions = $this->questionRepo->getByMaterialAndDifficulty($material->id, $difficulty);

        // Apply guest filtering - same logic as getFilteredQuestions
        if ($isGuest) {
            if ($difficulty === 'all') {
                $beginnerQuestions = $questions->where('difficulty', 'beginner')->take(3);
                $mediumQuestions   = $questions->where('difficulty', 'medium')->take(3);
                $hardQuestions     = $questions->where('difficulty', 'hard')->take(3);
                $questions         = $beginnerQuestions->concat($mediumQuestions)->concat($hardQuestions);
            } else {
                $questions = $questions->take(3);
            }
        }

        $levels = [];

        foreach ($questions as $index => $question) {
            $questionIndex = $index + 1;
            $isAnswered    = $answeredQuestionIds->contains($question->id);

            if ($isAnswered) {
                $status = 'completed';
            } elseif ($questionIndex === 1) {
                $status = 'unlocked';
            } else {
                $allPreviousAnswered = true;
                for ($i = 0; $i < $index; $i++) {
                    if (! $answeredQuestionIds->contains($questions[$i]->id)) {
                        $allPreviousAnswered = false;
                        break;
                    }
                }
                $status = $allPreviousAnswered ? 'unlocked' : 'locked';
            }

            $levels[] = [
                'level'       => $questionIndex,
                'question_id' => $question->id,
                'status'      => $status,
                'difficulty'  => $question->difficulty,
            ];
        }

        return $levels;
    }
}
