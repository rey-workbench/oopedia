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
 * Learning Profile
 * @property string $learning_style
 * @property array<int, string> $unlocked_modules
 * @property array<string, string> $certifications
 * @property array<string, int> $time_distribution
 *
 * Performance
 * @property int $total_answered
 * @property int $correct_count
 * @property int $wrong_count
 * @property int $wrong_streak
 * @property int $hints_used
 * @property int $hints_available
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

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        // Gamification
        'xp', 'level', 'streak', 'max_streak', 'badges',
        // Learning profile
        'learning_style', 'unlocked_modules', 'certifications', 'time_distribution',
        // Performance
        'total_answered', 'correct_count', 'wrong_count', 'wrong_streak',
        'hints_used', 'hints_available',
        // Navigation
        'current_material_id', 'target_difficulty',
        // Meta
        'last_active_at',
    ];

    protected $casts = [
        'xp'                 => 'integer',
        'streak'             => 'integer',
        'max_streak'         => 'integer',
        'hints_used'         => 'integer',
        'hints_available'    => 'integer',
        'total_answered'     => 'integer',
        'correct_count'      => 'integer',
        'wrong_count'        => 'integer',
        'wrong_streak'       => 'integer',
        'badges'             => 'array',
        'unlocked_modules'   => 'array',
        'certifications'     => 'array',
        'time_distribution'  => 'array',
        'last_active_at'     => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
