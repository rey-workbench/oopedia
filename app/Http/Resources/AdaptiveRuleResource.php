<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\AdaptiveRule;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AdaptiveRule
 */
final class AdaptiveRuleResource extends JsonResource
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
            'id'                => $this->id,
            'name'              => $this->name,
            'description'       => $this->description,
            'recommendation'    => $this->recommendation,
            'priority'          => $this->priority,
            'is_active'         => $this->is_active,
            'required_fact_ids' => $this->required_fact_ids,
            'deduced_fact_ids'  => $this->deduced_fact_ids,
            'actions'           => $this->actions,
            'execution_logs'    => $this->whenLoaded('executionLogs'),
            'created_at'        => $this->created_at?->toIso8601String(),
            'updated_at'        => $this->updated_at?->toIso8601String(),
        ];
    }
}
