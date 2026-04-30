<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string|null $variant
 * @property array $instructions
 */
class AdaptiveAction extends Model
{
    #[\Override]
    protected $keyType = 'string';

    #[\Override]
    public $incrementing = false;

    #[\Override]
    protected $fillable = ['id', 'name', 'description', 'variant', 'instructions'];

    #[\Override]
    protected function casts(): array
    {
        return [
            'instructions' => 'array',
        ];
    }
}
