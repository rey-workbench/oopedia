<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $material_id
 * @property string $title
 * @property string $content
 * @property string $jenis_konten
 * @property string|null $learning_style
 * @property int $order
 */
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
        'material_id' => 'integer',
        'order' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    // ==================== SCOPES ====================

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function scopeByJenis($query, string $jenis)
    {
        return $query->where('jenis_konten', $jenis);
    }

    public function scopeByLearningStyle($query, string $style)
    {
        return $query->where('learning_style', $style);
    }
}
