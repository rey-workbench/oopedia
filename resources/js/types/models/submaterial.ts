import type { JenisKonten, LearningStyle, Material, Question } from './index';

export interface SubMaterial {
    id: string;
    material_id: string;
    title: string;
    content: string;
    jenis_konten: JenisKonten;
    learning_style: LearningStyle | null;
    order: number;
    created_at: string;
    updated_at: string;
    material?: Material;
    questions?: Question[];
}
