<?php

declare(strict_types=1);

namespace App\Enums\Lms;

enum QuestionType: string
{
    case RADIO_BUTTON      = 'radio_button';
    case DRAG_AND_DROP     = 'drag_and_drop';
    case FILL_IN_THE_BLANK = 'fill_in_the_blank';

    public function label(): string
    {
        return match ($this) {
            self::RADIO_BUTTON      => 'Pilihan Ganda',
            self::DRAG_AND_DROP     => 'Seret & Lepas',
            self::FILL_IN_THE_BLANK => 'Isian Singkat',
        };
    }
}
