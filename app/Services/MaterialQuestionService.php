<?php

namespace App\Services;

use App\Repositories\Interfaces\MaterialRepositoryInterface;
use App\Repositories\Interfaces\ProgressRepositoryInterface;
use App\Repositories\Interfaces\QuestionRepositoryInterface;
use App\Models\QuestionBankConfig;

class MaterialQuestionService extends BaseService
{
    protected $materialRepo;
    protected $progressRepo;
    protected $questionRepo;

    public function __construct(
        MaterialRepositoryInterface $materialRepo,
        ProgressRepositoryInterface $progressRepo,
        QuestionRepositoryInterface $questionRepo
    ) {
        $this->materialRepo = $materialRepo;
        $this->progressRepo = $progressRepo;
        $this->questionRepo = $questionRepo;
    }

    public function getMaterialsListWithStudentCount($userId, $isGuest, $guestProgress = [])
    {
        $progressStats = $isGuest ? collect([]) : $this->progressRepo->getUserProgressStats($userId);
        $allMaterials = $this->materialRepo->getAllWithQuestions();

        // Limit for guests
        if ($isGuest) {
            $totalMaterials = $allMaterials->count();
            $materialsToShow = ceil($totalMaterials / 2);
            $allMaterials = $allMaterials->take($materialsToShow);
        }

        $studentCounts = $this->progressRepo->getStudentCountByMaterial();
        // $guestProgress passed from controller now

        $materials = $allMaterials->map(function ($material) use ($progressStats, $isGuest, $studentCounts, $guestProgress) {
            $configuredTotalQuestions = $this->calculateConfiguredQuestions($material, $isGuest);
            
            if ($isGuest) {
                // Calculate correct answers from provided array
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
// ...
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

        // guestProgress is now a flat array passed from controller
        // It comes from cookie which is flat: "materialId_questionId" => data

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

    public function getFilteredQuestions($material, $difficulty, $isGuest, $config = null)
    {
        $questions = $this->questionRepo->getByMaterialAndDifficulty($material->id, $difficulty);

        // Apply config limits
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
            if (!$config) {
                $config = QuestionBankConfig::where('material_id', $material->id)
                    ->where('is_active', true)
                    ->first();
            }

            if ($config) {
                if ($difficulty === 'all') {
                    $beginnerQuestions = $questions->where('difficulty', 'beginner')->take($config->beginner_count);
                    $mediumQuestions = $questions->where('difficulty', 'medium')->take($config->medium_count);
                    $hardQuestions = $questions->where('difficulty', 'hard')->take($config->hard_count);
                    $questions = $beginnerQuestions->concat($mediumQuestions)->concat($hardQuestions);
                    $totalFilteredQuestions = $config->beginner_count + $config->medium_count + $config->hard_count;
                } else {
                    $countField = $difficulty . '_count';
                    $limit = $config->$countField;
                    $questions = $questions->take($limit);
                    $totalFilteredQuestions = $limit;
                }
            } else {
                $totalFilteredQuestions = $questions->count();
            }
        }

        // Shuffle questions by difficulty
        if ($difficulty === 'all') {
            $beginnerShuffled = $questions->where('difficulty', 'beginner')->shuffle();
            $mediumShuffled = $questions->where('difficulty', 'medium')->shuffle();
            $hardShuffled = $questions->where('difficulty', 'hard')->shuffle();
            $questions = $beginnerShuffled->concat($mediumShuffled)->concat($hardShuffled);
        } else {
            $questions = $questions->shuffle();
        }

        return [
            'questions' => $questions,
            'totalFilteredQuestions' => $totalFilteredQuestions
        ];
    }

    public function getCurrentQuestion($questions, $answeredQuestionIds, $questionId = null)
    {
        // STRICT MODE:
        // Always return the first unanswered question to enforce sequence.
        // We ignore $questionId parameter for security, preventing URL manipulation.
        // Exception: If reviewing (handled separately), but for 'show' flow, it must be strict.

        $currentQuestion = $questions->reject(function ($question) use ($answeredQuestionIds) {
            return $answeredQuestionIds->contains($question->id);
        })->first();

        // If all questions are answered, just return null (Controller handles completion)
        
        // Shuffle answers if needed
        if ($currentQuestion && $currentQuestion->question_type !== 'fill_in_the_blank') {
            if (!$currentQuestion->relationLoaded('answers')) {
                $currentQuestion->load('answers');
            }
            $currentQuestion->setRelation('answers', $currentQuestion->answers->shuffle());
        }

        return $currentQuestion;
    }

    public function getLevelProgress($material, $difficulty, $answeredQuestionIds)
    {
        $questions = $this->questionRepo->getByMaterialAndDifficulty($material->id, $difficulty);
        $levels = [];

        foreach ($questions as $index => $question) {
            $questionIndex = $index + 1;
            $isAnswered = $answeredQuestionIds->contains($question->id);

            if ($isAnswered) {
                $status = 'completed';
            } elseif ($questionIndex === 1) {
                $status = 'unlocked';
            } elseif ($index > 0 && $answeredQuestionIds->contains($questions[$index - 1]->id)) {
                $status = 'unlocked';
            } else {
                $status = 'locked';
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
            $config = QuestionBankConfig::where('material_id', $material->id)
                ->where('is_active', true)
                ->first();

            if ($config) {
                return $config->beginner_count + $config->medium_count + $config->hard_count;
            } else {
                return $material->questions->count();
            }
        }
    }
}
