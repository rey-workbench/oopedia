<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $material_id
 * @property string $material_title
 * @property string $type
 * @property string|null $issued_at
 */
final class CertificateResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        return [
            'material_id'    => $this['material_id'],
            'material_title' => $this['material_title'],
            'type'           => $this['type'],
            'issued_at'      => $this['issued_at'] ?? null,
        ];
    }
}
