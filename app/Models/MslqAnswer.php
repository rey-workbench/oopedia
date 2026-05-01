<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\MslqAnswerFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MslqAnswer extends Model
{
    /** @use HasFactory<MslqAnswerFactory> */
    use HasFactory;

    use HasUlids;

    #[\Override]
    protected $fillable = [
        'mslq_result_id',
        'mslq_question_id',
        'value',
    ];

    #[\Override]
    protected $casts = [
        'value' => 'integer',
    ];

    /**
     * @return BelongsTo<MslqResult, $this>
     */
    public function mslqResult(): BelongsTo
    {
        return $this->belongsTo(MslqResult::class);
    }

    /**
     * @return BelongsTo<MslqQuestion, $this>
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(MslqQuestion::class, 'mslq_question_id');
    }
}
