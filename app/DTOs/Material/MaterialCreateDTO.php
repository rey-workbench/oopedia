<?php

namespace App\DTOs\Material;

use Illuminate\Http\Request;

readonly class MaterialCreateDTO
{
    public function __construct(
        public string $title,
        public ?string $description,
        public int $created_by,
    ) {}

    public static function fromRequest(Request $request, int $userId): self
    {
        return new self(
            title: $request->input('title'),
            description: $request->input('description'),
            created_by: $userId,
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'created_by' => $this->created_by,
        ];
    }
}
