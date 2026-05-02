<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\UeqSurvey;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin UeqSurvey
 */
final class UeqSurveyResource extends JsonResource
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
            'id'         => $this->id,
            'user'       => new UserResource($this->whenLoaded('user')),
            'nim'        => $this->nim,
            'class'      => $this->class,
            'created_at' => $this->created_at?->toIso8601String(),

            // Detailed column data merge when column1 is present
            $this->mergeWhen($this->column1 !== null, function (): array {
                $columns = [];
                for ($i = 1; $i <= 26; $i++) {
                    $key           = 'column' . $i;
                    $columns[$key] = $this->$key;
                }

                return array_merge($columns, [
                    'comments'    => $this->comments,
                    'suggestions' => $this->suggestions,
                ]);
            }),
        ];
    }
}
