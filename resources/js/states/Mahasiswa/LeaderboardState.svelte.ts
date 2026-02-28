import { BaseState } from "@/states/BaseState.svelte";

interface LeaderboardEntry {
    id: number;
    rank: number;
    name: string;
    global_xp: number;
    current_level: string;
    [key: string]: unknown;
}

export class LeaderboardState extends BaseState {
    leaderboardData = $state<LeaderboardEntry[]>([]);

    topThree = $derived(this.leaderboardData.slice(0, 3));
    restOfLeaderboard = $derived(this.leaderboardData.slice(3));

    // Determine current user rank safely using pattern from BaseState
    userRank = $derived(
        Array.isArray(this.leaderboardData) && this.user
            ? this.leaderboardData.find((u) => u.id === this.user!.id)?.rank
            : null
    );

    constructor(leaderboardData: LeaderboardEntry[]) {
        super();
        this.leaderboardData = leaderboardData;
    }
}
