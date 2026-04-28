<?php

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
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['id', 'name', 'description', 'variant', 'instructions'];

    protected function casts(): array
    {
        return [
            'instructions' => 'array',
        ];
    }
}
