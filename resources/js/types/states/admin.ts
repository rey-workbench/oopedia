import type { Material, User, UeqSurvey, SusResult, AdaptiveFact, AdaptiveAction } from '@/types/models';
import type { SharedProps } from './shared';

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
    classes: string[];
    activeClass: string;
}

export interface AdminSusDetailProps extends SharedProps {
    user: User;
    result: SusResult;
    calculation: {
        item_scores: Record<string, number>;
        total_score: number;
    };
}

export interface MaterialStatsItem {
    id: string;
    title: string;
    questions_count: number;
    active_students: number;
    completion_rate: number;
}

export interface RecentProgressItem {
    id: string;
    user: Pick<User, 'id' | 'name' | 'email'>;
    material: Pick<Material, 'id' | 'title'>;
    progress: number;
    updated_at: string;
}

export interface StudentProgressItem {
    user: Pick<User, 'id' | 'name' | 'email'>;
    total_attempts: number;
    correct_count: number;
    accuracy: number;
}

export interface PopularMaterialItem {
    id: string;
    title: string;
    total_attempts: number;
    unique_students: number;
}

export interface StudentAnalytics {
    distribution: Record<string, number>;
    radar: Record<string, number>;
}

export interface AdminDashboardProps extends SharedProps {
    totalStudents: number;
    totalMaterials: number;
    totalQuestions: number;
    activeStudents: number;
    recentProgress: RecentProgressItem[];
    studentProgress: StudentProgressItem[];
    popularMaterials: PopularMaterialItem[];
    studentAnalytics: StudentAnalytics;
    materialStats: MaterialStatsItem[];
}

export interface MaterialWithProgress extends Material {
    progress: number;
}

export interface MissingQuestionsItem {
    material_id: string;
    material_title: string;
    missing_count: number;
}

export interface AdminStudentProgressProps extends SharedProps {
    student: User;
    materials: MaterialWithProgress[];
    missingQuestionsByMaterial: MissingQuestionsItem[];
    certifications: Record<string, string>;
}

export interface AdminUeqIndexProps extends SharedProps {
    surveys: UeqSurvey[];
    averages: Record<string, number>;
    classes: string[];
    activeClass: string;
}

export interface AdminUeqDetailProps extends SharedProps {
    user: User;
    survey: UeqSurvey;
}

export type UeqAverages = Record<string, number>;

export interface AdaptiveRule {
    id: string;
    real_id: number;
    name: string;
    priority: number;
    action: string;
    action_id: number;
    required_facts: string[];
    forbidden_facts: string[];
    is_active: boolean;
}

export interface AdaptiveRuleDomain {
    domain: string;
    count: number;
    rules: AdaptiveRule[];
}

export interface AdaptiveTriggerItem {
    id: number;
    rule_id: string;
    rule_name: string;
    action: string;
    user_name: string;
    material_title: string;
    created_at: string;
}

export interface AdaptiveStateDistribution {
    difficulty: string;
    count: number;
}

export interface AdaptiveRuleTriggerStat {
    rule_id: string;
    rule_name: string;
    trigger_count: number;
    percentage: number;
}

export interface AdminAdaptiveAnalyticsProps extends SharedProps {
    totalRules: number;
    totalFacts: number;
    totalActions: number;
    rulesByDomain: AdaptiveRuleDomain[];
    adaptiveStateDistribution: AdaptiveStateDistribution[];
    recentTriggers: AdaptiveTriggerItem[];
    ruleTriggersStats: AdaptiveRuleTriggerStat[];
    decisionTree: any;
    allFacts: AdaptiveFact[];
    allActions: AdaptiveAction[];
}
