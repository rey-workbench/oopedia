<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Lms\ContentCategory;
use App\Enums\Lms\QuestionDifficulty;
use App\Enums\Lms\QuestionType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Question extends Model
{
    use HasFactory;
    use HasUlids;

    public const int DIFFICULTY_RANK_BEGINNER = 1;

    public const int DIFFICULTY_RANK_MEDIUM   = 2;

    public const int DIFFICULTY_RANK_HARD     = 3;

    public const int DIFFICULTY_RANK_FINAL    = 4;

    public const array DIFFICULTY_ORDER = [
        QuestionDifficulty::BEGINNER->value => self::DIFFICULTY_RANK_BEGINNER,
        QuestionDifficulty::MEDIUM->value   => self::DIFFICULTY_RANK_MEDIUM,
        QuestionDifficulty::HARD->value     => self::DIFFICULTY_RANK_HARD,
        QuestionDifficulty::FINAL->value    => self::DIFFICULTY_RANK_FINAL,
    ];

    #[\Override]
    protected $fillable = [
        'material_id',
        'question_text',
        'question_type',
        'type',
        'difficulty',
        'hint',
        'created_by',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'material_id'     => 'string',
            'created_by'      => 'string',
            'question_type'   => QuestionType::class,
            'type'            => ContentCategory::class,
            'difficulty'      => QuestionDifficulty::class,
        ];
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
