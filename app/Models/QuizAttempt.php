<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $question_id
 * @property int|null $answer_id
 * @property string|null $user_response
 * @property bool $is_correct
 * @property int $score
 * @property int $attempt_number
 * @property int|null $time_spent
 */
class QuizAttempt extends Model
{
    use HasFactory;

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

    protected $casts = [
        'is_correct'     => 'boolean',
        'score'          => 'integer',
        'attempt_number' => 'integer',
        'time_spent'     => 'integer',
    ];

    public function setTimeSpent(int $seconds): static
    {
        $this->time_spent = $seconds;

        return $this;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class);
    }
}
