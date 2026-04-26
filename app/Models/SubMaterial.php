<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Lms\ContentCategory;
use App\Enums\Lms\LearningStyle;
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

    protected function casts(): array
    {
        return [
            'material_id'    => 'string',
            'order'          => 'integer',
            'jenis_konten'   => ContentCategory::class,
            'learning_style' => LearningStyle::class,
        ];
    }

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
