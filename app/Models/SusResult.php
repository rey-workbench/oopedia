<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $user_id
 * @property string|null $nim
 * @property string|null $class
 * @property float $total_score
 * @property string|null $comments
 * @property string|null $suggestions
 */
final class SusResult extends Model
{
    use HasFactory;
    use HasUlids;

    #[\Override]
    protected $fillable = [
        'user_id',
        'nim',
        'class',
        'total_score',
        'comments',
        'suggestions',
    ];

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return [
            'total_score' => 'float',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<SusAnswer, $this>
     */
    public function answers(): HasMany
    {
        return $this->hasMany(SusAnswer::class);
    }
}
