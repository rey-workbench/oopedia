<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class QuizAttempt extends Model
{
    use HasFactory;
    use HasUlids;

    #[\Override]
    protected $fillable = [
        'user_id',
        'question_id',
        'answer_id',
        'user_response',
        'is_correct',
        'score',
        'attempt_number',
        'time_spent',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'is_correct'     => 'boolean',
            'score'          => 'integer',
            'attempt_number' => 'integer',
            'time_spent'     => 'integer',
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
     * @return BelongsTo<Question, $this>
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * @return BelongsTo<Answer, $this>
     */
    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class);
    }
}
