<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Exceptions\Domain\UserNotFoundException;
use App\Mail\AdminApproved;
use App\Models\Role;
use App\Models\User;
use App\Services\User\Concerns\ImportsCsvUsers;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

final class UserService implements UserServiceInterface
{
    use ImportsCsvUsers;

    public function __construct(
        public readonly UserRepositoryInterface $userRepo,
    ) {
    }

    public function getUserById(string $id): ?User
    {
        return $this->userRepo->find($id);
    }

    public function getAdmins(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->userRepo->getStudentsWithRole(Role::ROLE_DOSEN, $search, $perPage);
    }

    public function createAdmin(array $data): User
    {
        $data['password']    = Hash::make($data['password']);
        $data['role_id']     = Role::where('role_name', Role::ROLE_DOSEN)->value('id');
        $data['is_approved'] = true;

        return $this->userRepo->create($data);
    }

    public function updateAdmin(string $userId, array $data): User
    {
        return $this->updateUser($userId, $data);
    }

    public function updateProfile(string $userId, array $data): User
    {
        return $this->updateUser($userId, $data);
    }

    protected function updateUser(string $userId, array $data): User
    {
        if (empty($data['password'] ?? null)) {
            unset($data['password']);

            return $this->userRepo->update($userId, $data);
        }

        $data['password'] = Hash::make($data['password']);

        return $this->userRepo->update($userId, $data);
    }

    public function deleteAdmin(string $userId): void
    {
        $user = $this->userRepo->find($userId);

        if (! $user) {
            throw new UserNotFoundException($userId);
        }

        if ($user->id === Auth::id() || $user->isSuperAdmin()) {
            throw new \RuntimeException('Tidak dapat menghapus user ini.');
        }

        $this->userRepo->delete($user->id);
    }

    public function getPendingAdmins(?int $perPage = null): LengthAwarePaginator
    {
        return $this->userRepo->getUsersByRoleAndApproval(Role::ROLE_DOSEN, false, null, $perPage ?? 10);
    }

    public function getPendingAdminsCount(): int
    {
        return $this->userRepo->getUsersByRoleAndApproval(Role::ROLE_DOSEN, false, null, 10)->total();
    }

    public function approveAdmin(string $userId): void
    {
        $user = $this->userRepo->find($userId);

        if (! $user) {
            throw new UserNotFoundException($userId);
        }

        DB::transaction(function () use ($user) {
            $this->userRepo->approveStudent($user->id);
            Mail::to($user->email)->send(new AdminApproved($user));
        });
    }

    public function rejectAdmin(string $userId): void
    {
        $this->userRepo->delete($userId);
    }

    public function registerUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        $roleDosenId     = Role::where('role_name', Role::ROLE_DOSEN)->value('id');
        $roleMahasiswaId = Role::where('role_name', Role::ROLE_MAHASISWA)->value('id');

        $data['role_id'] ??= str_ends_with($data['email'], User::ADMIN_EMAIL_DOMAIN)
            ? $roleDosenId
            : $roleMahasiswaId;

        $data['is_approved'] ??= $data['role_id'] === $roleDosenId;

        return $this->userRepo->create($data);
    }

    public function importAdminsFromFile(UploadedFile $file): array
    {
        return $this->importUsersFromCsv($file, fn (array $rowData) => $this->createAdmin($rowData));
    }

    public function generateImportTemplate(): array
    {
        return $this->generateCsvTemplate('admin_template.csv', ['Nama Admin', 'admin@example.com', 'password123']);
    }
}
