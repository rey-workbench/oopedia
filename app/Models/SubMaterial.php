<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $material_id
 * @property string $title
 * @property string $content
 * @property string $jenis_konten
 * @property string|null $learning_style
 * @property int $order
 */
final class SubMaterial extends Model
{
    use HasUlids;

    protected $fillable = [
        'material_id',
        'title',
        'content',
        'jenis_konten',
        'learning_style',
        'order',
    ];

    protected $casts = [
        'material_id' => 'string',
        'order'       => 'integer',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }
}
