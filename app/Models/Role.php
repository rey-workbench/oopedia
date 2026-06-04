<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\User\RoleName;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Role extends Model
{
    use HasFactory;
    use HasUlids;

    #[\Override]
    public $incrementing = false;

    #[\Override]
    protected $keyType = 'string';

    #[\Override]
    protected $fillable = ['id', 'role_name'];

    #[\Override]
    protected function casts(): array
    {
        return [
            'role_name' => RoleName::class,
        ];
    }
}
