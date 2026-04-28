import { BaseState } from '@/states/BaseState.svelte';
import type { Material, RecentActivity, LeaderboardEntry, MahasiswaDashboardProps } from '@/types';

export type DashboardStateProps = Omit<MahasiswaDashboardProps, 'auth' | 'flash' | 'errors'>;

export class DashboardState extends BaseState {
    totalMaterials = $state(0);
    totalQuestions = $state(0);
    easyQuestions = $state(0);
    mediumQuestions = $state(0);
    hardQuestions = $state(0);
    materialProgressPercentage = $state(0);
    questionProgressPercentage = $state(0);
    completedMaterials = $state(0);
    inProgressMaterials = $state(0);
    totalMaterialProgress = $state(0);
    totalAnsweredQuestions = $state(0);
    totalCorrectQuestions = $state(0);
    recentActivities = $state<RecentActivity[]>([]);
    allMaterials = $state<Material[]>([]);
    currentUserRank = $state<LeaderboardEntry | null>(null);
    certifications = $state<Record<string, string>>({});

    constructor(data: DashboardStateProps) {
        super();
        this.hydrate(data as any);
        if (!this.certifications) this.certifications = {};
    }
}
