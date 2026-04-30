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
 *
 * Gamification
 * @property int $xp
 * @property string $level
 * @property int $streak
 * @property int $max_streak
 * @property array<int, string> $badges
 *
 * Performance (Diagnostic Input)
 * @property int $total_answered
 * @property int $correct_count
 * @property float $accuracy
 * @property int $hints_used
 * @property int $hints_available
 *
 * Adaptive Engine State
 * @property array $session_history
 * @property array $current_session
 * @property array $performance_metrics
 * @property array $adaptive_state
 *
 * Navigation
 * @property string|null $current_material_id
 * @property string|null $target_difficulty
 * @property Carbon|null $last_active_at
 */
final class StudentState extends Model
{
    use HasFactory;
    use HasUlids;

    #[\Override]
    public $incrementing = false;

    #[\Override]
    protected $keyType = 'string';

    #[\Override]
    protected $fillable = [
        'user_id',
        // Gamification
        'xp', 'level', 'streak', 'max_streak', 'badges',
        // Performance
        'total_answered', 'correct_count', 'accuracy',
        'hints_used', 'hints_available',
        // Adaptive Engine
        'session_history', 'current_session', 'performance_metrics', 'adaptive_state',
        // Navigation
        'current_material_id', 'target_difficulty',
        // Meta
        'last_active_at',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'xp'                  => 'integer',
            'streak'              => 'integer',
            'max_streak'          => 'integer',
            'hints_used'          => 'integer',
            'hints_available'     => 'integer',
            'total_answered'      => 'integer',
            'correct_count'       => 'integer',
            'accuracy'            => 'float',
            'badges'              => 'array',
            'session_history'     => 'array',
            'current_session'     => 'array',
            'performance_metrics' => 'array',
            'adaptive_state'      => 'array',
            'last_active_at'      => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
