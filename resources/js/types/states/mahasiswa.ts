import type {
    Material,
    Question,
    User,
    DifficultyLevel,
    RecentActivity,
    MaterialWithStats,
    LeaderboardEntry,
    QuestionWithAttempt,
    QuizSessionState,
    StudentProfile,
    Certification,
} from '@/types/models';
import type { SharedProps } from './shared';

export interface MahasiswaDashboardProps extends SharedProps {
    totalMaterials: number;
    totalQuestions: number;
    easyQuestions: number;
    mediumQuestions: number;
    hardQuestions: number;
    materialProgressPercentage: number;
    questionProgressPercentage: number;
    completedMaterials: number;
    inProgressMaterials: number;
    totalMaterialProgress: number;
    totalAnsweredQuestions: number;
    totalCorrectQuestions: number;
    recentActivities: RecentActivity[];
    allMaterials: Material[];
    currentUserRank: LeaderboardEntry | null;
    certifications: Record<string, string>;
}

export interface MahasiswaInProgressProps extends SharedProps {
    materialsWithStats: MaterialWithStats[];
}

export interface MahasiswaCompletedProps extends SharedProps {
    materialsWithStats: MaterialWithStats[];
}

export interface MaterialsListProps extends SharedProps {
    materials: Material[];
    isGuest: boolean;
}

export interface QuestionListProps extends SharedProps {
    materials: Material[];
    isGuest: boolean;
}

export interface MaterialShowProps extends SharedProps {
    material: Material;
    isGuest: boolean;
}

export interface QuestionLevelsProps extends SharedProps {
    material: Material;
    levels: LevelStatus[];
    isGuest: boolean;
}

export interface LevelStatus {
    level: number;
    difficulty: DifficultyLevel;
    status: 'locked' | 'available' | 'completed';
    score: number | null;
    attempts: number;
}

export interface QuestionShowProps extends SharedProps {
    material: Material;
    currentQuestion: Question;
    currentQuestionNumber: number;
    totalQuestions: number;
    answeredCount: number;
    difficulty: DifficultyLevel;
    isGuest: boolean;
    studentState: QuizSessionState;
}

export interface QuestionReviewProps extends SharedProps {
    material: Material;
    materials: Material[];
    questions: QuestionWithAttempt[];
    difficulty: DifficultyLevel | 'all';
    isGuest: boolean;
}

export interface ProfileProps extends SharedProps {
    user: User;
    personalization: StudentProfile;
    materials: Material[];
    certifications: Certification[];
}

export interface CertificatesPageProps extends SharedProps {
    certifications: Certification[];
}

export interface LeaderboardProps extends SharedProps {
    leaderboardData: LeaderboardEntry[];
    currentUserRank: LeaderboardEntry | null;
}

export interface UeqCreateProps extends SharedProps {
    hasSubmitted: boolean;
}

export interface SusCreateProps extends SharedProps {
    hasSubmitted: boolean;
}

export interface ProfileForm {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
}

export interface UeqSurveyForm {
    nim: string;
    class: string;
    comments: string;
    suggestions: string;
    [key: string]: unknown;
}

export interface SusSurveyForm {
    nim: string;
    class: string;
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

export interface AnswerPayload {
    question_id: string;
    material_id: string;
    used_hint: boolean;
    time_spent: number;
    difficulty: string;
    fill_in_the_blank_answer?: string;
    answer?: string | null;
    drag_and_drop_answers?: string;
}
