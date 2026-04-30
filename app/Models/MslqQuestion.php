<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Lms\MslqCategory;
use App\Enums\Lms\MslqScale;
use Database\Factories\MslqQuestionFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class MslqQuestion extends Model
{
    /** @use HasFactory<MslqQuestionFactory> */
    use HasFactory;

    use HasUlids;

    #[\Override]
    protected $fillable = [
        'text',
        'category',
        'scale',
        'is_reverse',
        'order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return [
            'category'   => MslqCategory::class,
            'scale'      => MslqScale::class,
            'is_reverse' => 'boolean',
            'order'      => 'integer',
        ];
    }
}
