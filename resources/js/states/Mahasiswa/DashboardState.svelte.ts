import { BaseState } from "@/states/BaseState.svelte";
import type { MahasiswaDashboardProps, RecentActivityItem } from "@/types";

export class DashboardState extends BaseState {
    totalMaterials = $state(0);
    totalQuestions = $state(0);
    hardQuestions = $state(0);
    recentActivities = $state<RecentActivityItem[]>([]);

    constructor(data: MahasiswaDashboardProps) {
        super();
        this.totalMaterials = data.totalMaterials;
        this.totalQuestions = data.totalQuestions;
        this.hardQuestions = data.hardQuestions;
        this.recentActivities = data.recentActivities;
    }
}

