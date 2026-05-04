<?php

declare(strict_types=1);

namespace App\Enums\Lms;

enum AssessmentType: string
{
    case PRE_TEST = 'pre';
    case POST_TEST = 'post';
}
