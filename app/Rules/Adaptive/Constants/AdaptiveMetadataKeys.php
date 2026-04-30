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
    public const string NOTIFY_TEACHER = 'notify_teacher';

    public const string NOTIFY_TYPE = 'notify_type';

    public const string SHOW_MOTIVATION = 'show_motivation';

    // Difficulty & Question Control
    public const string TARGET_DIFFICULTY = 'target_difficulty';

    public const string FORCED_EASY_COUNT = 'forced_easy_count';

    public const string DIFFICULTY_STEPS = 'difficulty_steps';

    // Scaffold & Learning Mode
    public const string GRADUAL_SCAFFOLD_REDUCTION = 'gradual_scaffold_reduction';

    public const string CROSS_TOPIC_CHALLENGE = 'cross_topic_challenge';

    public const string CHECK_CERTIFICATION = 'check_certification';

    public const string UNLOCK_ADVANCED = 'unlock_advanced';

    // Notification Types (Standard values)
    public const string TYPE_CRISIS = 'crisis';

    public const string TYPE_CERTIFICATION = 'certification';

    public const string TYPE_GENERAL = 'general';
}
