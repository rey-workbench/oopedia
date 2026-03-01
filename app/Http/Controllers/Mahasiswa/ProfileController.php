<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
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
            'learning_style'           => $studentState->learning_style           ?? 'visual',
            'current_level'            => $studentState->current_level            ?? 'Pemula',
            'global_xp'                => $studentState->global_xp                ?? 0,
            'current_streak'           => $studentState->current_streak           ?? 0,
            'max_streak'               => $studentState->max_streak               ?? 0,
            'total_questions_answered' => $studentState->total_questions_answered ?? 0,
            'correct_count'            => $studentState->correct_count            ?? 0,
            'wrong_count'              => $studentState->wrong_count              ?? 0,
            'hints_used_count'         => $studentState->hints_used_count         ?? 0,
            'hints_available'          => $studentState->hints_available          ?? 3,
            'accuracy'                 => $studentState->total_questions_answered > 0
                ? round(($studentState->correct_count / $studentState->total_questions_answered) * 100, 1)
                : 0,
            'fast_track_active' => ($studentState->adaptive_state['fast_track_active'] ?? false),
        ];

        return Inertia::render('Mahasiswa/Profile/Index', compact('materials', 'user', 'personalization'));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::id();

        $data = $request->only('name', 'email');

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $this->userService->updateProfile($user, $data);

        return back()->with('success', 'Profile berhasil diperbarui');
    }
}
