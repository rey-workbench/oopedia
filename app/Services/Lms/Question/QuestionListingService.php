<?php

namespace App\Services\Lms\Question;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Services\QuestionListingServiceInterface;

class QuestionListingService implements QuestionListingServiceInterface
{
    public function __construct(
        protected MaterialRepositoryInterface $materialRepo,
        protected ProgressRepositoryInterface $progressRepo,
        protected QuestionRepositoryInterface $questionRepo
    ) {}

    public function getQuizData($material, $difficulty, $userId, $isGuest, $guestProgress = [])
    {
        $answeredQuestionIds = $isGuest 
            ? $this->getGuestAnsweredQuestionIds($material->id, $guestProgress)
            : $this->progressRepo->getAnsweredQuestionIds($userId, $material->id);

        $filteredData = $this->getFilteredQuestions($material, $difficulty, $isGuest);
        $questions = $filteredData['questions'];
        $totalFilteredQuestions = $filteredData['totalFilteredQuestions'];

        $currentQuestion = $this->getCurrentQuestion($questions, $answeredQuestionIds);
        
        $levelProgress = $this->getLevelProgress($material, $difficulty, $answeredQuestionIds, $isGuest);

        return [
            'material' => $material,
            'questions' => $questions,
            'currentQuestion' => $currentQuestion,
            'totalQuestions' => $totalFilteredQuestions,
            'answeredCount' => $answeredQuestionIds->count(),
            'levelProgress' => $levelProgress,
            'difficulty' => $difficulty
        ];
    }

    public function getMaterialsListWithStudentCount($userId, $isGuest, $guestProgress = [])
    {
        $progressStats = $isGuest ? collect([]) : $this->progressRepo->getUserProgressStats($userId);
        $allMaterials = $this->materialRepo->getAllWithQuestions();

        if ($isGuest) {
            $totalMaterials = $allMaterials->count();
            $materialsToShow = ceil($totalMaterials / 2);
            $allMaterials = $allMaterials->take($materialsToShow);
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
                $correctAnswers = $materialProgress ? $materialProgress->correct_answers : 0;
            }

            $progressPercentage = $configuredTotalQuestions > 0
                ? min(100, round(($correctAnswers / $configuredTotalQuestions) * 100))
                : 0;

            $studentCount = isset($studentCounts[$material->id]) ? $studentCounts[$material->id]->student_count : 0;

            $material->progress_percentage = $progressPercentage;
            $material->total_questions = $configuredTotalQuestions;
            $material->completed_questions = $correctAnswers;
            $material->student_count = $studentCount;

            return $material;
        });

        return $materials;
    }

    public function getReviewQuestions($material, $difficulty, $userId, $isGuest, $guestProgress = [])
    {
        $questions = $material->questions;

        if ($difficulty && $difficulty !== 'all') {
            $questions = $questions->where('difficulty', $difficulty);
        }

        if ($isGuest) {
            $answeredQuestionIds = $this->getGuestAnsweredQuestionIds($material->id, $guestProgress);
            $questions = $questions->whereIn('id', $answeredQuestionIds->toArray());
        } else {
            $answeredQuestionIds = $this->progressRepo->getAnsweredQuestionIds($userId, $material->id);
            $questions = $questions->whereIn('id', $answeredQuestionIds);
        }

        return $questions;
    }

    public function getGuestAnsweredQuestionIds($materialId, $guestProgress = [])
    {
        $answeredQuestionIds = collect([]);

        foreach ($guestProgress as $key => $progress) {
            if (is_array($progress) && (isset($progress['is_correct']) || isset($progress['attempt_number']))) {
                 $parts = explode('_', $key);
                 if (count($parts) >= 2 && $parts[0] == $materialId) {
                      $questionId = (int)$parts[1];
                      if (!$answeredQuestionIds->contains($questionId)) {
                           $answeredQuestionIds->push($questionId);
                      }
                 }
            }
        }

        return $answeredQuestionIds;
    }

    public function getFilteredQuestions($material, $difficulty, $isGuest)
    {
        $questions = $this->questionRepo->getByMaterialAndDifficulty($material->id, $difficulty);

        if ($isGuest) {
            if ($difficulty === 'all') {
                $beginnerQuestions = $questions->where('difficulty', 'beginner')->take(3);
                $mediumQuestions = $questions->where('difficulty', 'medium')->take(3);
                $hardQuestions = $questions->where('difficulty', 'hard')->take(3);
                $questions = $beginnerQuestions->concat($mediumQuestions)->concat($hardQuestions);
                $totalFilteredQuestions = 9;
            } else {
                $questions = $questions->take(3);
                $totalFilteredQuestions = 3;
            }
        } else {
            $totalFilteredQuestions = $questions->count();
        }

        // Sequential progression: questions are presented in order, not shuffled
        // This ensures consistency with the levels system where progress must be linear

        return [
            'questions' => $questions,
            'totalFilteredQuestions' => $totalFilteredQuestions
        ];
    }

    public function getCurrentQuestion($questions, $answeredQuestionIds)
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

    public function getLevelProgress($material, $difficulty, $answeredQuestionIds, $isGuest = false)
    {
        $questions = $this->questionRepo->getByMaterialAndDifficulty($material->id, $difficulty);
        
        // Apply guest filtering - same logic as getFilteredQuestions
        if ($isGuest) {
            if ($difficulty === 'all') {
                $beginnerQuestions = $questions->where('difficulty', 'beginner')->take(3);
                $mediumQuestions = $questions->where('difficulty', 'medium')->take(3);
                $hardQuestions = $questions->where('difficulty', 'hard')->take(3);
                $questions = $beginnerQuestions->concat($mediumQuestions)->concat($hardQuestions);
            } else {
                $questions = $questions->take(3);
            }
        }
        
        $levels = [];

        foreach ($questions as $index => $question) {
            $questionIndex = $index + 1;
            $isAnswered = $answeredQuestionIds->contains($question->id);

            if ($isAnswered) {
                $status = 'completed';
            } elseif ($questionIndex === 1) {
                $status = 'unlocked';
            } else {
                $allPreviousAnswered = true;
                for ($i = 0; $i < $index; $i++) {
                    if (!$answeredQuestionIds->contains($questions[$i]->id)) {
                        $allPreviousAnswered = false;
                        break;
                    }
                }
                $status = $allPreviousAnswered ? 'unlocked' : 'locked';
            }

            $levels[] = [
                'level' => $questionIndex,
                'question_id' => $question->id,
                'status' => $status,
                'difficulty' => $question->difficulty
            ];
        }

        return $levels;
    }

    protected function calculateConfiguredQuestions($material, $isGuest)
    {
        if ($isGuest) {
            $beginnerCount = min(3, $material->questions->where('difficulty', 'beginner')->count());
            $mediumCount = min(3, $material->questions->where('difficulty', 'medium')->count());
            $hardCount = min(3, $material->questions->where('difficulty', 'hard')->count());
            return $beginnerCount + $mediumCount + $hardCount;
        } else {
            return $material->questions->count();
        }
    }
}
