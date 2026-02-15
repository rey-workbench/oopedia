<?php

namespace App\DTOs\User;

use Illuminate\Http\Request;

readonly class UserRegistrationDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public int $role_id,
        public bool $is_approved,
    ) {}

    public static function fromRequest(Request $request): self
    {
        // Determine role_id based on email domain
        $role_id = str_ends_with($request->input('email'), '@admin.oopedia.com') ? 2 : 3;
        $is_approved = ($role_id === 2); // Admin auto-approved

        return new self(
            name: $request->input('name'),
            email: $request->input('email'),
            password: $request->input('password'),
            role_id: $role_id,
            is_approved: $is_approved,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password, // Will be hashed in service
            'role_id' => $this->role_id,
            'is_approved' => $this->is_approved,
        ];
    }
}
