<?php

declare(strict_types=1);

namespace App\DTOs\User;

use Illuminate\Http\Request;

final readonly class ProfileUpdateDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?string $nim = null,
        public ?string $class = null,
        public ?string $password = null,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            email: $request->input('email'),
            nim: $request->input('nim'),
            class: $request->input('class'),
            password: $request->filled('password') ? $request->input('password') : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name'     => $this->name,
            'email'    => $this->email,
            'nim'      => $this->nim,
            'class'    => $this->class,
            'password' => $this->password,
        ], static fn (?string $value): bool => $value !== null);
    }
}
