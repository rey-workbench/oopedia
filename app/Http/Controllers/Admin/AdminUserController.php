<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Services\UserServiceInterface;
use App\DTOs\User\AdminCreateDTO;
use App\DTOs\User\AdminUpdateDTO;
use App\Enums\User\RoleName;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ImportAdminRequest;
use App\Http\Requests\User\StoreAdminRequest;
use App\Http\Requests\User\UpdateAdminRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class AdminUserController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly RoleRepositoryInterface $roleRepository,
    ) {
    }

    public function index(Request $request): Response
    {
        $search             = $request->search;
        $users              = $this->userService->getAdmins($search);
        $pendingAdminsCount = $this->userService->getPendingAdminsCount();

        return $this->render('Admin/Users/Index', ['users' => $users, 'pendingAdminsCount' => $pendingAdminsCount]);
    }

    public function create(): Response
    {
        $roles = $this->roleRepository->all();

        return $this->render('Admin/Users/Create/Index', ['roles' => $roles]);
    }

    public function store(StoreAdminRequest $storeAdminRequest): RedirectResponse
    {
        $adminCreateDTO = AdminCreateDTO::fromRequest($storeAdminRequest);

        $this->userService->createAdmin($adminCreateDTO->toArray());

        return to_route('admin.users.index')
            ->with('success', 'Admin berhasil ditambahkan');
    }

    public function edit(string $userId): Response|RedirectResponse
    {
        $user = $this->userService->getUserById($userId);

        if (! $user) {
            return to_route('admin.users.index')
                ->with('error', 'User tidak ditemukan');
        }

        if ($user['role']['role_name'] !== RoleName::DOSEN->value) {
            return to_route('admin.users.index')
                ->with('error', 'User bukan admin');
        }

        return $this->render('Admin/Users/Edit/Index', ['user' => $user]);
    }

    public function update(UpdateAdminRequest $updateAdminRequest, string $userId): RedirectResponse
    {
        $user = $this->userService->getUserById($userId);

        if (! $user) {
            return to_route('admin.users.index')
                ->with('error', 'User tidak ditemukan');
        }

        $adminUpdateDTO = AdminUpdateDTO::fromRequest($updateAdminRequest);

        $this->userService->updateAdmin($userId, $adminUpdateDTO->toArray());

        return to_route('admin.users.index')
            ->with('success', 'Data admin berhasil diperbarui');
    }

    public function destroy(string $userId): RedirectResponse
    {
        $this->userService->deleteAdmin($userId);

        return to_route('admin.users.index')
            ->with('success', 'Admin berhasil dihapus');
    }

    public function pendingApproval(): Response|RedirectResponse
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return to_route('admin.pending-admins');
        }

        if (! $user->isDosen()) {
            return to_route('mahasiswa.materials.index');
        }

        if ($user->is_approved) {
            return to_route('admin.dashboard');
        }

        $freshUser = $this->userService->getUserById((string) $user->id);

        if ($freshUser && $freshUser['is_approved']) {
            return to_route('admin.dashboard');
        }

        return $this->render('Admin/Users/PendingApproval/Index');
    }

    public function pendingAdmins(): Response
    {
        $pendingAdmins = $this->userService->getPendingAdmins();

        return $this->render('Admin/Users/Pending/Index', ['pendingAdmins' => $pendingAdmins]);
    }

    public function approveAdmin(string $userId): RedirectResponse
    {
        $this->userService->approveAdmin($userId);

        return to_route('admin.pending-admins')
            ->with('success', 'Admin berhasil disetujui');
    }

    public function rejectAdmin(string $userId): RedirectResponse
    {
        $this->userService->rejectAdmin($userId);

        return to_route('admin.pending-admins')
            ->with('success', 'Permintaan admin ditolak');
    }

    public function showImportForm(): Response
    {
        return $this->render('Admin/Users/Import/Index');
    }

    public function processImport(ImportAdminRequest $importAdminRequest): RedirectResponse
    {
        $result    = $this->userService->importAdminsFromFile($importAdminRequest->file('excel_file'));
        $errorRows = $result['error_rows'] ?? [];

        $message = sprintf('Berhasil menambahkan %s admin.', $result['success_count']);
        if (! empty($errorRows)) {
            $message .= ' Terdapat ' . count($errorRows) . ' baris dengan error.';
        }

        return to_route('admin.users.index')
            ->with('success', $message)
            ->with('importErrors', $errorRows);
    }

    public function downloadTemplate(): StreamedResponse
    {
        $template = $this->userService->generateImportTemplate();

        return response()->stream($template['callback'], 200, $template['headers']);
    }
}
