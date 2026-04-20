<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $user_id
 * @property string|null $nim
 * @property string|null $class
 * @property int $q1
 * @property int $q2
 * @property int $q3
 * @property int $q4
 * @property int $q5
 * @property int $q6
 * @property int $q7
 * @property int $q8
 * @property int $q9
 * @property int $q10
 * @property float $total_score
 * @property string|null $comments
 * @property string|null $suggestions
 */
final class SusResult extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'user_id',
        'nim',
        'class',
        'q1',
        'q2',
        'q3',
        'q4',
        'q5',
        'q6',
        'q7',
        'q8',
        'q9',
        'q10',
        'total_score',
        'comments',
        'suggestions',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_score' => 'float',
            'q1'          => 'integer',
            'q2'          => 'integer',
            'q3'          => 'integer',
            'q4'          => 'integer',
            'q5'          => 'integer',
            'q6'          => 'integer',
            'q7'          => 'integer',
            'q8'          => 'integer',
            'q9'          => 'integer',
            'q10'         => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
