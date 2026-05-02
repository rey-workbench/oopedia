<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $id
 * @property string $question_text
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
            'id'            => $this->id,
            'question_text' => $this->question_text,
            'category'      => $this->category,
            'order'         => $this->order,
            'type'          => $this->type,
        ];
    }
}
