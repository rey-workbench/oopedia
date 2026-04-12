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

    public function label(): string
    {
        return $this->value;
    }

    public function minXp(): int
    {
        return match ($this) {
            self::PEMULA   => 0,
            self::JUNIOR   => 100,
            self::MENENGAH => 250,
            self::AHLI     => 500,
            self::MASTER   => 1000,
        };
    }
}
