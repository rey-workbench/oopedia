<?php

namespace App\DTOs\User;

readonly class UserDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public string $role_id,
        public bool $is_approved,
        public \DateTimeInterface $created_at,
        public \DateTimeInterface $updated_at,
    ) {}

    public static function fromModel($user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            role_id: $user->role_id,
            is_approved: (bool) $user->is_approved,
            created_at: $user->created_at,
            updated_at: $user->updated_at,
        );
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'email'       => $this->email,
            'role_id'     => $this->role_id,
            'is_approved' => $this->is_approved,
            'created_at'  => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'  => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
