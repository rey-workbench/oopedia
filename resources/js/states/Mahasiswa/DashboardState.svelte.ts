import { BaseState } from "@/states/BaseState.svelte";

export class DashboardState extends BaseState {
    totalMaterials = $state(0);
    totalQuestions = $state(0);
    hardQuestions = $state(0);
    recentActivities = $state<any[]>([]);

    constructor(data: any) {
        super();
        this.totalMaterials = data.totalMaterials;
        this.totalQuestions = data.totalQuestions;
        this.hardQuestions = data.hardQuestions;
        this.recentActivities = data.recentActivities;
    }
}

