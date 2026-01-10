<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentState extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'global_xp',
        'current_level',
        'current_streak',
        'max_streak',
        'learning_style',
        'mastery_levels',
        'adaptive_variables',
        'badges',
        'unlocked_modules',
        'total_questions_answered',
        'correct_count',
        'wrong_count',
        'wrong_streak',
        'hints_used_count',
        'hints_available',
        'last_active_at',
    ];

    protected $casts = [
        'mastery_levels' => 'array',
        'adaptive_variables' => 'array',
        'badges' => 'array',
        'unlocked_modules' => 'array',
        'last_active_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to check if student is visual learner.
     */
    public function isVisualLearner(): bool
    {
        return $this->learning_style === 'visual';
    }

    /**
     * Helper to check if student is textual learner.
     */
    public function isTextualLearner(): bool
    {
        return $this->learning_style === 'textual';
    }

    /**
     * Update performance counters based on answer result.
     */
    public function updatePerformance(bool $isCorrect, int $timeSpent = 0, bool $usedHint = false)
    {
        $this->total_questions_answered++;
        
        if ($usedHint) {
            $this->hints_used_count++;
            $this->hints_available = max(0, $this->hints_available - 1);
        }
        
        if ($isCorrect) {
            $this->correct_count++;
            $this->current_streak++;
            $this->max_streak = max($this->current_streak, $this->max_streak);
            $this->wrong_streak = 0; // Reset wrong streak
            
            // Standard XP Gain (can be overridden by rules later)
            // $this->global_xp += 10; 
        } else {
            $this->wrong_count++;
            $this->wrong_streak++;
            $this->current_streak = 0; // Reset proper streak
            // No XP loss usually
        }

        $this->last_active_at = now();
        $this->save();
        
        return $this;
    }
}
