<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreAdminRequest;
use App\Http\Requests\User\UpdateAdminRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminUserController extends Controller
{
    public function __construct(protected
        UserServiceInterface $userService, protected
        RoleRepositoryInterface $roleRepo,
        )
    {
    }

    public function index(Request $request): Response
    {
        $search = $request->search;
        $users = $this->userService->getAdmins($search);
        $pendingAdminsCount = $this->userService->getPendingAdminsCount();

        return Inertia::render('Admin/Users/Index', compact('users', 'pendingAdminsCount'));
    }

    public function create(): Response
    {
        $roles = $this->roleRepo->all();

        return Inertia::render('Admin/Users/Create/Index', compact('roles'));
    }

    public function store(StoreAdminRequest $request): RedirectResponse
    {
        $this->userService->createAdmin($request->validated());

        return redirect()->route('admin.users.index')
            ->with('success', 'Admin berhasil ditambahkan');
    }

    public function edit(string $userId): Response|RedirectResponse
    {
        $user = $this->userService->getUserById($userId);

        if (!$user) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User tidak ditemukan');
        }

        if (!$user->isDosen()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User bukan admin');
        }

        return Inertia::render('Admin/Users/Edit/Index', compact('user'));
    }

    public function update(UpdateAdminRequest $request, string $userId): RedirectResponse
    {
        $user = $this->userService->getUserById($userId);

        if (!$user) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User tidak ditemukan');
        }

        $this->userService->updateAdmin($userId, $request->validated());

        return redirect()->route('admin.users.index')
            ->with('success', 'Data admin berhasil diperbarui');
    }

    public function destroy(string $userId): RedirectResponse
    {
        $this->userService->deleteAdmin($userId);

        return redirect()->route('admin.users.index')
            ->with('success', 'Admin berhasil dihapus');
    }

    public function pendingApproval(): Response|RedirectResponse
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.pending-admins');
        }

        if ($user->isDosen() && !$user->is_approved) {
            $freshUser = $this->userService->getUserById($user->id);

            if ($freshUser && $freshUser->is_approved) {
                return redirect()->route('admin.dashboard');
            }

            return Inertia::render('Admin/Users/PendingApproval/Index');
        }

        if ($user->isDosen() && $user->is_approved) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('mahasiswa.materials.index');
    }

    public function pendingAdmins(): Response
    {
        $pendingAdmins = $this->userService->getPendingAdmins();

        return Inertia::render('Admin/Users/Pending/Index', compact('pendingAdmins'));
    }

    public function approveAdmin(string $userId): RedirectResponse
    {
        $this->userService->approveAdmin($userId);

        return redirect()->route('admin.pending-admins')
            ->with('success', 'Admin berhasil disetujui');
    }

    public function rejectAdmin(string $userId): RedirectResponse
    {
        $this->userService->rejectAdmin($userId);

        return redirect()->route('admin.pending-admins')
            ->with('success', 'Permintaan admin ditolak');
    }

    public function showImportForm(): Response
    {
        return Inertia::render('Admin/Users/Import/Index');
    }

    public function processImport(Request $request): RedirectResponse
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:2048',
        ]);

        $result = $this->userService->importAdminsFromFile($request->file('excel_file'));

        $message = "Berhasil menambahkan {$result['success_count']} admin.";
        if (!empty($result['error_rows'])) {
            $message .= ' Terdapat ' . count($result['error_rows']) . ' baris dengan error.';
        }

        return redirect()->route('admin.users.index')
            ->with('success', $message)
            ->with('importErrors', $result['error_rows']);
    }

    public function downloadTemplate(): StreamedResponse
    {
        $template = $this->userService->generateImportTemplate();

        return response()->stream($template['callback'], 200, $template['headers']);
    }
}
