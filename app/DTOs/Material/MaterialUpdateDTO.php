<?php

namespace App\DTOs\Material;

use Illuminate\Http\Request;

readonly class MaterialUpdateDTO
{
    public function __construct(
        public ?string $title = null,
        public ?string $content = null,
        public ?string $module_id = null,
        public ?bool $is_final_project = null,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            title: $request->input('title'),
            content: $request->input('content'),
            module_id: $request->input('module_id'),
            is_final_project: $request->has('is_final_project') ? $request->boolean('is_final_project') : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'title'            => $this->title,
            'content'          => $this->content,
            'module_id'        => $this->module_id,
            'is_final_project' => $this->is_final_project,
        ], fn ($value) => $value !== null);
    }
}
