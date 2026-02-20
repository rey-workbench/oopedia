import { page } from "@inertiajs/svelte";

export class LeaderboardState {
    leaderboardData = $state<any[]>([]);
    currentUser = $state<any>(null);

    topThree = $derived(this.leaderboardData.slice(0, 3));
    restOfLeaderboard = $derived(this.leaderboardData.slice(3));

    // Determine current user rank safely
    userRank = $derived(
        Array.isArray(this.leaderboardData) && this.currentUser
            ? this.leaderboardData.find((u) => u.id === this.currentUser.id)?.rank
            : null
    );

    constructor(leaderboardData: any, currentUser: any) {
        this.leaderboardData = leaderboardData;
        this.currentUser = currentUser;
    }
}
