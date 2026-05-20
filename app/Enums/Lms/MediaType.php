<?php

declare(strict_types=1);

namespace App\Enums\Lms;

enum MediaType: string
{
    case IMAGE = 'image';
    case VIDEO = 'video';
    case FILE  = 'file';
}
