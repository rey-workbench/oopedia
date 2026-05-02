<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\AdaptiveFact;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AdaptiveFact
 */
final class AdaptiveFactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'category' => $this->category,
            'logic'    => $this->logic,
        ];
    }
}
