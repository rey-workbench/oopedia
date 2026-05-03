<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

final class Material extends Model
{
    use HasFactory;
    use HasUlids;

    /**
     * @return HasManyThrough<QuizAttempt, Question, $this>
     */
    #[\Override]
    protected $fillable = [
        'title',
        'cover_url',
        'content',
        'module_id',
        'created_by',
        'is_final_project',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'is_final_project' => 'boolean',
            'module_id'        => 'string',
            'created_by'       => 'string',
        ];
    }

    /**
     * @return HasMany<Question, $this>
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * @return HasMany<Media, $this>
     */
    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    /**
     * @return HasManyThrough<QuizAttempt, Question, $this>
     */
    public function quizAttempts(): HasManyThrough
    {
        return $this->hasManyThrough(QuizAttempt::class, Question::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCoverUrlAttribute(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return asset('storage/' . ltrim($value, '/'));
    }
}
