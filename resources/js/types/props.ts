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
    [key: string]: unknown;
}

// =============================================================================
// Admin Domain Props
// =============================================================================

export interface AdminDashboardData {
    totalStudents: number;
    totalMaterials: number;
    totalQuestions: number;
    activeStudents: number;
    recentProgress: RecentProgressItem[];
    studentProgress: StudentProgressItem[];
    popularMaterials: PopularMaterialItem[];
    studentAnalytics: StudentAnalytics;
    materialStats: MaterialStatsItem[];
    studentsNeedingAttention: StudentNeedingAttention[];
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
    allFacts: AdaptiveFact[];
    allActions: AdaptiveAction[];
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
    stats: {
        total_xp: number;
        rank: number;
        completed_materials: number;
        accuracy: number;
    };
    recent_activity: RecentActivity[];
    recommendations: Material[];
}

export interface MaterialShowProps extends SharedProps {
    material: Material;
    is_completed: boolean;
    next_material_id: string | null;
}

export interface QuestionShowProps extends SharedProps {
    material: Material;
    currentQuestion: Question | null;
    currentQuestionNumber: number;
    totalQuestions: number;
    answeredCount: number;
    difficulty: DifficultyLevel;
    isGuest: boolean;
    studentState: StudentSessionState | null;
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
