<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SusAnswer extends Model
{
    use HasUlids;

    #[\Override]
    protected $fillable = [
        'sus_result_id',
        'sus_question_id',
        'value',
    ];

    /**
     * @return BelongsTo<SusResult, $this>
     */
    public function result(): BelongsTo
    {
        return $this->belongsTo(SusResult::class, 'sus_result_id');
    }

    /**
     * @return BelongsTo<SusQuestion, $this>
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(SusQuestion::class, 'sus_question_id');
    }
}
