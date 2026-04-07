<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
    public const string ADMIN_EMAIL_DOMAIN = '@admin.oopedia.com';

    use HasFactory;
    use HasUlids;
    use Notifiable;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['name', 'email', 'password', 'role_id', 'is_approved'];

    /** @var array<int, string> */
    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

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

    public function hasRole(string $role): bool
    {
        $roleName = $this->role?->role_name;

        if ($roleName) {
            return $roleName === $role;
        }

        // Fallback for legacy numeric role_id values
        $rid = $this->role_id;

        if (is_numeric($rid)) {
            return match ((int) $rid) {
                1       => $role === Role::ROLE_SUPERADMIN,
                2       => $role === Role::ROLE_DOSEN,
                3       => $role === Role::ROLE_MAHASISWA,
                4       => $role === Role::ROLE_GUEST,
                default => false,
            };
        }

        return false;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(Role::ROLE_SUPERADMIN);
    }

    public function isDosen(): bool
    {
        return $this->hasRole(Role::ROLE_DOSEN);
    }

    public function isMahasiswa(): bool
    {
        return $this->hasRole(Role::ROLE_MAHASISWA);
    }

    public function scopeWhereRole(Builder $query, string $roleName): Builder
    {
        return $query->whereHas('role', function ($q) use ($roleName) {
            $q->where('role_name', $roleName);
        });
    }
}
