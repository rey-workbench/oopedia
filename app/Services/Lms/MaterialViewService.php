<?php

namespace App\Services\Lms;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\SubMaterialRepositoryInterface;
use App\Contracts\Services\MaterialViewServiceInterface;
use App\Exceptions\Domain\MaterialNotFoundException;
use App\Exceptions\Domain\SubMaterialNotFoundException;
use App\Helpers\ProgressHelper;
use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;

class MaterialViewService implements MaterialViewServiceInterface
{
    public function __construct(
        protected MaterialRepositoryInterface $materialRepo,
        protected ProgressRepositoryInterface $progressRepo,
        protected SubMaterialRepositoryInterface $subMaterialRepo,
    ) {}

    /** @return Collection<int, Material> */
    public function getMaterialsList(?string $userId, bool $isGuest): Collection
    {
        $progressStats = $userId ? $this->progressRepo->getUserProgressStats($userId) : collect();

        // Use optimized listing method
        $allMaterials = $this->materialRepo->getMaterialsForListing();

        // Get unlocked modules for authenticated users
        $unlockedModules = [];
        if ($userId) {
            $studentState    = $this->progressRepo->getStudentState($userId);
            $unlockedModules = $studentState?->learning_profile['unlocked_modules'] ?? [];
        }

        // Determine first module ID for gating
        $firstModuleId  = $allMaterials->whereNotNull('module_id')->min('module_id');
        $totalMaterials = $allMaterials->count();

        if ($isGuest) {
            // For guests, load questions for difficulty calculation
            $allMaterials->load(['questions' => function ($query) {
                $query->select('id', 'material_id', 'difficulty');
            }]);
        }

        $materials = $allMaterials->map(function ($material, $index) use ($progressStats, $isGuest, $unlockedModules, $firstModuleId, $totalMaterials) {
            // If logged in, we use questions_count which was eager-loaded via withCount
            // If guest, we use the loaded questions collection
            $configuredTotalQuestions = ProgressHelper::calculateMaterialQuestionCounts($material, $isGuest)['total'];

            $materialProgress = $progressStats->firstWhere('material_id', $material->id);
            $correctAnswers   = $materialProgress ? $materialProgress->correct_answers : 0;

            $progressPercentage = ProgressHelper::calculateProgressPercentage($correctAnswers, $configuredTotalQuestions);

            $material->progress_percentage = $progressPercentage;
            $material->total_questions     = $configuredTotalQuestions;
            $material->completed_questions = $correctAnswers;

            // Calculate is_locked status
            if ($isGuest) {
                // Guests can access first half only
                $material->is_locked = $index >= ceil($totalMaterials / 2);
            } else {
                // For authenticated users, use module-based locking
                $moduleId            = $material->module_id;
                $isFirstModule       = $moduleId !== null && $moduleId == $firstModuleId;
                $isUnlocked          = empty($moduleId) || $isFirstModule || in_array($moduleId, $unlockedModules);
                $material->is_locked = ! $isUnlocked;
            }

            return $material;
        });

        return $materials;
    }

    /** @return array<string, mixed> */
    public function getMaterialDetail(string $materialId, ?string $userId, bool $isGuest): array
    {
        $material = $this->materialRepo->findWithQuestionsShuffled($materialId);

        // Get list of all materials for navigation
        $allMaterials = $this->materialRepo->getAllOrdered();

        // Limit materials list for guests
        if ($isGuest) {
            $totalMaterials  = $allMaterials->count();
            $materialsToShow = ceil($totalMaterials / 2);
            $materials       = $allMaterials->take($materialsToShow);
        } else {
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

        if (! $currentQuestion && $material->questions->count() > 0) {
            $currentQuestion = $material->questions->first();
        }

        $answeredCount         = count($answeredQuestionIds);
        $currentQuestionNumber = $answeredCount + 1;

        if ($answeredCount >= $material->questions->count()) {
            $currentQuestionNumber = 'Review';
        }

        return [
            'material'              => $material,
            'materials'             => $materials,
            'currentQuestionNumber' => $currentQuestionNumber,
        ];
    }

    /** @return array<string, mixed> */
    public function getSubMaterialDetail(string $materialId, string $subMaterialId, bool $isGuest): array
    {
        $material = $this->materialRepo->find($materialId);

        if (! $material) {
            throw new MaterialNotFoundException($materialId);
        }

        $subMaterial = $this->subMaterialRepo->findWithQuestions($subMaterialId);

        if (! $subMaterial) {
            throw new SubMaterialNotFoundException($subMaterialId);
        }

        return [
            'material'    => $material,
            'subMaterial' => $subMaterial,
            'isGuest'     => $isGuest,
        ];
    }

    public function resetMaterialProgress(string $userId, string $materialId): void
    {
        $this->progressRepo->resetProgress($userId, $materialId);
    }
}
