import { BaseState } from '@/states/BaseState.svelte';
import type { Material, RecentActivity, LeaderboardEntry, MahasiswaDashboardProps } from '@/types';

export type DashboardStateProps = Omit<MahasiswaDashboardProps, 'auth' | 'flash' | 'errors'>;

export class DashboardState extends BaseState {
    total_materials = $state(0);
    total_questions = $state(0);
    easy_questions = $state(0);
    medium_questions = $state(0);
    hard_questions = $state(0);
    material_progress_percentage = $state(0);
    question_progress_percentage = $state(0);
    completed_materials = $state(0);
    in_progress_materials = $state(0);
    total_material_progress = $state(0);
    total_answered_questions = $state(0);
    total_correct_questions = $state(0);
    recent_activities = $state<RecentActivity[]>([]);
    all_materials = $state<Material[]>([]);
    current_user_rank = $state<LeaderboardEntry | null>(null);
    certifications = $state<Record<string, string>>({});

    constructor(data: DashboardStateProps) {
        super();
        this.hydrate(data as any);
        if (!this.certifications) this.certifications = {};
    }
}
