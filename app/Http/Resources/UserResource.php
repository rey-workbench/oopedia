<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
final class UserResource extends JsonResource
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
            'email'             => $this->email,
            'nim'               => $this->nim,
            'class'             => $this->class,
            'role_id'           => $this->role_id,
            'is_approved'       => (bool) $this->is_approved,
            'role'              => [
                'role_name' => ($userRole = $this->role?->role_name) instanceof \BackedEnum
                    ? $userRole->value
                    : ($userRole ?? 'guest'),
            ],
            'approved_at'       => $this->approved_at?->toIso8601String(),
            'overall_progress'  => $this->when($this->overall_progress !== null, $this->overall_progress),
            'total_answered'    => $this->when($this->total_answered_questions !== null, $this->total_answered_questions),
            'last_active'       => $this->updated_at?->toIso8601String(),
        ];
    }
}
