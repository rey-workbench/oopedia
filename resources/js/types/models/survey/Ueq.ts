import type { User } from '../User';

/**
 * resources/js/types/models/survey/Ueq.ts
 * Based on 26 Bipolar columns (column1-column26)
 */

export interface UeqAnswer {
    question_id: string;
    value: number | null;
}

export interface UeqSurvey {
    id: string;
    user: User;
    assessment_type: string;
    nim?: string;
    class?: string;
    answers: UeqAnswer[];
    comments?: string;
    suggestions?: string;
    created_at: string;
}

export interface UeqSurveyForm {
    assessment_type: string;
    nim: string;
    class: string;
    answers: UeqAnswer[];
    comments: string;
    suggestions: string;
    errors: Record<string, string>;
    processing: boolean;
}

export interface UeqAverages {
    attractiveness: number;
    perspicuity: number;
    efficiency: number;
    dependability: number;
    stimulation: number;
    novelty: number;
}

export interface AdminUeqIndexProps {
    surveys: UeqSurvey[];
    averages: UeqAverages;
    types: string[];
    activeType: string;
}

export interface AdminUeqDetailProps {
    survey: UeqSurvey;
    user: User;
}
