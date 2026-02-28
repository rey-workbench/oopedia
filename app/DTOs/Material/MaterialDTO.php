<?php

namespace App\DTOs\Material;

readonly class MaterialDTO
{
    public function __construct(
        public int $id,
        public string $title,
        public ?string $description,
        public ?int $created_by,
        public \DateTimeInterface $created_at,
        public \DateTimeInterface $updated_at,
    ) {}

    public static function fromModel($material): self
    {
        return new self(
            id: $material->id,
            title: $material->title,
            description: $material->description,
            created_by: $material->created_by,
            created_at: $material->created_at,
            updated_at: $material->updated_at,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
