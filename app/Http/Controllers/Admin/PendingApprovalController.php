<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\UserServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PendingApprovalController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService,
    ) {}

    public function index(): Response|RedirectResponse
    {
        $user = Auth::user();

        if ($user->role_id == 1) {
            return redirect()->route('admin.pending-admins');
        }

        if ($user->role_id == 2 && ! $user->is_approved) {
            $freshUser = $this->userService->getUserById($user->id);

            if ($freshUser && $freshUser->is_approved) {
                return redirect()->route('admin.dashboard');
            }

            return Inertia::render('Admin/Users/PendingApproval/Index');
        }

        if ($user->role_id == 2 && $user->is_approved) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('mahasiswa.dashboard');
    }
}
