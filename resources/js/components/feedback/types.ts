import type { QuestionShowState } from '@/states/Mahasiswa/QuizState.svelte';

export type FeedbackVariant =
    | 'certificate'
    | 'acceleration'
    | 'intervention'
    | 'backtrack'
    | 'success'
    | 'error';

export interface FeedbackState {
    feedbackData?: {
        status: 'success' | 'wrong' | 'error';
        message: string;
        adaptiveResult?: {
            triggered_rule?: {
                id?: string;
                name?: string;
                action?: string | null;
                priority?: number;
            } | null;
            global_xp_earned?: number;
            streak_bonus?: string | null;
            new_state?: {
                next_action?: string | null;
                next_action_data?: {
                    label?: string;
                    url?: string;
                    type?: string;
                } | null;
                recommendation?: string | null;
                certification?: string | null;
                intervention_type?: string | null;
                recovery_type?: string | null;
                fast_track_active?: boolean;
                message?: string | null;
            } | null;
        };
    };
    showFeedback: boolean;
}

export interface FeedbackModalProps {
    state: QuestionShowState;
    variant: FeedbackVariant;
    xpEarned: number;
    streakBonus: string | null;
    recommendation: string | null;
    certification: string | null;
    nextAction: string;
    nextActionType: string;
}

export interface CertificateDetails {
    color: string;
    title: string;
    badge: string;
    subtitle: string;
}
