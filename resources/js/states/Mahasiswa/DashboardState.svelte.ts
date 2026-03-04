import { BaseState } from '@/states/BaseState.svelte';
import type { Material, RecentActivity, LeaderboardEntry } from '@/types';

type DashboardData = {
    totalMaterials: number;
    totalQuestions: number;
    easyQuestions: number;
    mediumQuestions: number;
    hardQuestions: number;
    materialProgressPercentage: number;
    questionProgressPercentage: number;
    completedMaterials: number;
    inProgressMaterials: number;
    totalMaterialProgress: number;
    totalAnsweredQuestions: number;
    totalCorrectQuestions: number;
    recentActivities: RecentActivity[];
    allMaterials: Material[];
    currentUserRank: LeaderboardEntry | null;
};

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

    constructor(data: DashboardData) {
        super();
        this.totalMaterials = data.totalMaterials;
        this.totalQuestions = data.totalQuestions;
        this.easyQuestions = data.easyQuestions;
        this.mediumQuestions = data.mediumQuestions;
        this.hardQuestions = data.hardQuestions;
        this.materialProgressPercentage = data.materialProgressPercentage;
        this.questionProgressPercentage = data.questionProgressPercentage;
        this.completedMaterials = data.completedMaterials;
        this.inProgressMaterials = data.inProgressMaterials;
        this.totalMaterialProgress = data.totalMaterialProgress;
        this.totalAnsweredQuestions = data.totalAnsweredQuestions;
        this.totalCorrectQuestions = data.totalCorrectQuestions;
        this.recentActivities = data.recentActivities;
        this.allMaterials = data.allMaterials;
        this.currentUserRank = data.currentUserRank;
    }
}
