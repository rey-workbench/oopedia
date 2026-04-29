<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Constants;

/**
 * Standardized keys for rule metadata to prevent typos and ensure
 * consistency between the DB, Service layer, and Admin UI.
 */
final class AdaptiveMetadataKeys
{
    // Notification & Feedback
    public const NOTIFY_TEACHER = 'notify_teacher';

    public const NOTIFY_TYPE = 'notify_type';

    public const SHOW_MOTIVATION = 'show_motivation';

    // Difficulty & Question Control
    public const TARGET_DIFFICULTY = 'target_difficulty';

    public const FORCED_EASY_COUNT = 'forced_easy_count';

    public const DIFFICULTY_STEPS = 'difficulty_steps';

    // Scaffold & Learning Mode
    public const GRADUAL_SCAFFOLD_REDUCTION = 'gradual_scaffold_reduction';

    public const CROSS_TOPIC_CHALLENGE = 'cross_topic_challenge';

    public const CHECK_CERTIFICATION = 'check_certification';

    public const UNLOCK_ADVANCED = 'unlock_advanced';

    // Notification Types (Standard values)
    public const TYPE_CRISIS = 'crisis';

    public const TYPE_CERTIFICATION = 'certification';

    public const TYPE_GENERAL = 'general';
}
