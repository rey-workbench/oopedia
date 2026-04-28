<?php

namespace App\Models;

use App\Rules\Adaptive\Constants\ActionConstants;
use App\Rules\Adaptive\Contracts\AdaptiveRuleInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property int $priority
 * @property array $required_facts
 * @property array|null $deduced_facts
 * @property int $action_id
 * @property bool $is_active
 * @property-read AdaptiveAction $action
 */
class AdaptiveRule extends Model implements AdaptiveRuleInterface
{
    protected $fillable = [
        'code',
        'name',
        'domain',
        'priority',
        'required_facts',
        'deduced_facts',
        'action_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'required_facts' => 'array',
            'deduced_facts'  => 'array',
            'is_active'      => 'boolean',
        ];
    }

    public function action(): BelongsTo
    {
        return $this->belongsTo(AdaptiveAction::class, 'action_id');
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('priority');
    }

    // ── AdaptiveRuleInterface Implementation ─────────────────────────────────

    public function getRuleId(): string
    {
        return $this->code;
    }

    public function getRuleName(): string
    {
        return $this->name;
    }

    public function getActionCode(): string
    {
        return ($this->relationLoaded('action') && $this->action)
            ? $this->action->code
            : ActionConstants::DEDUCTION;
    }

    public function getPriority(): int
    {
        return (int) $this->priority;
    }

    public function evaluate(array $facts): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $required = $this->required_facts ?? [];

        foreach ($required as $code) {
            if (! in_array($code, $facts, true)) {
                return false;
            }
        }

        return true;
    }

    public function getDeducedFacts(): array
    {
        return $this->deduced_facts ?? [];
    }
}
