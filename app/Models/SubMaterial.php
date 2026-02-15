<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubMaterial extends Model
{
    protected $fillable = [
        'material_id',
        'title',
        'content',
        'jenis_konten',
        'learning_style',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * SubMaterial belongs to Material.
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * SubMaterial has many Questions.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    // ==================== SCOPES ====================

    /**
     * Scope to order by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Scope to filter by jenis_konten.
     */
    public function scopeByJenis($query, string $jenis)
    {
        return $query->where('jenis_konten', $jenis);
    }

    /**
     * Scope to filter by learning_style.
     */
    public function scopeByLearningStyle($query, string $style)
    {
        return $query->where('learning_style', $style);
    }
}
