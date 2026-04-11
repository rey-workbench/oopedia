<?php

namespace App\DTOs\User;

use App\Models\Role;
use Illuminate\Http\Request;

readonly class StudentCreateDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            email: $request->input('email'),
            password: $request->input('password'),
        );
    }

    public function toArray(): array
    {
        return [
            'name'        => $this->name,
            'email'       => $this->email,
            'password'    => $this->password,
            'role_id'     => Role::where('role_name', 'mahasiswa')->value('id'),
            'is_approved' => true,
        ];
    }
}
