<?php

declare(strict_types=1);

namespace App\Services\Lms;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\MediaRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\MaterialServiceInterface;
use App\Exceptions\Domain\MaterialNotFoundException;
use App\Exceptions\Domain\MediaOperationException;
use App\Helpers\ProgressHelper;
use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

final class MaterialService implements MaterialServiceInterface
{
    public function __construct(
        private readonly MaterialRepositoryInterface $materialRepo,
        private readonly MediaRepositoryInterface $mediaRepo,
        private readonly ProgressRepositoryInterface $progressRepo,
    ) {}

    // =========================================================================
    // MATERIAL MANAGEMENT (CRUD)
    // =========================================================================

    public function getAllMaterials(
        ?string $search = null,
        string $sort = 'created_at',
        string $direction = 'asc',
    ): Collection {
        return $this->materialRepo->getMaterialsForAdmin($search, $sort, $direction);
    }

    public function getAllOrdered(): Collection
    {
        return $this->materialRepo->getAllOrdered();
    }

    public function getMaterialById(string $id): ?Material
    {
        return $this->materialRepo->find($id);
    }

    public function getMaterialWithQuestionsAndAnswers(string $id): ?Material
    {
        return $this->materialRepo->findWithQuestionsAndAnswers($id);
    }

    public function createMaterial(array $data, mixed $coverImage = null): Material
    {
        $material = $this->materialRepo->create([
            'title'            => $data['title'],
            'content'          => $data['content'],
            'module_id'        => $data['module_id'],
            'created_by'       => $data['created_by'],
            'is_final_project' => $data['is_final_project'] ?? false,
        ]);

        if ($coverImage) {
            $this->uploadCoverImage($material, $coverImage);
        }

        Cache::forget('sidebar_materials_v4');

        return $material;
    }

    public function updateMaterial(string $materialId, array $data, mixed $coverImage = null): Material
    {
        $material = $this->materialRepo->find($materialId);

        if (! $material) {
            throw new MaterialNotFoundException($materialId);
        }

        $this->materialRepo->update($material->id, [
            'title'            => $data['title'],
            'content'          => $data['content']          ?? $data['description'] ?? null,
            'module_id'        => $data['module_id']        ?? $material->module_id,
            'is_final_project' => $data['is_final_project'] ?? $material->is_final_project,
        ]);

        if ($coverImage) {
            $this->deleteCoverImage($material);
            $this->uploadCoverImage($material, $coverImage);
        }

        Cache::forget('sidebar_materials_v4');

        return $material->fresh();
    }

    public function deleteMaterial(string $materialId): void
    {
        $material = $this->materialRepo->find($materialId);

        if (! $material) {
            throw new MaterialNotFoundException($materialId);
        }

        $mediaFiles = $this->mediaRepo->getByMaterial($material->id);

        foreach ($mediaFiles as $media) {
            $this->removeMediaFile($media->media_url);
            $this->mediaRepo->delete($media->id);
        }

        $this->materialRepo->delete($material->id);

        Cache::forget('sidebar_materials_v4');
    }

    public function deleteMedia(string $mediaId): string
    {
        $media = $this->mediaRepo->find($mediaId);

        if (! $media) {
            throw new MediaOperationException("Media dengan ID '{$mediaId}' tidak ditemukan.");
        }

        $materialId = $media->material_id;

        $this->removeMediaFile($media->media_url);
        $this->mediaRepo->delete($mediaId);

        return (string) $materialId;
    }

    // =========================================================================
    // VIEWING & PROGRESS
    // =========================================================================

    public function getSidebarMaterials(?string $userId, bool $isGuest): Collection
    {
        $materials = Material::orderBy('created_at', 'asc')
            ->select('id', 'title', 'module_id')
            ->get();

        if ($isGuest) {
            $totalMaterials = $materials->count();

            return $materials->map(function ($material, $index) use ($totalMaterials) {
                $material->is_locked = $index >= ceil($totalMaterials / 2);

                return $material;
            });
        }

        return $materials->map(function ($material) {
            $material->is_locked = false;

            return $material;
        });
    }

    public function getMaterialsList(?string $userId, bool $isGuest): Collection
    {
        $progressStats = $userId ? $this->progressRepo->getUserProgressStats($userId) : collect();
        $allMaterials  = $this->materialRepo->getMaterialsForListing();

        $unlockedModules = [];
        if ($userId) {
            $studentState    = $this->progressRepo->getStudentState($userId);
            $unlockedModules = $studentState?->adaptive_state['unlocked_modules'] ?? [];
        }

        $allMaterials->load(['questions' => function ($query) {
            $query->select('id', 'material_id', 'difficulty');
        }]);

        $firstModuleId  = $allMaterials->whereNotNull('module_id')->min('module_id');
        $totalMaterials = $allMaterials->count();

        return $allMaterials->map(
            function ($material, $index) use ($progressStats, $isGuest, $totalMaterials) {
                $counts                   = ProgressHelper::calculateMaterialQuestionCounts($material, $isGuest);
                $configuredTotalQuestions = $counts['total'];
                $materialProgress         = $progressStats->firstWhere('material_id', $material->id);
                $correctAnswers           = $materialProgress ? $materialProgress->correct_answers : 0;

                $material->progress_percentage = ProgressHelper::calculateProgressPercentage($correctAnswers, $configuredTotalQuestions);
                $material->total_questions     = $configuredTotalQuestions;
                $material->completed_questions = $correctAnswers;

                if ($isGuest) {
                    $material->is_locked = $index >= ceil($totalMaterials / 2);
                } else {
                    $material->is_locked = false;
                }

                return $material;
            },
        );
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
            $unlockedModules = $studentState?->adaptive_state['unlocked_modules'] ?? [];
        }

        $allMaterials->map(function ($m, $index) use ($isGuest, $totalMaterials) {
            if ($isGuest) {
                $m->is_locked = $index >= ceil($totalMaterials / 2);
            } else {
                $m->is_locked = false;
            }

            return $m;
        });

        if ($isGuest) {
            $materialsToShow = (int) ceil($totalMaterials / 2);
            $materials       = $allMaterials->take($materialsToShow);

            $limitedQuestions = $material->questions->take(3);
            $material->setRelation('questions', $limitedQuestions);
        } else {
            $materials = $allMaterials;
        }

        $answeredQuestionIds = collect($userId
            ? $this->progressRepo->getAnsweredQuestionIds($userId, $materialId)
            : $guestProgress);

        $currentQuestion = $material->questions->whereNotIn('id', $answeredQuestionIds)->first()
            ?? $material->questions->first();

        $answeredCount         = $answeredQuestionIds->count();
        $currentQuestionNumber = ($answeredCount >= $material->questions->count()) ? 'Review' : ($answeredCount + 1);

        return [
            'material'              => $material,
            'materials'             => $materials,
            'currentQuestionNumber' => $currentQuestionNumber,
            'currentQuestion'       => $currentQuestion,
        ];
    }

    public function resetMaterialProgress(string $userId, string $materialId): void
    {
        $this->progressRepo->resetProgress($userId, $materialId);
    }

    // =========================================================================
    // HELPERS (MEDIA)
    // =========================================================================

    protected function uploadCoverImage(Material $material, mixed $file): void
    {
        $path = $file->store('materials', 'images');
        $this->mediaRepo->create([
            'material_id' => $material->id,
            'media_type'  => 'image',
            'media_url'   => '/images/' . $path,
        ]);
    }

    protected function deleteCoverImage(Material $material): void
    {
        $existingMedia = $this->mediaRepo->findByMaterialAndType($material->id, 'image');
        if (! $existingMedia) {
            return;
        }

        $this->removeMediaFile($existingMedia->media_url);
        $this->mediaRepo->delete($existingMedia->id);
    }

    protected function removeMediaFile(string $path): void
    {
        $disk = 'public';
        if (str_starts_with($path, '/images/')) {
            $disk = 'images';
            $path = str_replace('/images/', '', $path);
        } elseif (str_starts_with($path, '/storage/')) {
            $path = str_replace('/storage/', '', $path);
        } else {
            $path = str_replace('storage/', '', $path);
        }

        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }
}
