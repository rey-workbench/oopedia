import { BaseState } from "@/states/BaseState.svelte";
import type { AdminDashboardProps, PopularMaterialItem, RecentProgressItem, StudentAnalytics, StudentProgressItem } from "@/types";

export class AdminDashboardState extends BaseState {
    totalStudents = $state(0);
    totalMaterials = $state(0);
    totalQuestions = $state(0);
    activeStudents = $state(0);
    recentProgress = $state<RecentProgressItem[]>([]);
    studentProgress = $state<StudentProgressItem[]>([]);
    popularMaterials = $state<PopularMaterialItem[]>([]);
    studentAnalytics = $state<StudentAnalytics>({ distribution: {}, radar: {} });

    constructor(data: AdminDashboardProps) {
        super();
        this.totalStudents = data.totalStudents;
        this.totalMaterials = data.totalMaterials;
        this.totalQuestions = data.totalQuestions;
        this.activeStudents = data.activeStudents;
        this.recentProgress = data.recentProgress;
        this.studentProgress = data.studentProgress;
        this.popularMaterials = data.popularMaterials;
        this.studentAnalytics = data.studentAnalytics;
    }
}
