import { BaseState } from "@/states/BaseState.svelte";

type DashboardData = {
    totalMaterials: number;
    totalQuestions: number;
    hardQuestions: number;
    recentActivities: any[];
};

export class DashboardState extends BaseState {
    totalMaterials = $state(0);
    totalQuestions = $state(0);
    hardQuestions = $state(0);
    recentActivities = $state<any[]>([]);

    constructor(data: DashboardData) {
        super();
        this.totalMaterials = data.totalMaterials;
        this.totalQuestions = data.totalQuestions;
        this.hardQuestions = data.hardQuestions;
        this.recentActivities = data.recentActivities;
    }
}

