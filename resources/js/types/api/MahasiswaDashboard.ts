import type { Material } from '../models/Material';

/**
 * resources/js/types/api/MahasiswaDashboard.ts
 */

export interface LeaderboardEntry {
    id: string;
    name: string;
    xp: number;
    level: string;
    rank: number;
    badge?: string;
    badge_color?: string;
    percentage?: number;
    formatted_score?: string;
}

export interface RecentActivity {
    id: string;
    type: 'quiz' | 'material' | 'certification' | 'achievement' | 'milestone';
    title: string;
    description: string;
    timestamp: string;
    time_ago?: string;
    total_correct?: number;
    material_title?: string;
    difficulty?: string;
}

export interface MaterialWithStats extends Material {
    stats?: {
        overall: {
            correct: number;
            total: number;
            percentage: number;
        };
        beginner: {
            correct: number;
            total: number;
            configured_total: number;
            percentage: number;
        };
        medium: {
            correct: number;
            total: number;
            configured_total: number;
            percentage: number;
        };
        hard: {
            correct: number;
            total: number;
            configured_total: number;
            percentage: number;
        };
    };
}
