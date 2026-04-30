<?php

declare(strict_types=1);

namespace App\DTOs\Material;

use Illuminate\Http\Request;

final readonly class MaterialCreateDTO
{
    public function __construct(
        public string $title,
        public string $content,
        public string $module_id,
        public string $created_by,
        public bool $is_final_project = false,
        public mixed $cover_image = null,
    ) {}

    public static function fromRequest(Request $request, string $userId): self
    {
        return new self(
            title: $request->input('title'),
            content: $request->input('content'),
            module_id: $request->input('module_id'),
            created_by: $userId,
            is_final_project: $request->boolean('is_final_project'),
            cover_image: $request->file('cover_image'),
        );
    }
}
