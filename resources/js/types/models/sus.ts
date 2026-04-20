import type { User } from './user';

export interface SusResult {
    id: string;
    user_id: string;
    nim: string | null;
    class: string | null;
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
    total_score: number;
    comments: string | null;
    suggestions: string | null;
    created_at: string;
    updated_at: string;
    user?: User;
}
