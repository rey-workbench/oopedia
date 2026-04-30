<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\User\RoleName;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role_id
 * @property bool $is_approved
 */
final class User extends Authenticatable
{
    use HasFactory;
    use HasUlids;
    use Notifiable;

    public const string ADMIN_EMAIL_DOMAIN = '@admin.oopedia.com';

    #[\Override]
    public $incrementing = false;

    #[\Override]
    protected $keyType = 'string';

    #[\Override]
    protected $fillable = ['name', 'email', 'password', 'role_id', 'is_approved'];

    /** @var array<int, string> */
    #[\Override]
    protected $hidden = ['password', 'remember_token'];

    #[\Override]
    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
            'password'    => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function studentState(): HasOne
    {
        return $this->hasOne(StudentState::class);
    }

    public function hasRole(RoleName|string $role): bool
    {
        $roleName = $role instanceof RoleName ? $role->value : $role;

        $currentRole = $this->role?->role_name;

        if ($currentRole instanceof RoleName) {
            return $currentRole->value === $roleName;
        }

        return (string) $currentRole === $roleName;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(RoleName::SUPERADMIN);
    }

    public function isDosen(): bool
    {
        return $this->hasRole(RoleName::DOSEN);
    }

    public function isMahasiswa(): bool
    {
        return $this->hasRole(RoleName::MAHASISWA);
    }
}
