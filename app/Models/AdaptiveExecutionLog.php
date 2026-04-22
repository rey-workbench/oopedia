<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdaptiveExecutionLog extends Model
{
    protected $fillable = [
        'user_id',
        'rule_code',
        'action_code',
        'trigger_facts',
        'state_deltas',
        'new_state',
        'execution_context',
    ];

    protected function casts(): array
    {
        return [
            'trigger_facts'     => 'array',
            'state_deltas'      => 'array',
            'new_state'         => 'array',
            'execution_context' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
