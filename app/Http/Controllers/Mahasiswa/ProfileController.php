<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\MaterialRepository;
use App\Services\User\UserService;
use App\Repositories\ProgressRepository;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function __construct(
        protected \App\Contracts\Repositories\MaterialRepositoryInterface $materialRepo,
        protected \App\Contracts\Services\UserServiceInterface $userService,
        protected ProgressRepository $progressRepo
    ) {}

    public function show()
    {
        $user = auth()->user();
        $materials = $this->materialRepo->getAllOrdered();
        
        // Get student state with personalization data
        $studentState = $this->progressRepo->getStudentState($user->id);
        
        // Extract personalization data
        $personalization = [
            'learning_style' => $studentState->learning_style ?? 'visual',
            'current_level' => $studentState->current_level ?? 'Pemula',
            'global_xp' => $studentState->global_xp ?? 0,
            'current_streak' => $studentState->current_streak ?? 0,
            'max_streak' => $studentState->max_streak ?? 0,
            'total_questions_answered' => $studentState->total_questions_answered ?? 0,
            'correct_count' => $studentState->correct_count ?? 0,
            'wrong_count' => $studentState->wrong_count ?? 0,
            'hints_used_count' => $studentState->hints_used_count ?? 0,
            'hints_available' => $studentState->hints_available ?? 3,
            'accuracy' => $studentState->total_questions_answered > 0 
                ? round(($studentState->correct_count / $studentState->total_questions_answered) * 100, 1)
                : 0,
            'fast_track_active' => ($studentState->adaptive_state['fast_track_active'] ?? false),
        ];
        
        return Inertia::render('Mahasiswa/Profile/Index', compact('materials', 'user', 'personalization'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $this->userService->updateProfile($user, $data);

        return back()->with('success', 'Profile berhasil diperbarui');
    }
} 