<?php

declare(strict_types=1);

namespace App\Enums\Lms;

enum StudentLevel: string
{
    case PEMULA   = 'Pemula';
    case JUNIOR   = 'Junior';
    case MENENGAH = 'Menengah';
    case AHLI     = 'Ahli';
    case MASTER   = 'Master';

}
