<?php

namespace App\Services\Lms;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\SubMaterialRepositoryInterface;
use App\Contracts\Services\MaterialViewServiceInterface;
use App\Exceptions\Domain\MaterialNotFoundException;
use App\Exceptions\Domain\SubMaterialNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use App\Helpers\ProgressHelper;

class MaterialViewService implements MaterialViewServiceInterface
{

    public function __construct(protected
        MaterialRepositoryInterface $materialRepo, protected
        ProgressRepositoryInterface $progressRepo, protected
        SubMaterialRepositoryInterface $subMaterialRepo,
        )
    {
    }

    /** @return Collection<int, \App\Models\Material> */
    public function getMaterialsList(string|null $userId, bool $isGuest): Collection
    {
        $progressStats = $userId ? $this->progressRepo->getUserProgressStats($userId) : collect();

        // Use optimized listing method
        $allMaterials = $this->materialRepo->getMaterialsForListing();

        if ($isGuest) {
            $totalMaterials = $allMaterials->count();
            $materialsToShow = ceil($totalMaterials / 2);
            $allMaterials = $allMaterials->take($materialsToShow);

            // For guests, we only need question difficulties to calculate limits
            $allMaterials->load(['questions' => function ($query) {
                $query->select('id', 'material_id', 'difficulty');
            }]);
        }

        $materials = $allMaterials->map(function ($material) use ($progressStats, $isGuest) {
            // If logged in, we use questions_count which was eager-loaded via withCount
            // If guest, we use the loaded questions collection
            $configuredTotalQuestions = ProgressHelper::calculateMaterialQuestionCounts($material, $isGuest)['total'];

            $materialProgress = $progressStats->firstWhere('material_id', $material->id);
            $correctAnswers = $materialProgress ? $materialProgress->correct_answers : 0;

            $progressPercentage = ProgressHelper::calculateProgressPercentage($correctAnswers, $configuredTotalQuestions);

            $material->progress_percentage = $progressPercentage;
            $material->total_questions = $configuredTotalQuestions;
            $material->completed_questions = $correctAnswers;

            return $material;
        });

        return $materials;
    }

    /** @return array<string, mixed> */
    public function getMaterialDetail(string $materialId, string|null $userId, bool $isGuest): array
    {
        $material = $this->materialRepo->findWithQuestionsShuffled($materialId);

        // Get list of all materials for navigation
        $allMaterials = $this->materialRepo->getAllOrdered();

        // Limit materials list for guests
        if ($isGuest) {
            $totalMaterials = $allMaterials->count();
            $materialsToShow = ceil($totalMaterials / 2);
            $materials = $allMaterials->take($materialsToShow);
        }
        else {
            $materials = $allMaterials;
        }

        // Limit questions for guest users
        if ($isGuest) {
            $limitedQuestions = $material->questions->take(3);
            $material->setRelation('questions', $limitedQuestions);
        }

        // Get answered questions and current question
        // For guest users, we use session-based progress, so repository stats might be empty
        // MaterialViewService currently doesn't have access to Request to get cookie progress
        // But making repo methods nullable prevents the crash
        $answeredQuestionIds = $userId
            ? $this->progressRepo->getAnsweredQuestionIds($userId, $materialId)
            : collect();

        $currentQuestion = $material->questions
            ->whereNotIn('id', $answeredQuestionIds)
            ->first();

        if (!$currentQuestion && $material->questions->count() > 0) {
            $currentQuestion = $material->questions->first();
        }

        $answeredCount = count($answeredQuestionIds);
        $currentQuestionNumber = $answeredCount + 1;

        if ($answeredCount >= $material->questions->count()) {
            $currentQuestionNumber = 'Review';
        }

        return [
            'material' => $material,
            'materials' => $materials,
            'currentQuestionNumber' => $currentQuestionNumber,
        ];
    }

    /** @return array<string, mixed> */
    public function getSubMaterialDetail(string $materialId, string $subMaterialId, bool $isGuest): array
    {
        $material = $this->materialRepo->find($materialId);

        if (!$material) {
            throw new MaterialNotFoundException($materialId);
        }

        $subMaterial = $this->subMaterialRepo->findWithQuestions($subMaterialId);

        if (!$subMaterial) {
            throw new SubMaterialNotFoundException($subMaterialId);
        }

        return [
            'material' => $material,
            'subMaterial' => $subMaterial,
            'isGuest' => $isGuest,
        ];
    }

    public function resetMaterialProgress(string $userId, string $materialId): void
    {
        $this->progressRepo->resetProgress($userId, $materialId);
    }
}
