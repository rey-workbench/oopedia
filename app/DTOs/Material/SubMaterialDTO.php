<?php

namespace App\DTOs\Material;

use App\Models\SubMaterial;

readonly class SubMaterialDTO
{
    public function __construct(
        public int $id,
        public int $material_id,
        public string $title,
        public string $content,
        public string $jenis_konten,
        public int $order,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {}

    public static function fromModel(SubMaterial $subMaterial): self
    {
        return new self(
            id: $subMaterial->id,
            material_id: $subMaterial->material_id,
            title: $subMaterial->title,
            content: $subMaterial->content,
            jenis_konten: $subMaterial->jenis_konten,
            order: $subMaterial->order,
            created_at: $subMaterial->created_at?->toDateTimeString(),
            updated_at: $subMaterial->updated_at?->toDateTimeString(),
        );
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            material_id: $data['material_id'],
            title: $data['title'],
            content: $data['content'],
            jenis_konten: $data['jenis_konten'],
            order: $data['order'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'material_id' => $this->material_id,
            'title' => $this->title,
            'content' => $this->content,
            'jenis_konten' => $this->jenis_konten,
            'order' => $this->order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
