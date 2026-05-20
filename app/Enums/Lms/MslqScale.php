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
}
