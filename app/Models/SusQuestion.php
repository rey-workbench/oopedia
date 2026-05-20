<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

final class SusQuestion extends Model
{
    use HasUlids;

    #[\Override]
    protected $fillable = [
        'order',
        'text',
        'is_reverse',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'is_reverse' => 'boolean',
            'order'      => 'integer',
        ];
    }
}
