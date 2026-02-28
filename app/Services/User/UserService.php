<?php

namespace App\Services\User;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Exceptions\Domain\UserNotFoundException;
use App\Mail\AdminApproved;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserService implements UserServiceInterface
{
    public function __construct(
        protected UserRepositoryInterface $userRepo,
    ) {}

    public function getUserById(int $id): ?User
    {
        return $this->userRepo->find($id);
    }

    public function getAdmins(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->userRepo->getStudentsWithRole(2, $search, $perPage);
    }

    public function createAdmin(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['role_id'] = 2;
        $data['is_approved'] = true;

        return $this->userRepo->create($data);
    }

    public function updateAdmin(int $userId, array $data): User
    {
        if (isset($data['password']) && ! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $this->userRepo->update($userId, $data);
    }

    public function updateProfile(int $userId, array $data): User
    {
        if (isset($data['password']) && ! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $this->userRepo->update($userId, $data);
    }

    public function deleteAdmin(int $userId): void
    {
        $user = $this->userRepo->find($userId);

        if (! $user) {
            throw new UserNotFoundException($userId);
        }

        if ($user->id === Auth::id() || $user->role_id === 1) {
            throw new \RuntimeException('Tidak dapat menghapus user ini.');
        }

        $this->userRepo->delete($user->id);
    }

    public function getPendingAdmins(?int $perPage = null): LengthAwarePaginator
    {
        return $this->userRepo->getUsersByRoleAndApproval(2, false, null, $perPage ?? 10);
    }

    public function getPendingAdminsCount(): int
    {
        return $this->userRepo->getUsersByRoleAndApproval(2, false, null, 10)->total();
    }

    public function approveAdmin(int $userId): void
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

    public function rejectAdmin(int $userId): void
    {
        $this->userRepo->delete($userId);
    }

    public function registerUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        if (! isset($data['role_id'])) {
            $data['role_id'] = str_ends_with($data['email'], '@admin.oopedia.com') ? 2 : 3;
        }

        if (! isset($data['is_approved'])) {
            $data['is_approved'] = ($data['role_id'] === 2);
        }

        return $this->userRepo->create($data);
    }

    public function createStudent(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['role_id'] = 3;
        $data['is_approved'] = true;

        return $this->userRepo->create($data);
    }

    /** @return array<string, mixed> */
    public function importAdminsFromFile(UploadedFile $file): array
    {
        $path = $file->getRealPath();
        $successCount = 0;
        $errorRows = [];

        if (($handle = fopen($path, 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ',');
            $requiredColumns = ['name', 'email', 'password'];
            $missingColumns = array_diff($requiredColumns, $header);

            if (! empty($missingColumns)) {
                throw new \RuntimeException('File tidak memiliki kolom yang diperlukan: ' . implode(', ', $missingColumns));
            }

            $nameIndex = array_search('name', $header);
            $emailIndex = array_search('email', $header);
            $passwordIndex = array_search('password', $header);
            $rowNumber = 1;

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $rowNumber++;

                if (empty($row[$nameIndex]) && empty($row[$emailIndex])) {
                    continue;
                }

                $rowData = [
                    'name' => $row[$nameIndex] ?? '',
                    'email' => $row[$emailIndex] ?? '',
                    'password' => $row[$passwordIndex] ?? '',
                ];

                $validator = Validator::make($rowData, [
                    'name' => 'required|string|max:255',
                    'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
                    'password' => 'required|string|min:8',
                ]);

                if ($validator->fails()) {
                    $errorRows[] = ['row' => $rowNumber, 'errors' => $validator->errors()->all()];

                    continue;
                }

                try {
                    $this->createAdmin([
                        'name' => $row[$nameIndex],
                        'email' => $row[$emailIndex],
                        'password' => $row[$passwordIndex],
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    $errorRows[] = ['row' => $rowNumber, 'errors' => [$e->getMessage()]];
                }
            }

            fclose($handle);
        }

        return ['success_count' => $successCount, 'error_rows' => $errorRows];
    }

    public function generateImportTemplate(): array
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="admin_template.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['name', 'email', 'password']);
            fputcsv($file, ['Nama Admin', 'admin@example.com', 'password123']);
            fclose($file);
        };

        return ['headers' => $headers, 'callback' => $callback];
    }
}
