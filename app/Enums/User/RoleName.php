<?php

declare(strict_types=1);

namespace App\Enums\User;

enum RoleName: string
{
    case SUPERADMIN = 'superadmin';
    case DOSEN      = 'dosen';
    case MAHASISWA  = 'mahasiswa';
    case GUEST      = 'guest';

    public function label(): string
    {
        return match ($this) {
            self::SUPERADMIN => 'Super Admin',
            self::DOSEN      => 'Dosen',
            self::MAHASISWA  => 'Mahasiswa',
            self::GUEST      => 'Tamu',
        };
    }
}
