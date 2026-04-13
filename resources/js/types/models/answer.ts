export interface Answer {
    id: string;
    question_id: string;
    is_correct: boolean;
    explanation: string | null;
    answer_text: string | null;
    drag_source: string | null;
    drag_target: string | null;
    blank_position: number | null;
    created_at: string;
    updated_at: string;
}
