<?php

declare(strict_types=1);

namespace App\Enums\Lms;

enum MediaType: string
{
    case IMAGE = 'image';
    case VIDEO = 'video';
    case FILE  = 'file';

    public function label(): string
    {
        return match ($this) {
            self::IMAGE => 'Gambar',
            self::VIDEO => 'Video',
            self::FILE  => 'File',
        };
    }
}
