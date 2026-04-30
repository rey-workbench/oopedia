<?php

declare(strict_types=1);

namespace App\DTOs\Adaptive;

use Illuminate\Http\Request;

/**
 * Data Transfer Object for Adaptive Rule management.
 *
 * Note: Metadata is now encapsulated within each action in the actions array.
 */
final readonly class AdaptiveRuleDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $recommendation,
        public int $priority,
        public array $actions,
        public array $required_fact_ids,
        public array $deduced_fact_ids,
        public bool $is_active,
        public ?string $logic = null,
        public array $facts = [],
        public array $deduced_facts = [],
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            id: $request->input('id'),
            name: $request->input('name'),
            recommendation: $request->input('recommendation'),
            priority: (int) $request->input('priority'),
            actions: $request->input('actions', []), // Contains objects with id and metadata
            required_fact_ids: $request->input('required_fact_ids', []),
            deduced_fact_ids: $request->input('deduced_fact_ids', []),
            is_active: $request->boolean('is_active'),
            logic: $request->input('logic'),
            facts: $request->input('facts', []),
            deduced_facts: $request->input('deduced_facts', []),
        );
    }

    public function toArray(): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'recommendation'    => $this->recommendation,
            'priority'          => $this->priority,
            'actions'           => $this->actions,
            'required_fact_ids' => $this->required_fact_ids,
            'deduced_fact_ids'  => $this->deduced_fact_ids,
            'is_active'         => $this->is_active,
            'logic'             => $this->logic,
        ];
    }
}
