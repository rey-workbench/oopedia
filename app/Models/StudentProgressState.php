<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProgressState extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'material_id',
        'current_level',
        'current_streak',
        'wrong_streak',
        'total_xp',
        'total_points',
        'hints_remaining',
        'retry_count',
        'level_correct_count',
        'level_attempt_count',
        'badges',
        'last_activity_at'
    ];

    protected $casts = [
        'badges' => 'array',
        'last_activity_at' => 'datetime',
        'used_hint' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
