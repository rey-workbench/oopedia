<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $id
 * @property string $media_url
 * @property string $media_type
 * @property string $material_id
 */
final class MediaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'url'         => str_starts_with($this->media_url, 'http') ? $this->media_url : asset($this->media_url),
            'type'        => $this->media_type,
            'material_id' => $this->material_id,
        ];
    }
}
