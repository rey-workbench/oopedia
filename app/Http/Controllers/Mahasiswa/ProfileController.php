<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\MslqServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\DTOs\User\ProfileUpdateDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\CertificateResource;
use App\Http\Resources\MaterialResource;
use App\Http\Resources\UserResource;
use App\Models\MslqResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

final class ProfileController extends Controller
{
    public function __construct(
        private readonly MaterialRepositoryInterface $materialRepository,
        private readonly UserServiceInterface $userService,
        private readonly ProgressRepositoryInterface $progressRepository,
        private readonly MslqServiceInterface $mslqService,
    ) {}

    public function show(): Response
    {
        $user         = Auth::user();
        $materials    = $this->materialRepository->getAllOrdered();
        $studentState = $this->progressRepository->getOrCreateStudentState($user->id);

        $total   = $studentState->total_answered ?? 0;
        $correct = $studentState->correct_count  ?? 0;

        // Derive learning style from MSLQ result
        $mslqResult      = $this->mslqService->getMslqResultForUser($user->id);
        $learningProfile = $this->deriveLearningProfile($mslqResult);

        // Derive last adaptive diagnosis from adaptive_state
        $adaptiveState = $studentState->adaptive_state          ?? [];
        $lastDiagnosis = $adaptiveState['last_diagnosis']       ?? null;
        $interventions = $adaptiveState['active_interventions'] ?? [];
        $needsRemedial = (bool) ($adaptiveState['needs_remedial'] ?? false);

        $personalization = [
            'learning_style'           => $learningProfile['style'],
            'learning_profile_label'   => $learningProfile['label'],
            'mslq_filled'              => $mslqResult instanceof MslqResult,
            'total_motivation'         => $mslqResult?->total_motivation ?? null,
            'total_strategy'           => $mslqResult?->total_strategy   ?? null,
            'current_level'            => $studentState->level           ?? 'Pemula',
            'global_xp'                => $studentState->xp              ?? 0,
            'current_streak'           => $studentState->streak          ?? 0,
            'max_streak'               => $studentState->max_streak      ?? 0,
            'total_questions_answered' => $total,
            'correct_count'            => $correct,
            'hints_used_count'         => $studentState->hints_used      ?? 0,
            'hints_available'          => $studentState->hints_available ?? 3,
            'accuracy'                 => $total > 0
                ? round(($correct / $total) * 100, 1)
                : 0,
            // Real adaptive engine data
            'last_diagnosis'       => $lastDiagnosis,
            'active_interventions' => $interventions,
            'needs_remedial'       => $needsRemedial,
            'target_difficulty'    => $studentState->target_difficulty ?? 'beginner',
        ];

        $rawCertifications = $studentState->certifications ?? [];
        $certifications    = collect($rawCertifications)
            ->map(function (string $type, string $materialId): array {
                $material = $this->materialRepository->find($materialId);

                return [
                    'material_id'    => $materialId,
                    'material_title' => $material?->title ?? 'Object-Oriented Programming',
                    'type'           => $type,
                    'issued_at'      => null,
                ];
            })
            ->values();

        return $this->render(
            'Mahasiswa/Profile/Index',
            [
                'materials'       => MaterialResource::collection($materials)->resolve(),
                'user'            => new UserResource($user)->resolve(),
                'personalization' => $personalization,
                'certifications'  => CertificateResource::collection($certifications)->resolve(),
            ],
        );
    }

    private function deriveLearningProfile(?MslqResult $mslqResult): array
    {
        if (! $mslqResult instanceof MslqResult) {
            return ['style' => 'unknown', 'label' => 'Belum Diisi'];
        }

        $motivation = $mslqResult->total_motivation ?? 0;
        $strategy   = $mslqResult->total_strategy   ?? 0;

        if ($motivation >= 5.0 && $strategy >= 5.0) {
            return ['style' => 'deep', 'label' => 'Pelajar Mendalam'];
        }

        if ($motivation > $strategy) {
            return ['style' => 'motivated', 'label' => 'Termotivasi Tinggi'];
        }

        if ($strategy > $motivation) {
            return ['style' => 'strategic', 'label' => 'Strategis'];
        }

        return ['style' => 'balanced', 'label' => 'Seimbang'];
    }

    public function update(UpdateProfileRequest $updateProfileRequest): RedirectResponse
    {
        $profileUpdateDTO = ProfileUpdateDTO::fromRequest($updateProfileRequest);

        $this->userService->updateProfile(Auth::id(), $profileUpdateDTO->toArray());

        return back()->with('success', 'Profile berhasil diperbarui');
    }
}
