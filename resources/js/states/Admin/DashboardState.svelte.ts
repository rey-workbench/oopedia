import { BaseState } from '@/states/BaseState.svelte';
import type {
    AdminDashboardData,
    RecentProgressItem,
    StudentAnalytics,
    StudentProgressItem,
    PopularMaterialItem,
    MaterialStatsItem,
    StudentNeedingAttention,
} from '@/types';

export class AdminDashboardState extends BaseState {
    totalStudents = $state(0);
    totalMaterials = $state(0);
    totalQuestions = $state(0);
    activeStudents = $state(0);
    recentProgress = $state<RecentProgressItem[]>([]);
    studentProgress = $state<StudentProgressItem[]>([]);
    popularMaterials = $state<PopularMaterialItem[]>([]);
    studentAnalytics = $state<StudentAnalytics>({ distribution: {}, radar: {} });
    materialStats = $state<MaterialStatsItem[]>([]);
    studentsNeedingAttention = $state<StudentNeedingAttention[]>([]);

    constructor(data?: AdminDashboardData) {
        super();
        this.hydrate(data as any);
    }
}
