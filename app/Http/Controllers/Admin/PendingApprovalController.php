<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PendingApprovalController extends Controller
{
    protected $userService;

    public function __construct(\App\Services\User\UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $user = auth()->user();

        // Jika user adalah superadmin, redirect ke halaman management pending admin
        if ($user->role_id == 1) {
            return redirect()->route('admin.pending-admins');
        }
        
        // Jika user adalah admin yang belum diapprove, tampilkan halaman menunggu persetujuan
        if ($user->role_id == 2 && !$user->is_approved) {
            // Refresh user data from DB to check latest status
            $freshUser = $this->userService->getUserById($user->id);
            
            // Double check apakah user sudah diapprove
            if ($freshUser && $freshUser->is_approved) {
                return redirect()->route('admin.dashboard');
            }
            
            return view('admin.users.pending-approval');
        }
        
        // Jika bukan superadmin atau admin yang belum diapprove, redirect sesuai role
        if ($user->role_id == 2 && $user->is_approved) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('mahasiswa.dashboard');
        }
    }
} 