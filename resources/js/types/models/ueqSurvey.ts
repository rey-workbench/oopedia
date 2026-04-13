import type { User } from './user';

export interface UeqSurvey {
    id: string;
    user_id: string;
    nim: string | null;
    class: string | null;
    annoying_enjoyable: number;
    not_understandable_understandable: number;
    creative_dull: number;
    easy_difficult: number;
    valuable_inferior: number;
    boring_exciting: number;
    not_interesting_interesting: number;
    unpredictable_predictable: number;
    fast_slow: number;
    inventive_conventional: number;
    obstructive_supportive: number;
    good_bad: number;
    complicated_easy: number;
    unlikable_pleasing: number;
    usual_leading_edge: number;
    unpleasant_pleasant: number;
    secure_not_secure: number;
    motivating_demotivating: number;
    meets_expectations_does_not_meet: number;
    inefficient_efficient: number;
    clear_confusing: number;
    impractical_practical: number;
    organized_cluttered: number;
    attractive_unattractive: number;
    friendly_unfriendly: number;
    conservative_innovative: number;
    comments: string | null;
    suggestions: string | null;
    created_at: string;
    updated_at: string;
    user?: User;
}
