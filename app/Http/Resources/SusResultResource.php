<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\SusResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SusResult
 */
final class SusResultResource extends JsonResource
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
            'id'              => $this->id,
            'user'            => $this->whenLoaded('user', fn () => (new UserResource($this->user))->resolve()),
            'assessment_type' => $this->assessment_type,
            'total_score'     => $this->total_score,
            'created_at'      => $this->created_at?->toIso8601String(),
            'comments'        => $this->comments,
            'suggestions'     => $this->suggestions,
            'answers'         => $this->whenLoaded('answers', fn () => $this->answers->map(fn ($answer): array => [
                'order' => $answer->question->order,
                'text'  => $answer->question->text,
                'value' => $answer->value,
            ])->sortBy('order')->values()),
        ];
    }
}
