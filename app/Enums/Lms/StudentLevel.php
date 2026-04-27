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

    public static function fromXp(int $xp): self
    {
        return match (true) {
            $xp >= 1000 => self::MASTER,
            $xp >= 500  => self::AHLI,
            $xp >= 250  => self::MENENGAH,
            $xp >= 100  => self::JUNIOR,
            default     => self::PEMULA,
        };
    }
}
