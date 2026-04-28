<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Constants;

final class ActionConstants
{
    public const string REMEDIAL = 'REMEDIAL';

    public const string REDUCE_DIFF = 'REDUCE_DIFF';

    public const string INCREASE_DIFF = 'INCREASE_DIFF';

    public const string SCAFFOLD_REDUCTION = 'SCAFFOLD_REDUCTION';

    public const string NEW_CHALLENGE = 'NEW_CHALLENGE';

    public const string STREAK_BONUS = 'STREAK_BONUS';

    public const string CERTIFICATION = 'CERTIFICATION';

    public const string FEEDBACK = 'FEEDBACK';

    // Instruction Keys
    public const string KEY_FLOW = 'flow_action';

    public const string FLOW_NEXT = 'next_question';

    public const string FLOW_PREV = 'prev_question';

    public const string FLOW_UP = 'increase_difficulty';

    public const string FLOW_DOWN = 'decrease_difficulty';

    public const string FLOW_REVIEW = 'remedial_review';

    public const string FLOW_FINISH = 'finish_material';

    public const array NAMES = [
        self::REMEDIAL           => 'Remedial Review',
        self::REDUCE_DIFF        => 'Turunkan Kesulitan',
        self::INCREASE_DIFF      => 'Naikkan Kesulitan',
        self::SCAFFOLD_REDUCTION => 'Kurangi Bantuan (Scaffold)',
        self::NEW_CHALLENGE      => 'Tantangan Baru',
        self::STREAK_BONUS       => 'Bonus Streak',
        self::CERTIFICATION      => 'Berikan Sertifikat',
        self::FEEDBACK           => 'Berikan Umpan Balik',
    ];
}
