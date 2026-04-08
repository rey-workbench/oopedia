<?php

namespace App\DTOs\Material;

use Illuminate\Http\Request;

readonly class MaterialCreateDTO
{
    public function __construct(
        public string $title,
        public string $content,
        public string $module_id,
        public string $created_by,
        public bool $is_final_project = false,
    ) {}

    public static function fromRequest(Request $request, string $userId): self
    {
        return new self(
            title: $request->input('title'),
            content: $request->input('content'),
            module_id: $request->input('module_id'),
            created_by: $userId,
            is_final_project: $request->boolean('is_final_project'),
        );
    }

    public function toArray(): array
    {
        return [
            'title'            => $this->title,
            'content'          => $this->content,
            'module_id'        => $this->module_id,
            'created_by'       => $this->created_by,
            'is_final_project' => $this->is_final_project,
        ];
    }
}
