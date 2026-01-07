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