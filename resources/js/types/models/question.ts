import type { QuestionType, DifficultyLevel, Answer, ContentCategory } from './index';

export interface Question {
    id: string;
    material_id: string;
    sub_material_id: string | null;
    question_text: string;
    question_type: QuestionType;
    type: ContentCategory;
    difficulty: DifficultyLevel;
    hint: string | null;
    created_by: string | null;
    created_at: string;
    updated_at: string;
    answers?: Answer[];
}
