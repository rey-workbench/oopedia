import { BaseState } from '@/states/BaseState.svelte';
import type { LeaderboardEntry } from '@/types';

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
        this.hydrate({ leaderboardData });
    }
}
