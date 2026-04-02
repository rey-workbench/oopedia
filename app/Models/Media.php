<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $material_id
 * @property string $media_type
 * @property string $media_url
 * @property-read string $full_url
 */
class Media extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = ['material_id', 'media_type', 'media_url'];

    protected $casts = [
        'material_id' => 'string',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function getFullUrlAttribute(): string
    {
        if (
            str_starts_with($this->media_url, 'http://') ||
            str_starts_with($this->media_url, 'https://')
        ) {
            return $this->media_url;
        }

        $url = str_replace('storage/', '', $this->media_url);

        return asset('storage/' . $url);
    }
}
