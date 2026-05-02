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
            'id'          => $this->id,
            'user'        => new UserResource($this->whenLoaded('user')),
            'nim'         => $this->nim,
            'class'       => $this->class,
            'score'       => $this->score,
            'total_score' => $this->total_score,
            'created_at'  => $this->created_at?->toIso8601String(),

            // Detailed answers merge when q1 is present
            $this->mergeWhen($this->q1 !== null, function (): array {
                $details = [];
                for ($i = 1; $i <= 10; $i++) {
                    $details['q' . $i] = $this->{'q' . $i};
                }

                return array_merge($details, [
                    'comments'    => $this->comments,
                    'suggestions' => $this->suggestions,
                ]);
            }),
        ];
    }
}
