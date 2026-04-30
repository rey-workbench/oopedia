import type { AuthData, FlashMessages } from './auth';
import type { 
    Material, 
    Question, 
    DifficultyLevel, 
    Certification
} from './learning';
import type { 
    StudentProfile, 
    RecentActivity,
    StudentSessionState,
    StudentNeedingAttention
} from './analytics';
import type { User } from './auth';
import type { AdaptiveRule, AdaptiveFact, AdaptiveAction } from './adaptive';
import type { MslqQuestion, SusResult } from './survey';

// =============================================================================
// Shared Inertia Props
// =============================================================================

export interface SharedProps {
    auth: AuthData;
    flash: FlashMessages;
    errors: Record<string, string>;
    [key: string]: unknown;
}

// =============================================================================
// Admin Domain Props
// =============================================================================

export interface AdminDashboardData {
    total_students: number;
    total_materials: number;
    total_questions: number;
    active_students: number;
    recent_progress: RecentProgressItem[];
    student_progress: StudentProgressItem[];
    popular_materials: PopularMaterialItem[];
    student_analytics: StudentAnalytics;
    material_stats: MaterialStatsItem[];
    students_needing_attention: StudentNeedingAttention[];
}

export interface AdminDashboardProps extends AdminDashboardData, SharedProps {}

export interface RecentProgressItem {
    user: { name: string };
    material: { title: string };
    progress: number;
    updated_at: string;
}

export interface StudentProgressItem {
    user: { name: string };
    accuracy: number;
    correct_count: number;
}

export interface PopularMaterialItem {
    title: string;
    total_attempts: number;
    unique_students: number;
}

export interface StudentAnalytics {
    distribution: Record<string, number>;
    radar: Record<string, number>;
}


export interface MaterialStatsItem {
    title: string;
    questions_count: number;
    active_students: number;
    completion_rate: number;
}
export interface AdminAdaptiveRuleProps extends SharedProps {
    all_facts: AdaptiveFact[];
    all_actions: AdaptiveAction[];
    rule?: AdaptiveRule;
}

export interface MslqSurveyProps extends SharedProps {
    questions: MslqQuestion[];
    is_completed: boolean;
}

// =============================================================================
// Mahasiswa Domain Props
// =============================================================================

export interface MahasiswaDashboardProps extends SharedProps {
    total_materials: number;
    total_questions: number;
    easy_questions: number;
    medium_questions: number;
    hard_questions: number;
    material_progress_percentage: number;
    question_progress_percentage: number;
    completed_materials: number;
    in_progress_materials: number;
    total_material_progress: number;
    total_answered_questions: number;
    total_correct_questions: number;
    recent_activities: RecentActivity[];
    all_materials: Material[];
    current_user_rank: any | null;
    certifications: Record<string, string>;
}

export interface MaterialShowProps extends SharedProps {
    material: Material;
    is_completed: boolean;
    next_material_id: string | null;
}

export interface QuestionShowProps extends SharedProps {
    material: Material;
    current_question: Question | null;
    current_question_number: number | string;
    total_questions: number;
    answered_count: number;
    difficulty: DifficultyLevel;
    is_guest: boolean;
    student_state: StudentSessionState | null;
}

export interface ProfileProps extends SharedProps {
    user: any;
    personalization: StudentProfile;
    certifications: Certification[];
}

export interface AdminSusIndexProps extends SharedProps {
    results: SusResult[];
    averages: { total: number; items: Record<string, number> };
    grading: { score: number; adjective: string; grade: string; acceptability: string };
    classes: string[];
    activeClass: string;
}

export interface AdminSusDetailProps extends SharedProps {
    user: User;
    result: SusResult;
    calculation: { item_scores: Record<string, number>; total_score: number };
}

export interface CertificatesPageProps extends SharedProps {
    certifications: Certification[];
}
