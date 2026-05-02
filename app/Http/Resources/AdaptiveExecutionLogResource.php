<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $id
 * @property string $rule_id
 * @property string $action_id
 * @property object $user
 * @property array $execution_context
 * @property Carbon $created_at
 * @property string $rule_name
 * @property string $action_name
 */
final class AdaptiveExecutionLogResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'rule_id'        => $this->rule_id,
            'rule_name'      => $this->rule_name ?? $this->rule_id,
            'action'         => $this->action_id,
            'action_name'    => $this->action_name                         ?? $this->action_id,
            'user_name'      => $this->user->name                          ?? 'System',
            'material_title' => $this->execution_context['material_title'] ?? 'General',
            'created_at'     => $this->created_at->diffForHumans(),
            'timestamp'      => $this->created_at->toIso8601String(),
        ];
    }
}
