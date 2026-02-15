<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\Services\UserServiceInterface;
use App\Contracts\Repositories\RoleRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminUserController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService,
        protected RoleRepositoryInterface $roleRepo
    ) {}

    public function index(Request $request)
    {
        $search = $request->search;
        $users = $this->userService->getAdmins($search);
        
        $pendingAdminsCount = $this->userService->getPendingAdminsCount();

        return Inertia::render('Admin/Users/Index', compact('users', 'pendingAdminsCount'));
    }

    public function create()
    {
        $roles = $this->roleRepo->all();
        return Inertia::render('Admin/Users/Create/Index', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        try {
            $this->userService->createAdmin($request->all());
            
            return redirect()->route('admin.users.index')
                ->with('success', 'Admin berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan admin: ' . $e->getMessage());
        }
    }

    public function edit(int $userId)
    {
        $user = $this->userService->getUserById($userId);
        
        if (!$user) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User tidak ditemukan');
        }
        
        // Ensure editable user is admin
        if ($user->role_id != 2) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User bukan admin');
        }
        
        return Inertia::render('Admin/Users/Edit/Index', compact('user'));
    }

    public function update(Request $request, int $userId)
    {
        $user = $this->userService->getUserById($userId);
        
        if (!$user) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User tidak ditemukan');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        try {
            $this->userService->updateAdmin($userId, $request->all());
            
            return redirect()->route('admin.users.index')
                ->with('success', 'Data admin berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui admin: ' . $e->getMessage());
        }
    }

    public function destroy(int $userId)
    {
        try {
            $this->userService->deleteAdmin($userId);
            
            return redirect()->route('admin.users.index')
                ->with('success', 'Admin berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                ->with('error', $e->getMessage());
        }
    }

    public function pendingAdmins()
    {
        $pendingAdmins = $this->userService->getPendingAdmins();
        
        return Inertia::render('Admin/Users/Pending/Index', compact('pendingAdmins'));
    }

    public function approveAdmin(int $userId)
    {
        try {
            $this->userService->approveAdmin($userId);
            
            return redirect()->route('admin.pending-admins')
                ->with('success', 'Admin berhasil disetujui');
        } catch (\Exception $e) {
            return redirect()->route('admin.pending-admins')
                ->with('error', 'Gagal menyetujui admin: ' . $e->getMessage());
        }
    }

    public function rejectAdmin(int $userId)
    {
        try {
            $this->userService->rejectAdmin($userId);
            
            return redirect()->route('admin.pending-admins')
                ->with('success', 'Permintaan admin ditolak');
        } catch (\Exception $e) {
            return redirect()->route('admin.pending-admins')
                ->with('error', 'Gagal menolak permintaan: ' . $e->getMessage());
        }
    }
    
    public function showImportForm()
    {
        return Inertia::render('Admin/Users/Import/Index');
    }
    
    public function processImport(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:2048',
        ]);
        
        try {
            $result = $this->userService->importAdminsFromFile($request->file('excel_file'));
            
            $message = "Berhasil menambahkan {$result['success_count']} admin.";
            if (!empty($result['error_rows'])) {
                $message .= " Terdapat " . count($result['error_rows']) . " baris dengan error.";
            }
            
            return redirect()->route('admin.users.index')
                ->with('success', $message)
                ->with('importErrors', $result['error_rows']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
    public function downloadTemplate()
    {
        $template = $this->userService->generateImportTemplate();
        
        return response()->stream($template['callback'], 200, $template['headers']);
    }
}