<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Material
 */
final class MaterialResource extends JsonResource
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
            'id'                  => $this->id,
            'title'               => $this->title,
            'cover_url'           => $this->cover_url,
            'content'             => $this->content,
            'description'         => $this->description ?? '',
            'module_id'           => $this->module_id,
            'is_final_project'    => $this->is_final_project,
            'progress_percentage' => $this->progress_percentage ?? 0,
            'total_questions'     => $this->total_questions     ?? $this->questions_count ?? 0,
            'completed_questions' => $this->completed_questions ?? 0,
            'student_count'       => $this->student_count       ?? $this->active_students ?? 0,
            'completion_rate'     => $this->completion_rate     ?? 0,
            'is_locked'           => $this->is_locked           ?? false,
            'status'              => $this->status              ?? 'not_started',
            'last_accessed'       => $this->last_accessed       ?? null,
            'user_attempt'        => $this->when(isset($this->resource->user_attempt), $this->resource->user_attempt),
            'created_at'          => $this->created_at?->toIso8601String(),
            'updated_at'          => $this->updated_at?->toIso8601String(),

            // Optional: Include relations if loaded
            'media' => $this->whenLoaded('media', fn () => $this->media->map(fn ($media): array => [
                'id'        => $media->id,
                'type'      => $media->media_type,
                'url'       => $media->media_url,
                'full_url'  => $media->media_url ? url($media->media_url) : null,
            ])),

            'creator'         => new UserResource($this->whenLoaded('creator')),
            'questions_count' => $this->questions_count ?? $this->questions?->count() ?? 0,

            // If it's a detail view with stats (DashboardService usage)
            'stats' => $this->when(isset($this->resource->stats), $this->resource->stats),
        ];
    }
}
