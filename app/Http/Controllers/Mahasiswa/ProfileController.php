<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\UserServiceInterface;
use App\DTOs\User\ProfileUpdateDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Schemas\StudentStateSchema;
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
        $user      = Auth::user();
        $materials = $this->materialRepo->getAllOrdered();

        $studentState = $this->progressRepo->getOrCreateStudentState($user->id);

        $personalization = [
            'learning_style'           => $studentState->learning_profile[StudentStateSchema::KEY_LEARNING_STYLE]              ?? 'visual',
            'current_level'            => $studentState->gamification_data[StudentStateSchema::KEY_CURRENT_LEVEL]              ?? 'Pemula',
            'global_xp'                => $studentState->gamification_data[StudentStateSchema::KEY_GLOBAL_XP]                  ?? 0,
            'current_streak'           => $studentState->gamification_data[StudentStateSchema::KEY_CURRENT_STREAK]             ?? 0,
            'max_streak'               => $studentState->gamification_data[StudentStateSchema::KEY_MAX_STREAK]                 ?? 0,
            'total_questions_answered' => $studentState->performance_metrics[StudentStateSchema::KEY_TOTAL_QUESTIONS_ANSWERED] ?? 0,
            'correct_count'            => $studentState->performance_metrics[StudentStateSchema::KEY_CORRECT_COUNT]            ?? 0,
            'wrong_count'              => $studentState->performance_metrics[StudentStateSchema::KEY_WRONG_COUNT]              ?? 0,
            'hints_used_count'         => $studentState->performance_metrics[StudentStateSchema::KEY_HINTS_USED_COUNT]         ?? 0,
            'hints_available'          => $studentState->performance_metrics[StudentStateSchema::KEY_HINTS_AVAILABLE]          ?? 3,
            'accuracy'                 => ($studentState->performance_metrics[StudentStateSchema::KEY_TOTAL_QUESTIONS_ANSWERED] ?? 0) > 0
                ? round((($studentState->performance_metrics[StudentStateSchema::KEY_CORRECT_COUNT] ?? 0) / $studentState->performance_metrics[StudentStateSchema::KEY_TOTAL_QUESTIONS_ANSWERED]) * 100, 1)
                : 0,
            'fast_track_active' => $studentState->adaptive_state['fast_track_active'] ?? false,
        ];

        $rawCertifications = $studentState?->learning_profile['certifications'] ?? [];
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

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $dto = ProfileUpdateDTO::fromRequest($request);

        $this->userService->updateProfile(Auth::id(), $dto->toArray());

        return back()->with('success', 'Profile berhasil diperbarui');
    }
}
