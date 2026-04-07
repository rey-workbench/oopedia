<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $role_name
 */
final class Role extends Model
{
    use HasFactory;
    use HasUlids;

    public const string ROLE_SUPERADMIN = 'superadmin';

    public const string ROLE_DOSEN      = 'dosen';

    public const string ROLE_MAHASISWA  = 'mahasiswa';

    public const string ROLE_GUEST      = 'guest';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['id', 'role_name'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
