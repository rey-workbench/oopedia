<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdaptiveFact extends Model
{
    protected $fillable = ['code', 'name', 'category', 'description'];

    public function rules(): HasMany
    {
        return $this->hasMany(AdaptiveRule::class, 'fact_code', 'code');
    }
}
