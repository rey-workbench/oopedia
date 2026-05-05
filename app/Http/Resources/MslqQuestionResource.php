<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $id
 * @property string $text
 * @property string $category
 * @property int $order
 * @property string $type
 */
final class MslqQuestionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'text'       => $this->text,
            'category'   => $this->category instanceof \BackedEnum ? $this->category->value : $this->category,
            'scale'      => $this->scale,
            'is_reverse' => $this->is_reverse,
            'order'      => $this->order,
        ];
    }
}
