<?php

namespace App\Rules\Adaptive\Constants;

final class RulePriorities
{
    public const int PRIORITY_CRISIS_SAFETY_MIN = 1;

    public const int PRIORITY_CRISIS_SAFETY_MAX = 10;

    public const int PRIORITY_PROJECT_MIN = 11;

    public const int PRIORITY_PROJECT_MAX = 20;

    public const int PRIORITY_CERTIFICATE_MIN = 21;

    public const int PRIORITY_CERTIFICATE_MAX = 30;

    public const int PRIORITY_PROMOTION_MIN = 31;

    public const int PRIORITY_PROMOTION_MAX = 40;

    public const int PRIORITY_RECOVERY_MIN = 41;

    public const int PRIORITY_RECOVERY_MAX = 50;

    public const int PRIORITY_EDGE_CASE_MIN = 51;

    public const int PRIORITY_EDGE_CASE_MAX = 60;
}
