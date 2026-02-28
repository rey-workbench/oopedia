<?php

namespace App\DTOs\Material;

use Illuminate\Http\Request;

readonly class MaterialUpdateDTO
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            title: $request->input('title'),
            description: $request->input('description'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'description' => $this->description,
        ], fn ($value) => $value !== null);
    }
}
