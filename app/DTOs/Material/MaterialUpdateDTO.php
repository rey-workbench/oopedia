<?php

declare(strict_types=1);

namespace App\DTOs\Material;

use Illuminate\Http\Request;

final readonly class MaterialUpdateDTO
{
    public function __construct(
        public ?string $title = null,
        public ?string $content = null,
        public ?string $module_id = null,
        public ?bool $is_final_project = null,
        public mixed $cover_image = null,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            title: $request->input('title'),
            content: $request->input('content'),
            module_id: $request->input('module_id'),
            is_final_project: $request->has('is_final_project') ? $request->boolean('is_final_project') : null,
            cover_image: $request->file('cover_image'),
        );
    }

    // Removed: toArray()
}
