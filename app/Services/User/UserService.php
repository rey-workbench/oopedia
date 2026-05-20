<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Enums\User\RoleName;
use App\Exceptions\Domain\UserNotFoundException;
use App\Http\Resources\UserResource;
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

final readonly class UserService implements UserServiceInterface
{
    use ImportsCsvUsers;

    public function __construct(
        public UserRepositoryInterface $userRepo,
    ) {
    }

    public function getUserById(string $id): ?array
    {
        $user = $this->userRepo->find($id);

        return $user instanceof User ? new UserResource($user)->resolve() : null;
    }

    public function getAdmins(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $paginator = $this->userRepo->getStudentsWithRole(RoleName::DOSEN->value, $search, $perPage);

        return $paginator->through(fn ($user) => new UserResource($user)->resolve());
    }

    public function createAdmin(array $data): User
    {
        $data['password']    = Hash::make($data['password']);
        $data['role_id']     = Role::where('role_name', RoleName::DOSEN->value)->value('id');
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

    private function updateUser(string $userId, array $data): User
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

        if (! $user instanceof User) {
            throw new UserNotFoundException($userId);
        }

        if ($user->id === Auth::id() || $user->isSuperAdmin()) {
            throw new \RuntimeException('Tidak dapat menghapus user ini.');
        }

        $this->userRepo->delete($user->id);
    }

    public function getPendingAdmins(?int $perPage = null): LengthAwarePaginator
    {
        return $this->userRepo->getUsersByRoleAndApproval(RoleName::DOSEN->value, false, null, $perPage ?? 10);
    }

    public function getPendingAdminsCount(): int
    {
        return $this->userRepo->getUsersByRoleAndApproval(RoleName::DOSEN->value, false, null, 10)->total();
    }

    public function approveAdmin(string $userId): void
    {
        $user = $this->userRepo->find($userId);

        if (! $user instanceof User) {
            throw new UserNotFoundException($userId);
        }

        DB::transaction(function () use ($user): void {
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

        $roleDosenId     = Role::where('role_name', RoleName::DOSEN->value)->value('id');
        $roleMahasiswaId = Role::where('role_name', RoleName::MAHASISWA->value)->value('id');

        $data['role_id'] ??= str_ends_with((string) $data['email'], User::ADMIN_EMAIL_DOMAIN)
            ? $roleDosenId
            : $roleMahasiswaId;

        $data['is_approved'] ??= $data['role_id'] === $roleDosenId;

        return $this->userRepo->create($data);
    }

    public function importAdminsFromFile(UploadedFile $uploadedFile): array
    {
        return $this->importUsersFromCsv($uploadedFile, fn (array $rowData): User => $this->createAdmin($rowData));
    }

    public function generateImportTemplate(): array
    {
        return $this->generateCsvTemplate('admin_template.csv', ['Nama Admin', 'admin@example.com', 'password123']);
    }
}
