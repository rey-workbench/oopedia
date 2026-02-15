<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\MaterialRepository;
use App\Services\User\UserService;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function __construct(
        protected \App\Contracts\Repositories\MaterialRepositoryInterface $materialRepo,
        protected \App\Contracts\Services\UserServiceInterface $userService
    ) {}

    public function show()
    {
        $user = auth()->user();
        $materials = $this->materialRepo->getAllOrdered();
        return Inertia::render('Mahasiswa/Profile/Index', compact('materials', 'user'));
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