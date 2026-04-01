import type { SubMaterial, Question, Media, User } from './index';

export interface Material {
    id: string;
    title: string;
    content: string | null;
    module_id: string | null;
    is_final_project: boolean;
    created_by: string | null;
    created_at: string;
    updated_at: string;
    sub_materials?: SubMaterial[];
    questions?: Question[];
    media?: Media[];
    creator?: User;
}
