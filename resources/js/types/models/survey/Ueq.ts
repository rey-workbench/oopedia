import type { User } from '../User';

/**
 * resources/js/types/models/survey/Ueq.ts
 * Based on 26 Bipolar columns (column1-column26)
 */

export interface UeqSurvey {
    id: string;
    user: User;
    nim?: string;
    class?: string;
    comments?: string;
    suggestions?: string;
    column1: number;
    column2: number;
    column3: number;
    column4: number;
    column5: number;
    column6: number;
    column7: number;
    column8: number;
    column9: number;
    column10: number;
    column11: number;
    column12: number;
    column13: number;
    column14: number;
    column15: number;
    column16: number;
    column17: number;
    column18: number;
    column19: number;
    column20: number;
    column21: number;
    column22: number;
    column23: number;
    column24: number;
    column25: number;
    column26: number;
    created_at: string;
}

export interface UeqSurveyForm {
    nim: string;
    class: string;
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
    classes: string[];
    activeClass: string;
}

export interface AdminUeqDetailProps {
    survey: UeqSurvey;
    user: User;
}
