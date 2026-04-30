<?php

declare(strict_types=1);

namespace App\DTOs\Adaptive;

use Illuminate\Http\Request;

/**
 * Data Transfer Object for Adaptive Action management.
 */
final readonly class AdaptiveActionDTO
{
    public function __construct(
        public ?string $id,
        public string $name,
        public string $description,
        public ?string $variant,
        public array $instructions,
    ) {}

    public static function fromRequest(Request $request, ?string $id = null): self
    {
        return new self(
            id: $id ?? $request->input('id'),
            name: $request->input('name'),
            description: $request->input('description'),
            variant: $request->input('variant'),
            instructions: $request->input('instructions', []),
        );
    }

    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'description'  => $this->description,
            'variant'      => $this->variant,
            'instructions' => $this->instructions,
        ];
    }
}
