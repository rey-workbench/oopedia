import { BaseState } from '@/states/BaseState.svelte';
import type { LeaderboardEntry } from '@/types';

export class LeaderboardState extends BaseState {
    leaderboard_data = $state<LeaderboardEntry[]>([]);

    topThree = $derived(this.leaderboard_data.slice(0, 3));
    restOfLeaderboard = $derived(this.leaderboard_data.slice(3));

    // Determine current user rank safely using pattern from BaseState
    userRank = $derived(
        Array.isArray(this.leaderboard_data) && this.user
            ? this.leaderboard_data.find((u) => u.id === this.user!.id)?.rank
            : null
    );

    constructor(leaderboard_data: LeaderboardEntry[]) {
        super();
        this.hydrate({ leaderboard_data });
    }
}
