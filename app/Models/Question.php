<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $material_id
 * @property string|null $sub_material_id
 * @property string $question_text
 * @property string $question_type
 * @property string $type
 * @property string $difficulty
 * @property string|null $hint
 * @property string|null $created_by
 */
final class Question extends Model
{
    use HasFactory;
    use HasUlids;

    public const string QUESTION_TYPE_FILL_IN_THE_BLANK = 'fill_in_the_blank';

    public const string QUESTION_TYPE_RADIO_BUTTON      = 'radio_button';

    public const string QUESTION_TYPE_DRAG_AND_DROP     = 'drag_and_drop';

    public const string TYPE_TEORI   = 'teori';

    public const string TYPE_SINTAKS = 'sintaks';

    public const string DIFFICULTY_BEGINNER = 'beginner';

    public const string DIFFICULTY_MEDIUM   = 'medium';

    public const string DIFFICULTY_HARD     = 'hard';

    public const string DIFFICULTY_FINAL    = 'final';

    public const int DIFFICULTY_RANK_BEGINNER = 1;

    public const int DIFFICULTY_RANK_MEDIUM   = 2;

    public const int DIFFICULTY_RANK_HARD     = 3;

    public const int DIFFICULTY_RANK_FINAL    = 4;

    public const array DIFFICULTY_ORDER = [
        self::DIFFICULTY_BEGINNER => self::DIFFICULTY_RANK_BEGINNER,
        self::DIFFICULTY_MEDIUM   => self::DIFFICULTY_RANK_MEDIUM,
        self::DIFFICULTY_HARD     => self::DIFFICULTY_RANK_HARD,
        self::DIFFICULTY_FINAL    => self::DIFFICULTY_RANK_FINAL,
    ];

    protected $fillable = [
        'material_id',
        'sub_material_id',
        'question_text',
        'question_type',
        'type',
        'difficulty',
        'hint',
        'created_by',
    ];

    protected $casts = [
        'material_id'     => 'string',
        'sub_material_id' => 'string',
        'created_by'      => 'string',
    ];

    // ==================== RELATIONSHIPS ====================

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function subMaterial(): BelongsTo
    {
        return $this->belongsTo(SubMaterial::class);
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

    // ==================== SCOPES ====================
}
