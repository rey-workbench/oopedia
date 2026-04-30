<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdaptiveFact extends Model
{
    #[\Override]
    protected $keyType = 'string';

    #[\Override]
    public $incrementing = false;

    #[\Override]
    protected $fillable = ['id', 'name', 'category', 'logic'];
}
