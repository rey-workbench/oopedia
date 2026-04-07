<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $question_id
 * @property bool $is_correct
 * @property string|null $explanation
 * @property string|null $answer_text
 * @property string|null $drag_source
 * @property string|null $drag_target
 * @property int|null $blank_position
 */
final class Answer extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'question_id',
        'is_correct',
        'explanation',
        'answer_text',
        'drag_source',
        'drag_target',
        'blank_position',
    ];

    protected $casts = [
        'is_correct'     => 'boolean',
        'blank_position' => 'integer',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
