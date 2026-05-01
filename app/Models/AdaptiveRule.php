<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $name
 * @property string $domain
 * @property int $priority
 * @property array $required_fact_ids
 * @property array $deduced_fact_ids
 * @property array $action_ids
 * @property bool $is_active
 */
final class AdaptiveRule extends Model
{
    #[\Override]
    protected $keyType = 'string';

    #[\Override]
    public $incrementing = false;

    #[\Override]
    protected $fillable = [
        'id',
        'name',
        'recommendation',
        'priority',
        'actions',
        'required_fact_ids',
        'deduced_fact_ids',
        'is_active',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'actions'           => 'array',
            'required_fact_ids' => 'array',
            'deduced_fact_ids'  => 'array',
            'is_active'         => 'boolean',
        ];
    }

    /**
     * Accessor for related Action models.
     * Use $rule->actions for the raw JSON with metadata.
     */
    protected function getActionModelsAttribute()
    {
        $actions = $this->getAttribute('actions') ?? [];

        if (empty($actions)) {
            return collect();
        }

        $ids = array_map(fn ($action): mixed => is_array($action) ? $action['id'] : $action, (array) $actions);

        return AdaptiveAction::whereIn('id', $ids)->get();
    }

    protected function getRequiredFactsAttribute()
    {
        if (empty($this->required_fact_ids)) {
            return collect();
        }

        return AdaptiveFact::whereIn('id', $this->required_fact_ids)->get();
    }

    protected function getDeducedFactsAttribute()
    {
        if (empty($this->deduced_fact_ids)) {
            return collect();
        }

        return AdaptiveFact::whereIn('id', $this->deduced_fact_ids)->get();
    }

    #[Scope]
    protected function ordered(Builder $builder): void
    {
        $builder->orderBy('priority');
    }
}
