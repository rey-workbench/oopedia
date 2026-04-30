<?php

declare(strict_types=1);

namespace App\Services\Lms;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\MediaRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\MaterialServiceInterface;
use App\DTOs\Material\MaterialCreateDTO;
use App\DTOs\Material\MaterialUpdateDTO;
use App\Exceptions\Domain\MaterialNotFoundException;
use App\Exceptions\Domain\MediaOperationException;
use App\Helpers\ProgressHelper;
use App\Models\Material;
use App\Models\Media;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

final readonly class MaterialService implements MaterialServiceInterface
{
    public function __construct(
        private MaterialRepositoryInterface $materialRepository,
        private MediaRepositoryInterface $mediaRepository,
        private ProgressRepositoryInterface $progressRepository,
    ) {}

    // =========================================================================
    // MATERIAL MANAGEMENT (CRUD)
    // =========================================================================

    public function getAllMaterials(
        ?string $search = null,
        string $sort = 'created_at',
        string $direction = 'asc',
    ): Collection {
        return $this->materialRepository->getMaterialsForAdmin($search, $sort, $direction);
    }

    public function getAllOrdered(): Collection
    {
        return $this->materialRepository->getAllOrdered();
    }

    public function getMaterialById(string $id): ?Material
    {
        return $this->materialRepository->find($id);
    }

    public function getMaterialWithQuestionsAndAnswers(string $id): Material
    {
        return $this->materialRepository->findWithQuestionsAndAnswers($id);
    }

    public function createMaterial(MaterialCreateDTO $materialCreateDTO): Material
    {
        $material = $this->materialRepository->create([
            'title'            => $materialCreateDTO->title,
            'content'          => $materialCreateDTO->content,
            'module_id'        => $materialCreateDTO->module_id,
            'created_by'       => $materialCreateDTO->created_by,
            'is_final_project' => $materialCreateDTO->is_final_project,
        ]);

        if ($materialCreateDTO->cover_image) {
            $this->uploadCoverImage($material, $materialCreateDTO->cover_image);
        }

        Cache::forget('sidebar_materials_v4');

        return $material;
    }

    public function updateMaterial(string $materialId, MaterialUpdateDTO $materialUpdateDTO): Material
    {
        $material = $this->materialRepository->find($materialId);

        if (! $material instanceof Material) {
            throw new MaterialNotFoundException($materialId);
        }

        $this->materialRepository->update($material->id, [
            'title'            => $materialUpdateDTO->title            ?? $material->title,
            'content'          => $materialUpdateDTO->content          ?? $material->content,
            'module_id'        => $materialUpdateDTO->module_id        ?? $material->module_id,
            'is_final_project' => $materialUpdateDTO->is_final_project ?? $material->is_final_project,
        ]);

        if ($materialUpdateDTO->cover_image) {
            $this->deleteCoverImage($material);
            $this->uploadCoverImage($material, $materialUpdateDTO->cover_image);
        }

        Cache::forget('sidebar_materials_v4');

        return $material->fresh();
    }

    public function deleteMaterial(string $materialId): void
    {
        $material = $this->materialRepository->find($materialId);

        if (! $material instanceof Material) {
            throw new MaterialNotFoundException($materialId);
        }

        $mediaFiles = $this->mediaRepository->getByMaterial($material->id);

        foreach ($mediaFiles as $mediumFile) {
            $this->removeMediaFile($mediumFile->media_url);
            $this->mediaRepository->delete($mediumFile->id);
        }

        $this->materialRepository->delete($material->id);

        Cache::forget('sidebar_materials_v4');
    }

    public function deleteMedia(string $mediaId): string
    {
        $media = $this->mediaRepository->find($mediaId);

        if (! $media instanceof Media) {
            throw new MediaOperationException(sprintf("Media dengan ID '%s' tidak ditemukan.", $mediaId));
        }

        $materialId = $media->material_id;

        $this->removeMediaFile($media->media_url);
        $this->mediaRepository->delete($mediaId);

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
        $progressStats = $userId ? $this->progressRepository->getUserProgressStats($userId) : collect();
        $allMaterials  = $this->materialRepository->getMaterialsForListing();

        $allMaterials->load([
            'questions' => function ($query): void {
                $query->select('id', 'material_id', 'difficulty');
            },
        ]);

        $totalMaterials = $allMaterials->count();

        return $allMaterials->map(
            function ($material, $index) use ($progressStats, $isGuest, $totalMaterials): Model {
                $counts                   = ProgressHelper::calculateMaterialQuestionCounts($material, $isGuest);
                $configuredTotalQuestions = $counts['total'];
                $materialProgress         = $progressStats->firstWhere('material_id', $material->id);
                $correctAnswers           = $materialProgress ? $materialProgress->correct_answers : 0;

                $material->progress_percentage = ProgressHelper::calculateProgressPercentage($correctAnswers, $configuredTotalQuestions);
                $material->total_questions     = $configuredTotalQuestions;
                $material->completed_questions = $correctAnswers;

                $material->is_locked = $isGuest && $index >= ceil($totalMaterials / 2);

                return $material;
            },
        );
    }

    public function getMaterialDetail(string $materialId, ?string $userId, bool $isGuest, array $guestProgress = []): array
    {
        $material       = $this->materialRepository->findWithQuestionsShuffled($materialId);
        $allMaterials   = $this->materialRepository->getAllOrdered();
        $totalMaterials = $allMaterials->count();

        $allMaterials->map(function ($m, $index) use ($isGuest, $totalMaterials): Model {
            $m->is_locked = $isGuest && $index >= ceil($totalMaterials / 2);

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
            ? $this->progressRepository->getAnsweredQuestionIds($userId, $materialId)
            : $guestProgress);

        $currentQuestion = $material->questions->whereNotIn('id', $answeredQuestionIds)->first()
            ?? $material->questions->first();

        $answeredCount         = $answeredQuestionIds->count();
        $currentQuestionNumber = ($answeredCount >= $material->questions->count()) ? 'Review' : ($answeredCount + 1);

        return [
            'material'                => $material,
            'materials'               => $materials,
            'current_question_number' => $currentQuestionNumber,
            'current_question'        => $currentQuestion,
        ];
    }

    public function resetMaterialProgress(string $userId, string $materialId): void
    {
        $this->progressRepository->resetProgress($userId, $materialId);
    }

    // =========================================================================
    // HELPERS (MEDIA)
    // =========================================================================

    private function uploadCoverImage(Material $material, mixed $file): void
    {
        $path = $file->store('materials', 'images');
        $this->mediaRepository->create([
            'material_id' => $material->id,
            'media_type'  => 'image',
            'media_url'   => '/images/' . $path,
        ]);
    }

    private function deleteCoverImage(Material $material): void
    {
        $existingMedia = $this->mediaRepository->findByMaterialAndType($material->id, 'image');
        if (! $existingMedia instanceof Media) {
            return;
        }

        $this->removeMediaFile($existingMedia->media_url);
        $this->mediaRepository->delete($existingMedia->id);
    }

    private function removeMediaFile(string $path): void
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
