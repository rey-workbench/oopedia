<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property array<string,mixed> $gamification_data
 * @property array<string,mixed> $learning_profile
 * @property array<string,mixed> $performance_metrics
 * @property array<string,mixed> $adaptive_state
 * @property \Carbon\Carbon|null $last_active_at
 * @property-read int            $global_xp
 * @property-read string         $current_level
 * @property-read int            $current_streak
 * @property-read int            $max_streak
 * @property-read int            $total_questions_answered
 * @property-read int            $correct_count
 * @property-read int            $wrong_count
 * @property-read int            $wrong_streak
 * @property-read int            $hints_used_count
 * @property-read int            $hints_available
 * @property-read string         $learning_style
 * @property-read array<int,mixed> $unlocked_modules
 */
class StudentState extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gamification_data',
        'learning_profile',
        'performance_metrics',
        'adaptive_state',
        'last_active_at',
    ];

    /**
     * Include virtual attributes in array/JSON representation.
     */
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

    /**
     * CRITICAL: Cast JSON columns to arrays automatically
     * This prevents "Cannot access offset of type string on string" errors
     * Laravel will automatically encode/decode these fields
     */
    protected $casts = [
        'performance_metrics' => 'array',
        'gamification_data'   => 'array',
        'learning_profile'    => 'array',
        'adaptive_state'      => 'array',
        'last_active_at'      => 'datetime',
    ];

    /**
     * Default values for JSON fields
     */
    protected $attributes = [
        'gamification_data'   => '{"global_xp":0,"current_level":"Pemula","current_streak":0,"max_streak":0,"badges":[]}',
        'learning_profile'    => '{"learning_style":"visual","mastery_levels":{},"unlocked_modules":[]}',
        'performance_metrics' => '{"total_questions_answered":0,"correct_count":0,"wrong_count":0,"wrong_streak":0,"hints_used_count":0,"hints_available":3}',
        'adaptive_state'      => '{"fast_track_active":false,"current_material_id":null,"target_difficulty":null,"module_progress":{},"time_metrics":{"avg_time_per_question":0,"total_time_spent":0}}',
    ];

    // ==================== RELATIONSHIPS ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ==================== ACCESSORS ====================

    // Gamification Data (Transparent Access)
    public function getGlobalXpAttribute()
    {
        return $this->gamification_data['global_xp'] ?? 0;
    }

    public function getCurrentLevelAttribute()
    {
        return $this->gamification_data['current_level'] ?? 'Pemula';
    }

    public function getCurrentStreakAttribute()
    {
        return $this->gamification_data['current_streak'] ?? 0;
    }

    public function getMaxStreakAttribute()
    {
        return $this->gamification_data['max_streak'] ?? 0;
    }

    // Performance Metrics (Transparent Access)
    public function getTotalQuestionsAnsweredAttribute()
    {
        return $this->performance_metrics['total_questions_answered'] ?? 0;
    }

    public function getCorrectCountAttribute()
    {
        return $this->performance_metrics['correct_count'] ?? 0;
    }

    public function getWrongCountAttribute()
    {
        return $this->performance_metrics['wrong_count'] ?? 0;
    }

    public function getWrongStreakAttribute()
    {
        return $this->performance_metrics['wrong_streak'] ?? 0;
    }

    public function getHintsUsedCountAttribute()
    {
        return $this->performance_metrics['hints_used_count'] ?? 0;
    }

    public function getHintsAvailableAttribute()
    {
        return $this->performance_metrics['hints_available'] ?? 3;
    }

    // Learning Profile
    public function getLearningStyleAttribute()
    {
        return $this->learning_profile['learning_style'] ?? 'visual';
    }

    public function getUnlockedModulesAttribute()
    {
        return $this->learning_profile['unlocked_modules'] ?? [];
    }

    // ==================== HELPER METHODS ====================

    /**
     * Update performance counters based on answer result.
     * No need for custom accessors/mutators - Laravel handles JSON casting automatically
     */
    public function updatePerformance($isCorrect, $timeSpent, $usedHint)
    {
        // Get current metrics (already decoded by Laravel)
        $metrics      = $this->performance_metrics ?? [];
        $gamification = $this->gamification_data   ?? [];

        // Update counters
        $metrics['total_questions_answered'] = ($metrics['total_questions_answered'] ?? 0) + 1;

        if ($usedHint) {
            $metrics['hints_used_count'] = ($metrics['hints_used_count'] ?? 0) + 1;
            $metrics['hints_available']  = max(0, ($metrics['hints_available'] ?? 3) - 1);
        }

        if ($isCorrect) {
            $metrics['correct_count']       = ($metrics['correct_count'] ?? 0)       + 1;
            $gamification['current_streak'] = ($gamification['current_streak'] ?? 0) + 1;
            $gamification['max_streak']     = max(
                $gamification['max_streak']     ?? 0,
                $gamification['current_streak'] ?? 0,
            );
            $metrics['wrong_streak'] = 0;
        } else {
            $metrics['wrong_count']         = ($metrics['wrong_count'] ?? 0)  + 1;
            $metrics['wrong_streak']        = ($metrics['wrong_streak'] ?? 0) + 1;
            $gamification['current_streak'] = 0;
        }

        // Save back (Laravel will encode automatically)
        $this->performance_metrics = $metrics;
        $this->gamification_data   = $gamification;
        $this->last_active_at      = now();
        $this->save();

        return $this;
    }
}
