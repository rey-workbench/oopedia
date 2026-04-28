<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\UserServiceInterface;
use App\DTOs\User\ProfileUpdateDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\MslqResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

final class ProfileController extends Controller
{
    public function __construct(
        protected MaterialRepositoryInterface $materialRepo,
        protected UserServiceInterface $userService,
        protected ProgressRepositoryInterface $progressRepo,
    ) {}

    public function show(): Response
    {
        $user         = Auth::user();
        $materials    = $this->materialRepo->getAllOrdered();
        $studentState = $this->progressRepo->getOrCreateStudentState($user->id);

        $total   = $studentState->total_answered ?? 0;
        $correct = $studentState->correct_count  ?? 0;

        // Derive learning style from MSLQ result
        $mslqResult      = MslqResult::where('user_id', $user->id)->first();
        $learningProfile = $this->deriveLearningProfile($mslqResult);

        // Derive last adaptive diagnosis from adaptive_state
        $adaptiveState    = $studentState->adaptive_state          ?? [];
        $lastDiagnosis    = $adaptiveState['last_diagnosis']       ?? null;
        $interventions    = $adaptiveState['active_interventions'] ?? [];
        $needsRemedial    = (bool) ($adaptiveState['needs_remedial'] ?? false);

        $personalization = [
            'learning_style'           => $learningProfile['style'],
            'learning_profile_label'   => $learningProfile['label'],
            'mslq_filled'              => $mslqResult !== null,
            'total_motivation'         => $mslqResult?->total_motivation    ?? null,
            'total_strategy'           => $mslqResult?->total_strategy      ?? null,
            'current_level'            => $studentState->level              ?? 'Pemula',
            'global_xp'                => $studentState->xp                 ?? 0,
            'current_streak'           => $studentState->streak             ?? 0,
            'max_streak'               => $studentState->max_streak         ?? 0,
            'total_questions_answered' => $total,
            'correct_count'            => $correct,
            'wrong_count'              => $studentState->wrong_count         ?? 0,
            'hints_used_count'         => $studentState->hints_used          ?? 0,
            'hints_available'          => $studentState->hints_available     ?? 3,
            'accuracy'                 => $total > 0
                ? round(($correct / $total) * 100, 1)
                : 0,
            // Real adaptive engine data
            'last_diagnosis'        => $lastDiagnosis,
            'active_interventions'  => $interventions,
            'needs_remedial'        => $needsRemedial,
            'target_difficulty'     => $studentState->target_difficulty ?? 'beginner',
        ];

        $rawCertifications = $studentState?->certifications ?? [];
        $certifications    = collect($rawCertifications)
            ->map(function (string $type, string $materialId): array {
                $material = $this->materialRepo->find($materialId);

                return [
                    'material_id'    => $materialId,
                    'material_title' => $material?->title ?? 'Object-Oriented Programming',
                    'type'           => $type,
                    'issued_at'      => null,
                ];
            })
            ->values()
            ->toArray();

        return $this->render(
            'Mahasiswa/Profile/Index',
            compact('materials', 'user', 'personalization', 'certifications'),
        );
    }

    private function deriveLearningProfile(?MslqResult $result): array
    {
        if ($result === null) {
            return ['style' => 'unknown', 'label' => 'Belum Diisi'];
        }

        $motivation = $result->total_motivation ?? 0;
        $strategy   = $result->total_strategy   ?? 0;

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

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $dto = ProfileUpdateDTO::fromRequest($request);

        $this->userService->updateProfile(Auth::id(), $dto->toArray());

        return back()->with('success', 'Profile berhasil diperbarui');
    }
}
