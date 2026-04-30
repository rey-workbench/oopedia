<?php

declare(strict_types=1);

namespace App\DTOs\User;

use App\Models\Role;
use Illuminate\Http\Request;

final readonly class UserRegistrationDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $role_id,
        public bool $is_approved,
        public bool $isDosen,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $roleName    = str_ends_with((string) $request->input('email'), '@admin.oopedia.com') ? 'dosen' : 'mahasiswa';
        $role        = Role::where('role_name', $roleName)->first();
        $role_id     = $role?->id ?? '';
        $is_approved = $roleName === 'dosen';
        $isDosen     = $roleName === 'dosen';

        return new self(
            name: $request->input('name'),
            email: $request->input('email'),
            password: $request->input('password'),
            role_id: $role_id,
            is_approved: $is_approved,
            isDosen: $isDosen,
        );
    }

    public function toArray(): array
    {
        return [
            'name'        => $this->name,
            'email'       => $this->email,
            'password'    => $this->password,
            'role_id'     => $this->role_id,
            'is_approved' => $this->is_approved,
        ];
    }
}
