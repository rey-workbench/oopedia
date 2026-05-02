<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $material_title
 * @property string $material_id
 * @property string $difficulty
 * @property string $created_at
 * @property bool $is_correct
 * @property string $type
 */
final class ActivityResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        return [
            'material_title' => $this->material_title,
            'material_id'    => $this->material_id,
            'difficulty'     => $this->difficulty,
            'created_at'     => $this->created_at instanceof \DateTimeInterface
                ? $this->created_at->format('Y-m-d H:i:s')
                : $this->created_at,
            'is_correct'     => (bool) $this->is_correct,
            'type'           => $this->type ?? 'progress',
        ];
    }
}
