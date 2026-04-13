import type { MediaType } from './primitives';

export interface Media {
    id: string;
    material_id: string;
    media_type: MediaType;
    media_url: string;
    created_at: string;
    updated_at: string;
    full_url?: string;
}
