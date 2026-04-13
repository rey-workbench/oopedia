<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\MslqResultFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class MslqResult extends Model
{
    /** @use HasFactory<MslqResultFactory> */
    use HasFactory;

    use HasUlids;

    protected $fillable = [
        'user_id',
        'nim',
        'class',
        'scores_by_scale',
        'total_motivation',
        'total_strategy',
    ];

    protected $casts = [
        'scores_by_scale'  => 'json',
        'total_motivation' => 'float',
        'total_strategy'   => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(MslqAnswer::class);
    }
}
