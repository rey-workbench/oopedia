<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $domain
 * @property int $priority
 * @property array $required_facts
 * @property array|null $deduced_facts
 * @property array|null $action_codes
 * @property int|null $action_id
 * @property bool $is_active
 * @property-read AdaptiveAction|null $action
 */
final class AdaptiveRule extends Model
{
    protected $fillable = [
        'code',
        'name',
        'domain',
        'priority',
        'required_facts',
        'deduced_facts',
        'action_codes',
        'action_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'required_facts' => 'array',
            'deduced_facts'  => 'array',
            'action_codes'   => 'array',
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
}
