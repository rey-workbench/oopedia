import { BaseState } from '@/states/BaseState.svelte';
import type {
    AdminDashboardData,
    RecentProgressItem,
    StudentAnalytics,
    StudentProgressItem,
    PopularMaterialItem,
    MaterialStatsItem,
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

    constructor(data?: AdminDashboardData) {
        super();
        if (data) {
            this.loadData(data);
        }
    }

    loadData(data: AdminDashboardData): void {
        this.totalStudents = data.totalStudents;
        this.totalMaterials = data.totalMaterials;
        this.totalQuestions = data.totalQuestions;
        this.activeStudents = data.activeStudents;
        this.recentProgress = data.recentProgress;
        this.studentProgress = data.studentProgress;
        this.popularMaterials = data.popularMaterials;
        this.studentAnalytics = data.studentAnalytics;
        this.materialStats = data.materialStats;
    }
}
