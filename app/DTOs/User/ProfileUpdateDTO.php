<?php

declare(strict_types=1);

namespace App\DTOs\User;

use Illuminate\Http\Request;

final readonly class ProfileUpdateDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?string $password = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            email: $request->input('email'),
            password: $request->filled('password') ? $request->input('password') : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => $this->password,
        ], static fn (?string $value): bool => $value !== null);
    }
}
