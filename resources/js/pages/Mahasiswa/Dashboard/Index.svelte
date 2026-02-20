<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import DashboardStats from "@/components/Mahasiswa/Dashboard/DashboardStats.svelte";
    import RecentActivityList from "@/components/Mahasiswa/Dashboard/RecentActivityList.svelte";
    import FeaturedMaterials from "@/components/Mahasiswa/Dashboard/FeaturedMaterials.svelte";
    import { DashboardState } from "@/states/Mahasiswa/DashboardState.svelte";

    export let totalMaterials = 0;
    export let totalQuestions = 0;
    export let hardQuestions = 0;
    export let recentActivities = [];

    const state = new DashboardState({
        totalMaterials,
        totalQuestions,
        hardQuestions,
        recentActivities,
    });
</script>

<App title="Dashboard">
    <div class="space-y-12">
        <PageHeader
            title="Dashboard"
            subtitle="Selamat datang di pusat kendali belajar Anda."
        />

        <div
            class="relative overflow-hidden rounded-[3rem] bg-slate-900 p-12 text-white shadow-2xl shadow-slate-200"
        >
            <div
                class="absolute top-0 right-0 w-1/2 h-full bg-primary-600/10"
            ></div>
            <div
                class="absolute -bottom-24 -right-24 w-96 h-96 bg-primary-500/10 rounded-full blur-[100px]"
            ></div>

            <div
                class="relative z-10 flex flex-col md:flex-row items-center gap-10"
            >
                <div
                    class="w-32 h-32 bg-white rounded-[2.5rem] flex items-center justify-center shadow-2xl rotate-3"
                >
                    <img
                        src="/images/logo.png"
                        alt="Oopedia"
                        class="w-20 h-auto"
                    />
                </div>

                <div class="text-center md:text-left">
                    <p
                        class="text-[10px] font-bold uppercase tracking-widest text-primary-400 mb-3"
                    >
                        Selamat Datang Kembali
                    </p>
                    <h2
                        class="text-5xl font-bold tracking-widest mb-4 text-white uppercase"
                    >
                        {state.user.name}
                    </h2>
                    <p class="text-slate-400 font-medium text-lg max-w-xl">
                        Lanjutkan perjalanan belajar Anda hari ini dan kuasai
                        konsep <span class="text-white"
                            >Object-Oriented Programming</span
                        > dengan cara yang menyenangkan!
                    </p>
                </div>
            </div>
        </div>

        <DashboardStats
            totalMaterials={state.totalMaterials}
            totalQuestions={state.totalQuestions}
            hardQuestions={state.hardQuestions}
        />

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div class="lg:col-span-2 space-y-8">
                <RecentActivityList recentActivities={state.recentActivities} />
            </div>

            <div class="space-y-8">
                <FeaturedMaterials />
            </div>
        </div>
    </div>
</App>
