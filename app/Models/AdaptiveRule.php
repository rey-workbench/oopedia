<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdaptiveRule extends Model
{
    protected $fillable = [
        'rule_code',
        'name',
        'priority',
        'required_facts',
        'forbidden_facts',
        'action_id',
        'is_active',
    ];

    protected $casts = [
        'required_facts'  => 'array',
        'forbidden_facts' => 'array',
        'is_active'       => 'boolean',
    ];

    public function action(): BelongsTo
    {
        return $this->belongsTo(AdaptiveAction::class, 'action_id');
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('priority');
    }
}
