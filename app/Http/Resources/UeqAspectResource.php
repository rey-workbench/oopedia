<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $id
 * @property string $label
 * @property string $left
 * @property string $right
 */
final class UeqAspectResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'label' => $this->label,
            'left'  => $this->left,
            'right' => $this->right,
        ];
    }
}
