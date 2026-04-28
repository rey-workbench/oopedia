<?php

declare(strict_types=1);

namespace App\Enums\Lms;

enum StudentLevel: string
{
    case PEMULA   = 'Pemula';
    case MENENGAH = 'Menengah';
    case AHLI     = 'Ahli';
}
