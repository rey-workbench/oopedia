<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected function casts(): array
    {
        return [
            'is_correct'     => 'boolean',
            'blank_position' => 'integer',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
