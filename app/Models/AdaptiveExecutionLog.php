<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdaptiveExecutionLog extends Model
{
    #[\Override]
    protected $fillable = [
        'user_id',
        'rule_id',
        'action_id',
        'trigger_facts',
        'state_deltas',
        'new_state',
        'execution_context',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'trigger_facts'     => 'array',
            'state_deltas'      => 'array',
            'new_state'         => 'array',
            'execution_context' => 'array',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
