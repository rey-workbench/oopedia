<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\MaterialRepository;
use App\Services\UserService;

class ProfileController extends Controller
{
    protected $materialRepo;
    protected $userService;

    public function __construct(
        MaterialRepository $materialRepo,
        UserService $userService
    ) {
        $this->materialRepo = $materialRepo;
        $this->userService = $userService;
    }

    public function show()
    {
        $user = auth()->user();
        $materials = $this->materialRepo->getAllOrdered();
        return view('mahasiswa.profile.index', compact('materials', 'user'));
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