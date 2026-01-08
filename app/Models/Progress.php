<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'material_id',
        'question_id',
        'is_correct',
        'is_answered',
        'answer_id',
        'attempt_number',
        'attributes', // JSON Snapshot
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'is_answered' => 'boolean',
        'attributes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get specific attribute value from JSON attributes column
     */
    public function getStateAttribute($key, $default = null)
    {
        $attrs = $this->attributes['attributes'] ?? '{}';
        if (is_string($attrs)) {
            $attrs = json_decode($attrs, true) ?? [];
        }
        return $attrs[$key] ?? $default;
    }

    /**
     * Set specific attribute value in JSON attributes column
     */
    public function setStateAttribute($key, $value)
    {
        $attrs = $this->attributes['attributes'] ?? '{}';
        if (is_string($attrs)) {
            $attrs = json_decode($attrs, true) ?? [];
        }
        $attrs[$key] = $value;
        $this->attributes['attributes'] = json_encode($attrs);
    }

    /**
     * Get time spent on this question in seconds
     */
    public function getTimeSpent(): ?int
    {
        return $this->getStateAttribute('time_spent_seconds');
    }

    /**
     * Set time spent on this question in seconds
     */
    public function setTimeSpent(int $seconds): void
    {
        $this->setStateAttribute('time_spent_seconds', $seconds);
    }

    /**
     * Get topic tags for knowledge gap analysis
     */
    public function getTopicTags(): array
    {
        return $this->getStateAttribute('topic_tags', []);
    }

    /**
     * Set topic tags for knowledge gap analysis
     */
    public function setTopicTags(array $tags): void
    {
        $this->setStateAttribute('topic_tags', $tags);
    }

    /**
     * Get user's initial level (stored in first progress record)
     */
    public function getInitialLevel(): ?string
    {
        return $this->getStateAttribute('initial_level');
    }

    /**
     * Set user's initial level
     */
    public function setInitialLevel(string $level): void
    {
        $this->setStateAttribute('initial_level', $level);
    }

    /**
     * Get user's learning style (stored in first progress record)
     */
    public function getLearningStyle(): ?string
    {
        return $this->getStateAttribute('learning_style');
    }

    /**
     * Set user's learning style
     */
    public function setLearningStyle(string $style): void
    {
        $this->setStateAttribute('learning_style', $style);
    }

    /**
     * Get completed materials list
     */
    public function getCompletedMaterials(): array
    {
        return $this->getStateAttribute('completed_materials', []);
    }

    /**
     * Add a material to completed list
     */
    public function addCompletedMaterial(int $materialId): void
    {
        $completed = $this->getCompletedMaterials();
        if (!in_array($materialId, $completed)) {
            $completed[] = $materialId;
            $this->setStateAttribute('completed_materials', $completed);
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}