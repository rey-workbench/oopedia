import type { CheckAnswerResponse } from '@/types';

export type FeedbackVariant = 'feedback' | 'popup' | 'challenge';

export interface FeedbackState {
    feedbackData?: CheckAnswerResponse | null;
    show_feedback: boolean;
}

export interface CertificateDetails {
    color: string;
    title: string;
    badge: string;
    subtitle: string;
}
