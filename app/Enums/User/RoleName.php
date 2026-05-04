<?php

declare(strict_types=1);

namespace App\Enums\User;

enum RoleName: string
{
    case SUPERADMIN = 'superadmin';
    case DOSEN = 'admin';
    case MAHASISWA = 'mahasiswa';
    case GUEST = 'guest';

}
