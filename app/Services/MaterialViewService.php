<?php

namespace App\Services;

use App\Repositories\MaterialRepository;
use App\Repositories\ProgressRepository;
use App\Models\QuestionBankConfig;

class MaterialViewService
{
    protected $materialRepo;
    protected $progressRepo;

    public function __construct(
        MaterialRepository $materialRepo,
        ProgressRepository $progressRepo
    ) {
        $this->materialRepo = $materialRepo;
        $this->progressRepo = $progressRepo;
    }

    public function getMaterialsList($userId, $isGuest)
    {
        $progressStats = $this->progressRepo->getUserProgressStats($userId);
        $allMaterials = $this->materialRepo->getAllOrdered();

        // Limit materials for guests
        if ($isGuest) {
            $totalMaterials = $allMaterials->count();
            $materialsToShow = ceil($totalMaterials / 2);
            $allMaterials = $allMaterials->take($materialsToShow);
        }

        $materials = $allMaterials->map(function ($material) use ($progressStats, $isGuest) {
            $configuredTotalQuestions = $this->calculateConfiguredQuestions($material, $isGuest);
            
            // progressStats is a Collection of objects with material_id, correct_answers
            $materialProgress = $progressStats->firstWhere('material_id', $material->id);
            $correctAnswers = $materialProgress ? $materialProgress->correct_answers : 0;

            $progressPercentage = $configuredTotalQuestions > 0
                ? min(100, round(($correctAnswers / $configuredTotalQuestions) * 100))
                : 0;

            $material->progress_percentage = $progressPercentage;
            $material->total_questions = $configuredTotalQuestions;
            $material->completed_questions = $correctAnswers;

            // Ensure media is loaded
            if (!$material->relationLoaded('media')) {
                $material->load('media');
            }

            return $material;
        });

        return $materials;
    }

    public function getMaterialDetail($materialId, $userId, $isGuest)
    {
        $material = $this->materialRepo->findWithQuestionsShuffled($materialId);

        // Get list of all materials for navigation
        $allMaterials = $this->materialRepo->getAllOrdered();

        // Limit materials list for guests
        if ($isGuest) {
            $totalMaterials = $allMaterials->count();
            $materialsToShow = ceil($totalMaterials / 2);
            $materials = $allMaterials->take($materialsToShow);
        } else {
            $materials = $allMaterials;
        }

        // Limit questions for guest users
        if ($isGuest) {
            $limitedQuestions = $material->questions->take(3);
            $material->setRelation('questions', $limitedQuestions);
        }

        // Get answered questions and current question
        $answeredQuestionIds = $this->progressRepo->getAnsweredQuestionIds($userId, $materialId);

        $currentQuestion = $material->questions
            ->whereNotIn('id', $answeredQuestionIds)
            ->first();

        if (!$currentQuestion && $material->questions->count() > 0) {
            $currentQuestion = $material->questions->first();
        }

        $answeredCount = count($answeredQuestionIds);
        $currentQuestionNumber = $answeredCount + 1;

        if ($answeredCount >= $material->questions->count()) {
            $currentQuestionNumber = "Review";
        }

        return [
            'material' => $material,
            'materials' => $materials,
            'currentQuestionNumber' => $currentQuestionNumber
        ];
    }

    public function resetMaterialProgress($userId, $materialId)
    {
        return $this->progressRepo->resetProgress($userId, $materialId);
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
