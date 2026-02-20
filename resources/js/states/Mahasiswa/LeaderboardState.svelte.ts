import { BaseState } from "@/states/BaseState.svelte";

export class LeaderboardState extends BaseState {
    leaderboardData = $state<any[]>([]);

    topThree = $derived(this.leaderboardData.slice(0, 3));
    restOfLeaderboard = $derived(this.leaderboardData.slice(3));

    // Determine current user rank safely using pattern from BaseState
    userRank = $derived(
        Array.isArray(this.leaderboardData) && this.user
            ? this.leaderboardData.find((u) => u.id === this.user.id)?.rank
            : null
    );

    constructor(leaderboardData: any) {
        super();
        this.leaderboardData = leaderboardData;
    }
}
