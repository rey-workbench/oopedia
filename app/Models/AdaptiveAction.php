<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $description
 * @property string|null $variant
 * @property array $instructions
 * @property-read Collection|AdaptiveRule[] $rules
 */
class AdaptiveAction extends Model
{
    protected $fillable = ['code', 'name', 'description', 'variant', 'instructions'];

    protected function casts(): array
    {
        return [
            'instructions' => 'array',
        ];
    }

    public function rules(): HasMany
    {
        return $this->hasMany(AdaptiveRule::class, 'action_id');
    }
}
