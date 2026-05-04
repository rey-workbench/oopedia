import type { User } from '../User';
import type { Pagination } from '../../core';

/**
 * resources/js/types/models/survey/Mslq.ts
 */

export interface MslqQuestion {
    id: string;
    text: string;
    dimension: string;
    category: 'motivation' | 'learning_strategy';
    order: number;
    scale: string;
    is_reverse: boolean;
}

export interface MslqAnswer {
    question_id: string;
    value: number | null;
}

export interface MslqForm {
    assessment_type: string;
    answers: MslqAnswer[];
}

export interface MslqAnswerDetail {
    question: MslqQuestion;
    value: number;
}

export interface MslqResult {
    id: string;
    user: User;
    assessment_type: string;
    scores_by_scale: Record<string, number>;
    total_motivation: number;
    total_strategy: number;
    answers?: MslqAnswerDetail[];
    created_at: string;
}
export interface AdminMslqIndexProps {
    results: Pagination<MslqResult>;
    metrics: {
        averages: Record<string, number>;
        avg_motivation: number;
        avg_strategy: number;
        total_responses: number;
    };
    types: string[];
    activeType: string;
}

export interface AdminMslqDetailProps {
    result: MslqResult;
}
