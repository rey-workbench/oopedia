<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminUserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $users = $this->userService->getAdmins($search);
        
        $pendingAdminsCount = \App\Models\User::where('role_id', 2)->where('is_approved', false)->count();

        return Inertia::render('Admin/Users/Index', compact('users', 'pendingAdminsCount'));
    }

    public function create()
    {
        $roles = \App\Models\Role::all();
        return Inertia::render('Admin/Users/Create', compact('roles'));
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

    public function edit(User $user)
    {
        // Ensure editable user is admin
        if ($user->role_id != 2) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User bukan admin');
        }
        
        return Inertia::render('Admin/Users/Edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        try {
            $this->userService->updateAdmin($user, $request->all());
            
            return redirect()->route('admin.users.index')
                ->with('success', 'Data admin berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui admin: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            $this->userService->deleteAdmin($user);
            
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
        
        return Inertia::render('Admin/Users/Pending', compact('pendingAdmins'));
    }

    public function approveAdmin(User $user)
    {
        try {
            $this->userService->approveAdmin($user);
            
            return redirect()->route('admin.pending-admins')
                ->with('success', 'Admin berhasil disetujui');
        } catch (\Exception $e) {
            return redirect()->route('admin.pending-admins')
                ->with('error', 'Gagal menyetujui admin: ' . $e->getMessage());
        }
    }

    public function rejectAdmin(User $user)
    {
        try {
            $this->userService->rejectAdmin($user);
            
            return redirect()->route('admin.pending-admins')
                ->with('success', 'Permintaan admin ditolak');
        } catch (\Exception $e) {
            return redirect()->route('admin.pending-admins')
                ->with('error', 'Gagal menolak permintaan: ' . $e->getMessage());
        }
    }
    
    public function showImportForm()
    {
        return Inertia::render('Admin/Users/Import');
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