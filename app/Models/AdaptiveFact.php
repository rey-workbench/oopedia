<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdaptiveFact extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['id', 'name', 'category', 'logic'];
}
