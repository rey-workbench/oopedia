<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    const TYPE_FILL_IN_THE_BLANK = 'fill_in_the_blank';
    const TYPE_RADIO_BUTTON = 'radio_button';
    const TYPE_DRAG_AND_DROP = 'drag_and_drop';
    
    protected $fillable = [
        'material_id',
        'sub_material_id',
        'question_text',
        'question_type',
        'type',
        'difficulty',
        'hint',
        'created_by'
    ];

    protected $casts = [
        'material_id' => 'integer',
        'sub_material_id' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Question belongs to Material.
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Question belongs to SubMaterial.
     */
    public function subMaterial()
    {
        return $this->belongsTo(SubMaterial::class);
    }

    /**
     * Question belongs to User (creator).
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Question has many Answers.
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Question has many QuizAttempts.
     */
    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // ==================== SCOPES ====================

    /**
     * Scope to filter by difficulty.
     */
    public function scopeByDifficulty($query, string $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    /**
     * Scope to filter by question type (adaptive).
     */
    public function scopeByTypeAdaptive($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get final project questions.
     */
    public function scopeFinalProject($query)
    {
        return $query->where('difficulty', 'final');
    }
}