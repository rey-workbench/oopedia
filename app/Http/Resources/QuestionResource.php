<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Question
 */
final class QuestionResource extends JsonResource
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
            'id'            => $this->resource->id,
            'material_id'   => $this->material_id,
            'question_text' => $this->question_text,
            'question_type' => $this->question_type->value,
            'difficulty'    => $this->difficulty->value,
            'hint'          => $this->hint,
            'answers'       => $this->whenLoaded('answers', fn () => $this->answers->map(fn ($answer): array => [
                'id'             => $answer->id,
                'answer_text'    => $answer->answer_text,
                'is_correct'     => $answer->is_correct,
                'explanation'    => $answer->explanation,
                'drag_source'    => $answer->drag_source,
                'drag_target'    => $answer->drag_target,
                'blank_position' => $answer->blank_position,
            ])),
            'user_attempt' => $this->when(isset($this->resource->user_attempt), $this->resource->user_attempt),
            'created_at'   => $this->created_at?->toIso8601String(),
            'updated_at'   => $this->updated_at?->toIso8601String(),
        ];
    }
}
