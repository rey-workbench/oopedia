import { BaseState } from '@/states/BaseState.svelte';
import type { LeaderboardEntry } from '@/types';

export class LeaderboardState extends BaseState {
    leaderboard_data = $state<LeaderboardEntry[]>([]);

    topThree = $derived(this.getTopPerformers(3));
    restOfLeaderboard = $derived(this.getPerformersAfter(3));
    currentUserEntry = $derived(this.getCurrentUserEntry());
    userRank = $derived(this.currentUserEntry?.rank ?? null);

    constructor(leaderboard_data: LeaderboardEntry[]) {
        super();
        this.hydrate({ leaderboard_data });
    }

    private getTopPerformers(count: number): LeaderboardEntry[] {
        return this.leaderboard_data.slice(0, count);
    }

    private getPerformersAfter(count: number): LeaderboardEntry[] {
        return this.leaderboard_data.slice(count);
    }

    private getCurrentUserEntry(): LeaderboardEntry | undefined {
        if (!this.user || !Array.isArray(this.leaderboard_data)) return undefined;
        return this.leaderboard_data.find((entry) => entry.id === this.user!.id);
    }
}
