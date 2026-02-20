export class InProgressState {
    materialsWithStats = $state<any[]>([]);

    constructor(materialsWithStats: any) {
        this.materialsWithStats = materialsWithStats;
    }

    calculateProgress(correct: any, total: any) {
        return total > 0 ? Math.round((correct / total) * 100) : 0;
    }
}
