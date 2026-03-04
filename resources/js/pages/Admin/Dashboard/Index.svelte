<script lang="ts">
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Card from '@/components/ui/Card.svelte';
    import ProgressBar from '@/components/ui/ProgressBar.svelte';
    import UserAvatar from '@/components/ui/UserAvatar.svelte';
    import { untrack } from 'svelte';
    import { AdminDashboardState } from '@/states/Admin/DashboardState.svelte';
    import {
        Users,
        Signal,
        FolderTree,
        Cpu,
        BarChart3,
        Radar,
        Trophy,
        Activity,
    } from 'lucide-svelte';
    import { formatDate } from '@/utils/formatters';

    let {
        totalStudents,
        totalMaterials,
        totalQuestions,
        activeStudents,
        recentProgress,
        studentProgress,
        popularMaterials,
        studentAnalytics,
    }: {
        totalStudents: any;
        totalMaterials: any;
        totalQuestions: any;
        activeStudents: any;
        recentProgress: any;
        studentProgress: any;
        popularMaterials: any;
        studentAnalytics: any;
    } = $props();

    const state = untrack(
        () =>
            new AdminDashboardState({
                totalStudents,
                totalMaterials,
                totalQuestions,
                activeStudents,
                recentProgress,
                studentProgress,
                popularMaterials,
                studentAnalytics,
            } as any)
    );

    const distribution = $derived(state.studentAnalytics?.distribution ?? {});
    const radar = $derived(state.studentAnalytics?.radar ?? {});
    const distributionMax = $derived(Math.max(1, ...Object.values(distribution).map(Number)));
    const radarMax = $derived(Math.max(1, ...Object.values(radar).map(Number)));
    const radarColors = ['blue', 'emerald', 'amber', 'rose', 'gray'];

    const maxAttempts = $derived(
        Math.max(1, ...(state.popularMaterials || []).map((m: any) => m.total_attempts ?? 0))
    );

    const dashboardStats = $derived([
        {
            title: 'Total Mahasiswa',
            value: state.totalStudents,
            icon: Users,
            variant: 'primary',
            footer: 'Entitas terdaftar',
        },
        {
            title: 'Node Aktif',
            value: state.activeStudents,
            icon: Signal,
            variant: 'success',
            footer: 'Sesi aktif hari ini',
        },
        {
            title: 'Modul Instruksional',
            value: state.totalMaterials,
            icon: FolderTree,
            variant: 'primary',
            footer: 'Konten aktif',
        },
        {
            title: 'Korpus Evaluasi',
            value: state.totalQuestions,
            icon: Cpu,
            variant: 'success',
            footer: 'Total butir evaluasi',
        },
    ]);
</script>

<App title="Admin Dashboard">
    <div class="space-y-12">
        <PageHeader
            title="Dashboard"
            subtitle="Pusat kendali operasional dan visualisasi data sistem OOPedia."
        />

        <!-- Main Stats -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            {#each dashboardStats as stat}
                <Card hover={true} class="relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 text-slate-400">
                        {#if typeof stat.icon !== 'string'}
                            {@const IconComponent = stat.icon}
                            <div class="scale-[4] transition-transform duration-500 group-hover:scale-[4.5]">
                                <IconComponent size={24} strokeWidth={2.5} />
                            </div>
                        {/if}
                    </div>

                    <div class="relative z-10">
                        <div
                            class="glass mb-6 flex h-14 w-14 items-center justify-center rounded-2xl shadow-sm
                            {stat.variant === 'success' ? 'bg-emerald-100 text-emerald-600' : 'bg-primary-100 text-primary-600'}"
                        >
                            {#if typeof stat.icon === 'string'}
                                <i class={stat.icon}></i>
                            {:else}
                                {@const IconComponent = stat.icon}
                                <IconComponent size={24} strokeWidth={2.5} />
                            {/if}
                        </div>

                        <h3 class="mb-2 text-[10px] font-bold tracking-wider text-slate-600 uppercase">
                            {stat.title}
                        </h3>
                        <div class="font-display mb-2 text-4xl font-black tracking-tight text-slate-900">
                            {stat.value}
                        </div>

                        {#if stat.footer}
                            <div class="flex items-center gap-2">
                                <div
                                    class="h-1.5 w-1.5 rounded-full {stat.variant === 'success'
                                        ? 'bg-emerald-500'
                                        : 'bg-primary-500'}"
                                ></div>
                                <p class="text-[10px] font-bold tracking-widest text-slate-500 uppercase">
                                    {stat.footer}
                                </p>
                            </div>
                        {/if}
                    </div>
                </Card>
            {/each}
        </div>

        <!-- Analytics Section -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Distribution -->
            <Card hover={false}>
                <div class="space-y-4">
                    <div class="mb-2 flex items-center gap-3">
                        <div
                            class="bg-primary-50 text-primary-600 flex h-9 w-9 items-center justify-center rounded-xl"
                        >
                            <BarChart3 size={18} />
                        </div>
                        <h3 class="text-sm font-bold tracking-widest text-slate-900 uppercase">
                            Distribusi Level
                        </h3>
                    </div>
                    {#if Object.keys(distribution).length > 0}
                        <div class="space-y-3">
                            {#each Object.entries(distribution) as [label, value]}
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between px-0.5">
                                        <span
                                            class="text-[10px] font-bold tracking-widest text-slate-500 uppercase"
                                            >{label}</span
                                        >
                                        <span class="text-[10px] font-bold text-slate-700"
                                            >{value}</span
                                        >
                                    </div>
                                    <ProgressBar
                                        value={Number(value)}
                                        max={distributionMax}
                                        color="blue"
                                        height="h-2"
                                    />
                                </div>
                            {/each}
                        </div>
                    {:else}
                        <p class="text-xs font-medium text-slate-400">
                            Data distribusi tidak tersedia.
                        </p>
                    {/if}
                </div>
            </Card>

            <!-- Radar / Kompetensi -->
            <Card hover={false}>
                <div class="space-y-4">
                    <div class="mb-2 flex items-center gap-3">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"
                        >
                            <Radar size={18} />
                        </div>
                        <h3 class="text-sm font-bold tracking-widest text-slate-900 uppercase">
                            Kompetensi Materi
                        </h3>
                    </div>
                    {#if Object.keys(radar).length > 0}
                        <div class="space-y-3">
                            {#each Object.entries(radar) as [label, value], i}
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between px-0.5">
                                        <span
                                            class="line-clamp-1 max-w-[70%] text-[10px] font-bold tracking-widest text-slate-500 uppercase"
                                            >{label}</span
                                        >
                                        <span class="text-[10px] font-bold text-slate-700"
                                            >{Number(value).toFixed(1)}%</span
                                        >
                                    </div>
                                    <ProgressBar
                                        value={Number(value)}
                                        max={radarMax}
                                        color={(radarColors[i % radarColors.length] ?? 'blue') as
                                            | 'emerald'
                                            | 'amber'
                                            | 'rose'
                                            | 'blue'
                                            | 'gray'}
                                        height="h-2"
                                    />
                                </div>
                            {/each}
                        </div>
                    {:else}
                        <p class="text-xs font-medium text-slate-400">
                            Data kompetensi tidak tersedia.
                        </p>
                    {/if}
                </div>
            </Card>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Top Students -->
            <Card hover={false} class="lg:col-span-2">
                <div class="space-y-4">
                    <div class="mb-2 flex items-center gap-3">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-500"
                        >
                            <Trophy size={18} />
                        </div>
                        <h3 class="text-sm font-bold tracking-widest text-slate-900 uppercase">
                            Top Mahasiswa
                        </h3>
                    </div>

                    {#if state.studentProgress && state.studentProgress.length > 0}
                        <div class="space-y-3">
                            {#each state.studentProgress as s, i}
                                <div class="flex items-center gap-4">
                                    <span
                                        class="w-5 text-center text-[10px] font-bold text-slate-400"
                                        >{i + 1}</span
                                    >
                                    <UserAvatar name={s.user?.name ?? '?'} size="sm" />
                                    <div class="min-w-0 flex-1">
                                        <div class="mb-1 flex items-center justify-between">
                                            <span
                                                class="truncate text-xs font-bold tracking-widest text-slate-900 uppercase"
                                                >{s.user?.name ?? '-'}</span
                                            >
                                            <span
                                                class="ml-2 shrink-0 text-[10px] font-bold text-slate-400"
                                                >{s.accuracy ?? 0}%</span
                                            >
                                        </div>
                                        <ProgressBar
                                            value={s.accuracy ?? 0}
                                            color="amber"
                                            height="h-1.5"
                                        />
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <div class="text-xs font-bold text-slate-700">
                                            {s.correct_count ?? 0}
                                        </div>
                                        <div
                                            class="text-[9px] font-bold tracking-widest text-slate-400 uppercase"
                                        >
                                            Benar
                                        </div>
                                    </div>
                                </div>
                            {/each}
                        </div>
                    {:else}
                        <p class="text-xs font-medium text-slate-400">Tidak ada data mahasiswa.</p>
                    {/if}
                </div>
            </Card>

            <!-- Popular Materials -->
            <Card hover={false}>
                <div class="space-y-4">
                    <div class="mb-2 flex items-center gap-3">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"
                        >
                            <FolderTree size={18} />
                        </div>
                        <h3 class="text-sm font-bold tracking-widest text-slate-900 uppercase">
                            Materi Populer
                        </h3>
                    </div>

                    {#if state.popularMaterials && state.popularMaterials.length > 0}
                        <div class="space-y-4">
                            {#each state.popularMaterials as m}
                                <div class="space-y-1.5">
                                    <div class="flex items-start justify-between">
                                        <span
                                            class="line-clamp-1 max-w-[70%] text-xs font-bold tracking-widest text-slate-900 uppercase"
                                            >{m.title}</span
                                        >
                                        <span
                                            class="ml-2 shrink-0 text-[10px] font-bold text-emerald-600"
                                            >{m.total_attempts ?? 0} percobaan</span
                                        >
                                    </div>
                                    <ProgressBar
                                        value={m.total_attempts ?? 0}
                                        max={maxAttempts}
                                        color="emerald"
                                        height="h-1.5"
                                    />
                                    <div
                                        class="text-[9px] font-bold tracking-widest text-slate-400 uppercase"
                                    >
                                        {m.unique_students ?? 0} mahasiswa unik
                                    </div>
                                </div>
                            {/each}
                        </div>
                    {:else}
                        <p class="text-xs font-medium text-slate-400">Belum ada data materi.</p>
                    {/if}
                </div>
            </Card>
        </div>

        <!-- Recent Activity Timeline -->
        <Card hover={false}>
            <div class="space-y-4">
                <div class="mb-2 flex items-center gap-3">
                    <div
                        class="bg-primary-50 text-primary-600 flex h-9 w-9 items-center justify-center rounded-xl"
                    >
                        <Activity size={18} />
                    </div>
                    <h3 class="text-sm font-bold tracking-widest text-slate-900 uppercase">
                        Aktivitas Terbaru
                    </h3>
                </div>

                {#if state.recentProgress && state.recentProgress.length > 0}
                    <div class="space-y-4">
                        {#each state.recentProgress as item}
                            <div class="flex items-start gap-3">
                                <UserAvatar name={item.user?.name ?? '?'} size="sm" />
                                <div class="min-w-0 flex-1 space-y-1.5">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <span
                                                class="text-xs font-bold tracking-widest text-slate-900 uppercase"
                                                >{item.user?.name ?? '-'}</span
                                            >
                                            <span
                                                class="block text-[9px] font-bold tracking-widest text-slate-400 uppercase"
                                                >{item.material?.title ?? '-'}</span
                                            >
                                        </div>
                                        <span
                                            class="ml-2 shrink-0 text-[10px] font-bold text-slate-400"
                                        >
                                            {item.updated_at ? formatDate(item.updated_at) : '-'}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <ProgressBar
                                            value={item.progress ?? 0}
                                            color="blue"
                                            height="h-1.5"
                                        />
                                        <span class="shrink-0 text-[10px] font-bold text-slate-500"
                                            >{item.progress ?? 0}%</span
                                        >
                                    </div>
                                </div>
                            </div>
                        {/each}
                    </div>
                {:else}
                    <p class="text-xs font-medium text-slate-400">Belum ada aktivitas terbaru.</p>
                {/if}
            </div>
        </Card>
    </div>
</App>
