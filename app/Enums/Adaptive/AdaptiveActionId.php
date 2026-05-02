<?php

declare(strict_types=1);

namespace App\Enums\Adaptive;

/**
 * Standardized Action IDs for the Adaptive Engine.
 * These correspond to the hardcoded logic in the backend.
 * NO METADATA allowed. Each action is atomic.
 */
enum AdaptiveActionId: string
{
    // Learning Actions (Standard)
    case REMEDIAL           = 'REMEDIAL';
    case REMEDIAL_INTENSIVE = 'REMEDIAL_INTENSIVE';
    case REDUCE_DIFF        = 'REDUCE_DIFF';
    case INCREASE_DIFF      = 'INCREASE_DIFF';
    case REDUCE_HINT        = 'REDUCE_HINT';
    case NEW_CHALLENGE      = 'NEW_CHALLENGE';

    // Psychological & Motivational Actions
    case SHOW_GUIDANCE = 'SHOW_GUIDANCE';
    case STREAK_BONUS  = 'STREAK_BONUS';

    // System Actions
    case NOTIFY_TEACHER = 'NOTIFY_TEACHER';
    case CERTIFICATION  = 'CERTIFICATION';
    case FEEDBACK       = 'FEEDBACK';
    case GIVE_HINT      = 'GIVE_HINT';
}
