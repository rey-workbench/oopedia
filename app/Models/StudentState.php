<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $user_id
 * @property array<string,mixed> $gamification_data
 * @property array<string,mixed> $learning_profile
 * @property array<string,mixed> $performance_metrics
 * @property array<string,mixed> $adaptive_state
 * @property Carbon|null $last_active_at
 */
final class StudentState extends Model
{
    use HasFactory;
    use HasUlids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'gamification_data',
        'learning_profile',
        'performance_metrics',
        'adaptive_state',
        'last_active_at',
    ];

    protected $casts = [
        'performance_metrics' => 'array',
        'gamification_data'   => 'array',
        'learning_profile'    => 'array',
        'adaptive_state'      => 'array',
        'last_active_at'      => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
