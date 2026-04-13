import type { User } from './user';

export type MslqCategory = 'motivation' | 'learning_strategy';

export type MslqScale =
    | 'mslq_intrinsic_goal_orientation'
    | 'mslq_extrinsic_goal_orientation'
    | 'mslq_task_value'
    | 'mslq_control_of_learning_beliefs'
    | 'mslq_self_efficacy_for_learning_performance'
    | 'mslq_test_anxiety'
    | 'mslq_rehearsal'
    | 'mslq_elaboration'
    | 'mslq_organization'
    | 'mslq_critical_thinking'
    | 'mslq_metacognitive_self_regulation'
    | 'mslq_time_study_environment_management'
    | 'mslq_effort_regulation'
    | 'mslq_peer_learning'
    | 'mslq_help_seeking';

export interface MslqQuestion {
    id: string;
    text: string;
    category: MslqCategory;
    scale: MslqScale;
    order: number;
    is_reverse: boolean;
    created_at?: string;
    updated_at?: string;
}

export interface MslqAnswer {
    id: string;
    mslq_result_id: string;
    mslq_question_id: string;
    value: number;
    question: MslqQuestion;
    created_at?: string;
    updated_at?: string;
}

export interface MslqResult {
    id: string;
    user_id: number;
    nim: string;
    class: string;
    scores_by_scale: Partial<Record<MslqScale, number>>;
    total_motivation: number;
    total_strategy: number;
    created_at: string;
    updated_at?: string;
    user: User;
    answers: MslqAnswer[];
}
