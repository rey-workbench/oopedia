<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Lms\MediaType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Media extends Model
{
    use HasFactory;
    use HasUlids;

    #[\Override]
    protected $fillable = ['material_id', 'media_type', 'media_url'];

    #[\Override]
    protected function casts(): array
    {
        return [
            'material_id' => 'string',
            'media_type'  => MediaType::class,
        ];
    }

    /**
     * @return BelongsTo<Material, $this>
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    protected function getFullUrlAttribute(): string
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
