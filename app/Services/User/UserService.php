<?php

namespace App\Services\User;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Exceptions\Domain\UserNotFoundException;
use App\Mail\AdminApproved;
use App\Models\User;
use App\Services\User\Concerns\ImportsCsvUsers;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserService implements UserServiceInterface
{
    use ImportsCsvUsers;

    public function __construct(protected
        UserRepositoryInterface $userRepo,
        )
    {
    }

    public function getUserById(string $id): ?User
    {
        return $this->userRepo->find($id);
    }

    public function getAdmins(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->userRepo->getStudentsWithRole('dosen', $search, $perPage);
    }

    public function createAdmin(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['role_id'] = \App\Models\Role::where('role_name', 'dosen')->value('id');
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
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        else {
            unset($data['password']);
        }

        return $this->userRepo->update($userId, $data);
    }

    public function deleteAdmin(string $userId): void
    {
        $user = $this->userRepo->find($userId);

        if (!$user) {
            throw new UserNotFoundException($userId);
        }

        if ($user->id === Auth::id() || $user->isSuperAdmin()) {
            throw new \RuntimeException('Tidak dapat menghapus user ini.');
        }

        $this->userRepo->delete($user->id);
    }

    public function getPendingAdmins(?int $perPage = null): LengthAwarePaginator
    {
        return $this->userRepo->getUsersByRoleAndApproval('dosen', false, null, $perPage ?? 10);
    }

    public function getPendingAdminsCount(): int
    {
        return $this->userRepo->getUsersByRoleAndApproval('dosen', false, null, 10)->total();
    }

    public function approveAdmin(string $userId): void
    {
        $user = $this->userRepo->find($userId);

        if (!$user) {
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

        $roleDosenId = \App\Models\Role::where('role_name', 'dosen')->value('id');
        $roleMahasiswaId = \App\Models\Role::where('role_name', 'mahasiswa')->value('id');

        if (!isset($data['role_id'])) {
            $data['role_id'] = str_ends_with($data['email'], '@admin.oopedia.com') ? $roleDosenId : $roleMahasiswaId;
        }

        if (!isset($data['is_approved'])) {
            $data['is_approved'] = ($data['role_id'] === $roleDosenId);
        }

        return $this->userRepo->create($data);
    }

    /** @return array<string, mixed> */
    public function importAdminsFromFile(UploadedFile $file): array
    {
        return $this->importUsersFromCsv($file, function (array $rowData) {
            $this->createAdmin($rowData);
        });
    }

    public function generateImportTemplate(): array
    {
        return $this->generateCsvTemplate('admin_template.csv', ['Nama Admin', 'admin@example.com', 'password123']);
    }
}
