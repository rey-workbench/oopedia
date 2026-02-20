<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Stats from "@/components/Admin/Dashboard/Stats.svelte";
    import Analytics from "@/components/Admin/Dashboard/Analytics.svelte";
    import TopStudents from "@/components/Admin/Dashboard/TopStudents.svelte";
    import PopularMaterials from "@/components/Admin/Dashboard/PopularMaterials.svelte";
    import RecentActivity from "@/components/Admin/Dashboard/RecentActivity.svelte";
    import { AdminDashboardState } from "@/states/Admin/DashboardState.svelte";

    export let totalStudents;
    export let totalMaterials;
    export let totalQuestions;
    export let activeStudents;
    export let recentProgress;
    export let studentProgress;
    export let popularMaterials;
    export let studentAnalytics;

    const state = new AdminDashboardState({
        totalStudents,
        totalMaterials,
        totalQuestions,
        activeStudents,
        recentProgress,
        studentProgress,
        popularMaterials,
        studentAnalytics,
    });
</script>

<App title="Admin Dashboard">
    <div class="space-y-12">
        <PageHeader
            title="Dashboard"
            subtitle="Pusat kendali operasional dan visualisasi data sistem OOPedia."
        />

        <!-- Main Stats -->
        <Stats
            totalStudents={state.totalStudents}
            activeStudents={state.activeStudents}
            totalMaterials={state.totalMaterials}
            totalQuestions={state.totalQuestions}
        />

        <!-- Analytics Section -->
        <Analytics studentAnalytics={state.studentAnalytics} />

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Top Students Table -->
            <TopStudents studentProgress={state.studentProgress} />

            <!-- Popular Materials -->
            <PopularMaterials popularMaterials={state.popularMaterials} />
        </div>

        <!-- Recent Activity Timeline -->
        <RecentActivity recentProgress={state.recentProgress} />
    </div>
</App>
