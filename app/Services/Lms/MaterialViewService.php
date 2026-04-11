<?php

declare(strict_types=1);

namespace App\Services\Lms;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\SubMaterialRepositoryInterface;
use App\Contracts\Services\MaterialViewServiceInterface;
use App\Exceptions\Domain\MaterialNotFoundException;
use App\Exceptions\Domain\SubMaterialNotFoundException;
use App\Helpers\ProgressHelper;
use Illuminate\Database\Eloquent\Collection;

final class MaterialViewService implements MaterialViewServiceInterface
{
    public function __construct(
        protected MaterialRepositoryInterface $materialRepo,
        protected ProgressRepositoryInterface $progressRepo,
        protected SubMaterialRepositoryInterface $subMaterialRepo,
    ) {
    }

    public function getMaterialsList(?string $userId, bool $isGuest): Collection
    {
        $progressStats = $userId ? $this->progressRepo->getUserProgressStats($userId) : collect();

        $allMaterials = $this->materialRepo->getMaterialsForListing();

        $unlockedModules = [];
        if ($userId) {
            $studentState    = $this->progressRepo->getStudentState($userId);
            $unlockedModules = $studentState?->learning_profile['unlocked_modules'] ?? [];
        }

        $allMaterials->load(['questions' => function ($query) {
            $query->select('id', 'material_id', 'difficulty');
        }]);

        $firstModuleId  = $allMaterials->whereNotNull('module_id')->min('module_id');
        $totalMaterials = $allMaterials->count();

        $materials = $allMaterials->map(
            function (
                $material,
                $index,
            ) use (
                $progressStats,
                $isGuest,
                $unlockedModules,
                $firstModuleId,
                $totalMaterials,
            ) {
                $counts                   = ProgressHelper::calculateMaterialQuestionCounts($material, $isGuest);
                $configuredTotalQuestions = $counts['total'];
                $materialProgress         = $progressStats->firstWhere('material_id', $material->id);
                $correctAnswers           = $materialProgress ? $materialProgress->correct_answers : 0;

                $progressPercentage = ProgressHelper::calculateProgressPercentage(
                    $correctAnswers,
                    $configuredTotalQuestions,
                );

                $material->progress_percentage = $progressPercentage;
                $material->total_questions     = $configuredTotalQuestions;
                $material->completed_questions = $correctAnswers;

                if ($isGuest) {
                    $material->is_locked = $index >= ceil($totalMaterials / 2);
                } else {
                    $moduleId            = $material->module_id;
                    $isFirstModule       = $moduleId !== null && $moduleId == $firstModuleId;
                    $isUnlocked          = empty($moduleId) || $isFirstModule || in_array($moduleId, $unlockedModules);
                    $material->is_locked = ! $isUnlocked;
                }

                return $material;
            },
        );

        return $materials;
    }

    public function getMaterialDetail(string $materialId, ?string $userId, bool $isGuest, array $guestProgress = []): array
    {
        $material     = $this->materialRepo->findWithQuestionsShuffled($materialId);
        $allMaterials = $this->materialRepo->getAllOrdered();

        $firstModuleId   = $allMaterials->whereNotNull('module_id')->min('module_id');
        $totalMaterials  = $allMaterials->count();
        $unlockedModules = [];

        if ($userId) {
            $studentState    = $this->progressRepo->getStudentState($userId);
            $unlockedModules = $studentState?->learning_profile['unlocked_modules'] ?? [];
        }

        $allMaterials->map(function ($m, $index) use ($isGuest, $unlockedModules, $firstModuleId, $totalMaterials) {
            if ($isGuest) {
                $m->is_locked = $index >= ceil($totalMaterials / 2);
            } else {
                $moduleId      = $m->module_id;
                $isFirstModule = $moduleId !== null && $moduleId == $firstModuleId;
                $isUnlocked    = empty($moduleId) || $isFirstModule || in_array($moduleId, $unlockedModules);
                $m->is_locked  = ! $isUnlocked;
            }

            return $m;
        });

        if ($isGuest) {
            $materialsToShow = (int) ceil($totalMaterials / 2);
            $materials       = $allMaterials->take($materialsToShow);
        } else {
            $materials = $allMaterials;
        }

        if ($isGuest) {
            $limitedQuestions = $material->questions->take(3);
            $material->setRelation('questions', $limitedQuestions);
        }

        $answeredQuestionIds = $userId
            ? $this->progressRepo->getAnsweredQuestionIds($userId, $materialId)
            : collect($guestProgress);

        $answeredQuestionIds = collect($answeredQuestionIds);

        $currentQuestion = $material->questions
            ->whereNotIn('id', $answeredQuestionIds)
            ->first();

        if (! $currentQuestion && $material->questions->count() > 0) {
            $currentQuestion = $material->questions->first();
        }

        $answeredCount         = $answeredQuestionIds->count();
        $currentQuestionNumber = $answeredCount + 1;

        if ($answeredCount >= $material->questions->count()) {
            $currentQuestionNumber = 'Review';
        }

        return [
            'material'              => $material,
            'materials'             => $materials,
            'currentQuestionNumber' => $currentQuestionNumber,
            'currentQuestion'       => $currentQuestion,
        ];
    }

    public function getSubMaterialsList(string $materialId, ?string $userId, bool $isGuest): array
    {
        $material = $this->materialRepo->findWithRelations($materialId, ['subMaterials.media', 'media']);

        if (! $material) {
            throw new MaterialNotFoundException($materialId);
        }

        return [
            'material'     => $material,
            'subMaterials' => $material->subMaterials,
        ];
    }

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
