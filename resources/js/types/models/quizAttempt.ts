import type { User } from './user';
import type { Question } from './question';
import type { Answer } from './answer';

export interface QuizAttempt {
    id: string;
    user_id: string;
    question_id: string;
    answer_id: string | null;
    user_response: string | null;
    is_correct: boolean;
    score: number;
    attempt_number: number;
    time_spent: number | null;
    created_at: string;
    updated_at: string;
    user?: User;
    question?: Question;
    answer?: Answer;
}
