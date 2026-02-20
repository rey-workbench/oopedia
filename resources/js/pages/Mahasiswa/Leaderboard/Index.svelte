<script>
    import { page } from "@inertiajs/svelte";
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Card from "@/components/ui/Card.svelte";
    import LeaderboardPodium from "@/components/Mahasiswa/Leaderboard/LeaderboardPodium.svelte";
    import LeaderboardTable from "@/components/Mahasiswa/Leaderboard/LeaderboardTable.svelte";
    import { LeaderboardState } from "@/states/Mahasiswa/LeaderboardState.svelte";

    export let leaderboardData = [];

    const state = new LeaderboardState(leaderboardData, $page.props.auth.user);
</script>

<App title="Leaderboard">
    <div class="space-y-12">
        <PageHeader
            title="Leaderboard"
            subtitle="Peringkat Terbaik Mahasiswa Berdasarkan Progres Pembelajaran"
        />

        <div class="space-y-12">
            <Card padding="p-0" hover={false} class="overflow-hidden">
                <LeaderboardPodium top3={state.topThree} />
                <LeaderboardTable
                    leaderboardData={state.leaderboardData}
                    authUserId={state.currentUser.id}
                />
            </Card>
        </div>
    </div>
</App>
