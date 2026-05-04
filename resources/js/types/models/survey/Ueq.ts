import type { User } from '../User';

/**
 * resources/js/types/models/survey/Ueq.ts
 * Based on 26 Bipolar columns (column1-column26)
 */

export interface UeqSurvey {
    id: string;
    user: User;
    assessment_type: string;
    comments?: string;
    suggestions?: string;
    [key: string]: any;
    created_at: string;
}

export interface UeqSurveyForm {
    assessment_type: string;
    [key: string]: number | string | undefined;
    comments: string;
    suggestions: string;
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
