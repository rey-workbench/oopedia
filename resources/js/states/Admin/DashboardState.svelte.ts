export class AdminDashboardState {
    totalStudents = $state(0);
    totalMaterials = $state(0);
    totalQuestions = $state(0);
    activeStudents = $state(0);
    recentProgress = $state([]);
    studentProgress = $state([]);
    popularMaterials = $state([]);
    studentAnalytics = $state({});

    constructor(data: any) {
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
