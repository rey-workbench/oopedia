<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\MslqResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MslqResult
 */
final class MslqResultResource extends JsonResource
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
            'id'               => $this->id,
            'user'             => $this->whenLoaded('user', fn () => new UserResource($this->user)->resolve()),
            'assessment_type'  => $this->assessment_type,
            'scores_by_scale'  => $this->scores_by_scale,
            'total_score'      => $this->total_score,
            'total_motivation' => $this->total_motivation,
            'total_strategy'   => $this->total_strategy,
            'created_at'       => $this->created_at?->toIso8601String(),
            'updated_at'       => $this->updated_at?->toIso8601String(),
            'answers'          => $this->whenLoaded('answers', fn () => $this->answers->map(fn ($answer): array => [
                'question' => [
                    'text'       => $answer->question->text,
                    'dimension'  => $answer->question->dimension,
                    'category'   => $answer->question->category,
                    'order'      => $answer->question->order,
                    'scale'      => $answer->question->scale,
                    'is_reverse' => $answer->question->is_reverse,
                    'type'       => $answer->question->type,
                ],
                'value'        => $answer->value,
                'answer_value' => $answer->answer_value,
            ])),
        ];
    }
}
