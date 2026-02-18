<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $role_name
 */
class Role extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'role_name'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
