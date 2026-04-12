<?php

declare(strict_types=1);

namespace App\Enums\Lms;

enum MslqScale: string
{
    case INTRINSIC_GOAL_ORIENTATION             = 'mslq_intrinsic_goal_orientation';
    case EXTRINSIC_GOAL_ORIENTATION             = 'mslq_extrinsic_goal_orientation';
    case TASK_VALUE                             = 'mslq_task_value';
    case CONTROL_OF_LEARNING_BELIEFS            = 'mslq_control_of_learning_beliefs';
    case SELF_EFFICACY_FOR_LEARNING_PERFORMANCE = 'mslq_self_efficacy_for_learning_performance';
    case TEST_ANXIETY                           = 'mslq_test_anxiety';
    case REHEARSAL                              = 'mslq_rehearsal';
    case ELABORATION                            = 'mslq_elaboration';
    case ORGANIZATION                           = 'mslq_organization';
    case CRITICAL_THINKING                      = 'mslq_critical_thinking';
    case METACOGNITIVE_SELF_REGULATION          = 'mslq_metacognitive_self_regulation';
    case TIME_STUDY_ENVIRONMENT_MANAGEMENT      = 'mslq_time_study_environment_management';
    case EFFORT_REGULATION                      = 'mslq_effort_regulation';
    case PEER_LEARNING                          = 'mslq_peer_learning';
    case HELP_SEEKING                           = 'mslq_help_seeking';

    public function label(): string
    {
        return match ($this) {
            self::INTRINSIC_GOAL_ORIENTATION             => 'Intrinsic Goal Orientation',
            self::EXTRINSIC_GOAL_ORIENTATION             => 'Extrinsic Goal Orientation',
            self::TASK_VALUE                             => 'Task Value',
            self::CONTROL_OF_LEARNING_BELIEFS            => 'Control of Learning Beliefs',
            self::SELF_EFFICACY_FOR_LEARNING_PERFORMANCE => 'Self-Efficacy for Learning & Performance',
            self::TEST_ANXIETY                           => 'Test Anxiety',
            self::REHEARSAL                              => 'Rehearsal',
            self::ELABORATION                            => 'Elaboration',
            self::ORGANIZATION                           => 'Organization',
            self::CRITICAL_THINKING                      => 'Critical Thinking',
            self::METACOGNITIVE_SELF_REGULATION          => 'Metacognitive Self-Regulation',
            self::TIME_STUDY_ENVIRONMENT_MANAGEMENT      => 'Time & Study Environment Management',
            self::EFFORT_REGULATION                      => 'Effort Regulation',
            self::PEER_LEARNING                          => 'Peer Learning',
            self::HELP_SEEKING                           => 'Help Seeking',
        };
    }

    public function category(): MslqCategory
    {
        return match ($this) {
            self::INTRINSIC_GOAL_ORIENTATION,
            self::EXTRINSIC_GOAL_ORIENTATION,
            self::TASK_VALUE,
            self::CONTROL_OF_LEARNING_BELIEFS,
            self::SELF_EFFICACY_FOR_LEARNING_PERFORMANCE,
            self::TEST_ANXIETY => MslqCategory::MOTIVATION,

            self::REHEARSAL,
            self::ELABORATION,
            self::ORGANIZATION,
            self::CRITICAL_THINKING,
            self::METACOGNITIVE_SELF_REGULATION,
            self::TIME_STUDY_ENVIRONMENT_MANAGEMENT,
            self::EFFORT_REGULATION,
            self::PEER_LEARNING,
            self::HELP_SEEKING => MslqCategory::LEARNING_STRATEGY,
        };
    }
}
