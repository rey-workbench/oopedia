import type { User } from '../User';
import type { SharedProps } from '../../props/Shared';

/**
 * resources/js/types/models/survey/Sus.ts
 */

export interface SusAnswer {
    order: number;
    text: string;
    value: number;
}

export interface SusResult {
    id: string;
    user: User;
    assessment_type: string;
    nim?: string;
    class?: string;
    comments?: string;
    suggestions?: string;
    answers: SusAnswer[];
    total_score: number;
    created_at: string;
}

export interface SusSurveyForm {
    assessment_type: string;
    nim?: string;
    class?: string;
    answers: {
        question_id: string;
        value: number | null;
    }[];
    comments: string;
    suggestions: string;
}

export interface AdminSusIndexProps extends SharedProps {
    results: SusResult[];
    averages: {
        total: number;
        items: Record<string, number>;
    };
    grading: {
        score: number;
        adjective: string;
        grade: string;
        acceptability: string;
    };
    types: string[];
    activeType: string;
}

export interface AdminSusDetailProps extends SharedProps {
    user: User;
    result: SusResult;
    calculation: {
        item_scores: Record<string, number>;
        total_score: number;
    };
}
