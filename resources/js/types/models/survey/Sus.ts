import type { User } from '../User';
import type { SharedProps } from '../../props/Shared';

/**
 * resources/js/types/models/survey/Sus.ts
 */

export interface SusResult {
    id: string;
    user: User;
    assessment_type: string;
    comments?: string;
    suggestions?: string;
    q1: number;
    q2: number;
    q3: number;
    q4: number;
    q5: number;
    q6: number;
    q7: number;
    q8: number;
    q9: number;
    q10: number;
    score: number;
    total_score: number;
    created_at: string;
}

export interface SusSurveyForm {
    assessment_type: string;
    q1: number;
    q2: number;
    q3: number;
    q4: number;
    q5: number;
    q6: number;
    q7: number;
    q8: number;
    q9: number;
    q10: number;
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
