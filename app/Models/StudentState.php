<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Lms\LearningStyle;
use App\Enums\Lms\StudentLevel;
use App\Schemas\StudentStateSchema;
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
 * @property-read int $global_xp
 * @property-read StudentLevel $current_level
 * @property-read int $current_streak
 * @property-read int $max_streak
 * @property-read int $total_questions_answered
 * @property-read int $correct_count
 * @property-read int $wrong_count
 * @property-read int $wrong_streak
 * @property-read int $hints_used_count
 * @property-read int $hints_available
 * @property-read LearningStyle $learning_style
 * @property-read array<int, mixed> $unlocked_modules
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

    protected $appends = [
        'global_xp',
        'current_level',
        'current_streak',
        'max_streak',
        'total_questions_answered',
        'correct_count',
        'wrong_count',
        'wrong_streak',
        'hints_used_count',
        'hints_available',
        'learning_style',
        'unlocked_modules',
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

    public function getGlobalXpAttribute(): int
    {
        return $this->gamification_data[StudentStateSchema::KEY_GLOBAL_XP] ?? 0;
    }

    public function getCurrentLevelAttribute(): StudentLevel
    {
        $value = $this->gamification_data[StudentStateSchema::KEY_CURRENT_LEVEL] ?? StudentLevel::PEMULA->value;

        return StudentLevel::tryFrom($value) ?? StudentLevel::PEMULA;
    }

    public function setCurrentLevelAttribute(StudentLevel|string $value): void
    {
        $level                                               = $value instanceof StudentLevel ? $value->value : $value;
        $gamification                                        = $this->gamification_data ?? [];
        $gamification[StudentStateSchema::KEY_CURRENT_LEVEL] = $level;
        $this->gamification_data                             = $gamification;
    }

    public function getCurrentStreakAttribute(): int
    {
        return $this->gamification_data[StudentStateSchema::KEY_CURRENT_STREAK] ?? 0;
    }

    public function getMaxStreakAttribute(): int
    {
        return $this->gamification_data[StudentStateSchema::KEY_MAX_STREAK] ?? 0;
    }

    public function getTotalQuestionsAnsweredAttribute(): int
    {
        return $this->performance_metrics[StudentStateSchema::KEY_TOTAL_QUESTIONS_ANSWERED] ?? 0;
    }

    public function getCorrectCountAttribute(): int
    {
        return $this->performance_metrics[StudentStateSchema::KEY_CORRECT_COUNT] ?? 0;
    }

    public function getWrongCountAttribute(): int
    {
        return $this->performance_metrics[StudentStateSchema::KEY_WRONG_COUNT] ?? 0;
    }

    public function getWrongStreakAttribute(): int
    {
        return $this->performance_metrics[StudentStateSchema::KEY_WRONG_STREAK] ?? 0;
    }

    public function getHintsUsedCountAttribute(): int
    {
        return $this->performance_metrics[StudentStateSchema::KEY_HINTS_USED_COUNT] ?? 0;
    }

    public function getHintsAvailableAttribute(): int
    {
        return $this->performance_metrics[StudentStateSchema::KEY_HINTS_AVAILABLE]
            ?? StudentStateSchema::DEFAULT_HINTS_AVAILABLE;
    }

    public function getLearningStyleAttribute(): LearningStyle
    {
        $value = $this->learning_profile[StudentStateSchema::KEY_LEARNING_STYLE] ?? LearningStyle::VISUAL->value;

        return LearningStyle::tryFrom($value) ?? LearningStyle::VISUAL;
    }

    public function getUnlockedModulesAttribute(): array
    {
        return $this->learning_profile[StudentStateSchema::KEY_UNLOCKED_MODULES] ?? [];
    }

    /**
     * Update performance and gamification aggregates after an answer attempt.
     *
     * @param bool $isCorrect
     * @param int $timeSpent seconds
     * @param bool $usedHint
     * @param bool $save Persist changes immediately when true (default true)
     * @return self
     */
    public function updatePerformance(bool $isCorrect, int $timeSpent, bool $usedHint, bool $save = true): self
    {
        $metrics      = $this->performance_metrics ?? [];
        $gamification = $this->gamification_data   ?? [];

        $metrics[StudentStateSchema::KEY_TOTAL_QUESTIONS_ANSWERED] =
            ($metrics[StudentStateSchema::KEY_TOTAL_QUESTIONS_ANSWERED] ?? 0) + 1;

        if ($usedHint) {
            $metrics[StudentStateSchema::KEY_HINTS_USED_COUNT] =
                ($metrics[StudentStateSchema::KEY_HINTS_USED_COUNT] ?? 0) + 1;
            $metrics[StudentStateSchema::KEY_HINTS_AVAILABLE] = max(
                0,
                ($metrics[StudentStateSchema::KEY_HINTS_AVAILABLE] ?? StudentStateSchema::DEFAULT_HINTS_AVAILABLE) - 1,
            );
        }

        if ($isCorrect) {
            $metrics[StudentStateSchema::KEY_CORRECT_COUNT] =
                ($metrics[StudentStateSchema::KEY_CORRECT_COUNT] ?? 0) + 1;
            $gamification[StudentStateSchema::KEY_CURRENT_STREAK] =
                ($gamification[StudentStateSchema::KEY_CURRENT_STREAK] ?? 0) + 1;
            $gamification[StudentStateSchema::KEY_MAX_STREAK] = max(
                $gamification[StudentStateSchema::KEY_MAX_STREAK]     ?? 0,
                $gamification[StudentStateSchema::KEY_CURRENT_STREAK] ?? 0,
            );
            $metrics[StudentStateSchema::KEY_WRONG_STREAK] = 0;
        } else {
            $metrics[StudentStateSchema::KEY_WRONG_COUNT] =
                ($metrics[StudentStateSchema::KEY_WRONG_COUNT] ?? 0) + 1;
            $metrics[StudentStateSchema::KEY_WRONG_STREAK] =
                ($metrics[StudentStateSchema::KEY_WRONG_STREAK] ?? 0) + 1;
            $gamification[StudentStateSchema::KEY_CURRENT_STREAK] = 0;
        }

        $this->performance_metrics = $metrics;
        $this->gamification_data   = $gamification;
        $this->last_active_at      = now();

        if ($save) {
            $this->save();
        }

        return $this;
    }

    /**
     * Add XP to gamification data.
     */
    public function addXp(int $xp, bool $save = true): self
    {
        $gamification = $this->gamification_data ?? [];
        $gamification[StudentStateSchema::KEY_GLOBAL_XP] = (
            $gamification[StudentStateSchema::KEY_GLOBAL_XP] ?? 0
        ) + $xp;

        $this->gamification_data = $gamification;

        if ($save) {
            $this->save();
        }

        return $this;
    }

    /**
     * Consume hint(s) and update performance metrics accordingly.
     */
    public function useHint(int $count = 1, bool $save = true): self
    {
        $metrics = $this->performance_metrics ?? [];
        $metrics[StudentStateSchema::KEY_HINTS_USED_COUNT] = (
            $metrics[StudentStateSchema::KEY_HINTS_USED_COUNT] ?? 0
        ) + $count;

        $metrics[StudentStateSchema::KEY_HINTS_AVAILABLE] = max(
            0,
            ($metrics[StudentStateSchema::KEY_HINTS_AVAILABLE] ?? StudentStateSchema::DEFAULT_HINTS_AVAILABLE) - $count
        );

        $this->performance_metrics = $metrics;

        if ($save) {
            $this->save();
        }

        return $this;
    }

    /**
     * Increment current streak and update max streak.
     */
    public function incrementStreak(bool $save = true): self
    {
        $gamification = $this->gamification_data ?? [];
        $gamification[StudentStateSchema::KEY_CURRENT_STREAK] = (
            $gamification[StudentStateSchema::KEY_CURRENT_STREAK] ?? 0
        ) + 1;

        $gamification[StudentStateSchema::KEY_MAX_STREAK] = max(
            $gamification[StudentStateSchema::KEY_MAX_STREAK] ?? 0,
            $gamification[StudentStateSchema::KEY_CURRENT_STREAK]
        );

        $this->gamification_data = $gamification;

        if ($save) {
            $this->save();
        }

        return $this;
    }

    /**
     * Reset current streak to zero.
     */
    public function resetStreak(bool $save = true): self
    {
        $gamification = $this->gamification_data ?? [];
        $gamification[StudentStateSchema::KEY_CURRENT_STREAK] = 0;
        $this->gamification_data = $gamification;

        if ($save) {
            $this->save();
        }

        return $this;
    }

    public function save(array $options = []): bool
    {
        if ($this->user_id === 'guest') {
            return true;
        }

        return parent::save($options);
    }
}
