<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdaptiveAction extends Model
{
    protected $fillable = ['code', 'name', 'description', 'instructions'];

    protected $casts = [
        'instructions' => 'array',
    ];

    public function rules(): HasMany
    {
        return $this->hasMany(AdaptiveRule::class, 'h_action_code', 'code');
    }
}
