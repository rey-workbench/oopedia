import type { ContentCategory, LearningStyle, Material, Question } from './index';

export interface SubMaterial {
    id: string;
    material_id: string;
    title: string;
    content: string;
    jenis_konten: ContentCategory;
    learning_style: LearningStyle | null;
    order: number;
    created_at: string;
    updated_at: string;
    material?: Material;
    questions?: Question[];
}
