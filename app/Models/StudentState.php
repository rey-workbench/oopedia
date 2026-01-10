<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentState extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'current_level',
        'learning_style',
        'global_xp',
        'badges',
        'unlocked_modules',
        'last_active_at',
    ];

    protected $casts = [
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
    public function updatePerformance(bool $isCorrect, int $timeSpent = 0)
    {
        $this->total_questions_answered++;
        
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
        // Note: We don't save here, caller must save. 
        // Or we can save. Let's save to be safe and simple.
        $this->save();
        
        return $this;
    }
}
