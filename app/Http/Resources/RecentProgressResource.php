<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin QuizAttempt
 */
final class RecentProgressResource extends JsonResource
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
            'id'          => $this->id,
            'user'        => new UserResource($this->whenLoaded('user')),
            'material'    => $this->whenLoaded('question', fn (): array => [
                'id'    => $this->question->material->id    ?? null,
                'title' => $this->question->material->title ?? null,
            ]),
            'progress'    => $this->score,
            'updated_at'  => $this->updated_at?->toIso8601String(),
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}
