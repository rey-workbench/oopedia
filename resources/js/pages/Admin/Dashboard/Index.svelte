<script lang="ts">
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Card from '@/components/ui/Card.svelte';
    import DataTable from '@/components/ui/DataTable.svelte';
    import ProgressBar from '@/components/ui/ProgressBar.svelte';
    import UserAvatar from '@/components/ui/UserAvatar.svelte';
    import EmptyState from '@/components/ui/EmptyState.svelte';
    import { untrack } from 'svelte';
    import { AdminDashboardState } from '@/states/Admin/DashboardState.svelte';
    import type { AdminDashboardData } from '@/types';
    import {
        Users,
        Signal,
        FolderTree,
        Cpu,
        BarChart3,
        Radar,
        Trophy,
        Activity,
        AlertTriangle,
    } from 'lucide-svelte';
    import { formatDate } from '@/utils/formatters';

    let {
        total_students,
        total_materials,
        total_questions,
        active_students,
        recent_progress,
        student_progress,
        popular_materials,
        student_analytics,
        students_needing_attention = [],
        material_stats = [],
    }: AdminDashboardData = $props();

    const state = untrack(
        () =>
            new AdminDashboardState({
                total_students,
                total_materials,
                total_questions,
                active_students,
                recent_progress,
                student_progress,
                popular_materials,
                student_analytics,
                material_stats,
                students_needing_attention,
            })
    );

    const distribution = $derived(state.student_analytics?.distribution ?? {});
    const radar = $derived(state.student_analytics?.radar ?? {});
    const distributionMax = $derived(Math.max(1, ...Object.values(distribution).map(Number)));
    const radarMax = $derived(Math.max(1, ...Object.values(radar).map(Number)));
    const radarColors = ['blue', 'emerald', 'amber', 'rose', 'gray'];
    const materialColumns = [
        { key: 'title', label: 'Materi', align: 'left' },
        { key: 'questions_count', label: 'Total Soal', align: 'center' },
        { key: 'active_students', label: 'Mahasiswa Aktif', align: 'center' },
        {
            key: 'completion_rate',
            label: 'Tingkat Penyelesaian',
            align: 'left',
        },
    ];
    const maxAttempts = $derived(
        Math.max(1, ...(state.popular_materials || []).map((m) => m.total_attempts ?? 0))
    );

    const dashboardStats = $derived([
        {
            title: 'Total Mahasiswa',
            value: state.total_students,
            icon: Users,
            variant: 'primary',
            footer: 'Entitas terdaftar',
        },
        {
            title: 'Node Aktif',
            value: state.active_students,
            icon: Signal,
            variant: 'success',
            footer: 'Sesi aktif hari ini',
        },
        {
            title: 'Pohon Modul',
            value: state.total_materials,
            icon: FolderTree,
            variant: 'warning',
            footer: 'Unit materi belajar',
        },
        {
            title: 'Korpus Evaluasi',
            value: state.total_questions,
            icon: Cpu,
            variant: 'success',
            footer: 'Total butir evaluasi',
        },
    ]);
</script>

<App title="Admin Dashboard">
    <div class="space-y-12">
        <PageHeader
            id="page-header"
            title="Dashboard"
            subtitle="Pusat kendali operasional dan visualisasi data sistem OOPedia."
        />

        {#if students_needing_attention.length > 0}
            <div
                class="relative overflow-hidden rounded-[2rem] bg-linear-to-br from-rose-500 to-rose-700 p-px shadow-lg shadow-rose-500/20"
            >
                <div class="relative h-full w-full rounded-[calc(2rem-1px)] bg-white p-6 sm:p-8">
                    <!-- Background Elements -->
                    <div
                        class="pointer-events-none absolute -top-24 -right-24 h-64 w-64 rounded-full bg-rose-50/50 blur-3xl"
                    ></div>
                    <div
                        class="pointer-events-none absolute top-0 right-0 p-8 text-rose-500 opacity-[0.03]"
                    >
                        <Activity size={120} strokeWidth={1} />
                    </div>

                    <!-- Header -->
                    <div
                        class="relative flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between"
                    >
                        <div class="flex items-start gap-5">
                            <div
                                class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-rose-100 to-rose-50 text-rose-600 shadow-sm ring-4 ring-white"
                            >
                                <Activity size={28} strokeWidth={2.5} class="animate-pulse" />
                            </div>
                            <div class="pt-1">
                                <h3 class="text-xl font-black tracking-tight text-slate-900">
                                    Perhatian Dosen Dibutuhkan
                                </h3>
                                <p
                                    class="mt-1 max-w-xl text-sm leading-relaxed font-medium text-slate-500"
                                >
                                    Sistem adaptif mendeteksi ada <span
                                        class="font-bold text-rose-600"
                                        >{students_needing_attention.length} mahasiswa</span
                                    > yang memerlukan atensi Anda karena berada dalam krisis belajar atau
                                    membutuhkan verifikasi sertifikasi.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Grid -->
                    <div
                        class="relative mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                    >
                        {#each students_needing_attention as student}
                            <div
                                class="group flex flex-col justify-between overflow-hidden rounded-2xl border border-slate-200/60 bg-slate-50/40 p-5 transition-all duration-300 hover:-translate-y-1 hover:border-rose-200 hover:bg-white hover:shadow-xl hover:shadow-rose-100/50"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="rounded-full ring-2 ring-white">
                                        <UserAvatar name={student.name} size="md" />
                                    </div>
                                    <div class="flex min-w-0 flex-1 flex-col">
                                        <span class="truncate text-sm font-bold text-slate-900"
                                            >{student.name}</span
                                        >
                                        <span
                                            class="truncate text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                            >{student.email}</span
                                        >
                                    </div>
                                </div>
                                <div
                                    class="mt-5 flex flex-col gap-3 border-t border-slate-200/60 pt-4"
                                >
                                    <div class="flex items-center gap-2">
                                        {#if student.student_state?.adaptive_state?.['notify_teacher_type'] === 'certification'}
                                            <span
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-100 px-2.5 py-1 text-[9px] font-black tracking-widest text-emerald-700 uppercase"
                                            >
                                                <Trophy size={10} strokeWidth={3} /> Sertifikasi
                                            </span>
                                        {:else}
                                            <span
                                                class="inline-flex animate-pulse items-center gap-1.5 rounded-lg bg-rose-100 px-2.5 py-1 text-[9px] font-black tracking-widest text-rose-700 uppercase"
                                            >
                                                <AlertTriangle size={10} strokeWidth={3} /> Krisis Belajar
                                            </span>
                                        {/if}
                                    </div>
                                    <p
                                        class="line-clamp-2 text-[11px] leading-relaxed font-medium text-slate-500"
                                        title={student.student_state?.adaptive_state?.[
                                            'last_diagnosis'
                                        ] || '-'}
                                    >
                                        <span class="font-bold text-slate-700">Diagnosis:</span>
                                        {student.student_state?.adaptive_state?.[
                                            'last_diagnosis'
                                        ] || 'Menunggu evaluasi diagnostik...'}
                                    </p>
                                </div>
                            </div>
                        {/each}
                    </div>
                </div>
            </div>
        {/if}

        <!-- Main Stats -->
        <div id="admin-stats-overview" class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            {#each dashboardStats as stat}
                <Card hover={true} class="group relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 text-slate-400 opacity-10">
                        {#if typeof stat.icon !== 'string'}
                            {@const IconComponent = stat.icon}
                            <div
                                class="scale-[4] transition-transform duration-500 group-hover:scale-[4.5]"
                            >
                                <IconComponent size={24} strokeWidth={2.5} />
                            </div>
                        {/if}
                    </div>

                    <div class="relative z-10">
                        <div
                            class="glass mb-6 flex h-14 w-14 items-center justify-center rounded-2xl shadow-sm
                            {stat.variant === 'success'
                                ? 'bg-emerald-100 text-emerald-600'
                                : 'bg-primary-100 text-primary-600'}"
                        >
                            {#if typeof stat.icon === 'string'}
                                <i class={stat.icon}></i>
                            {:else}
                                {@const IconComponent = stat.icon}
                                <IconComponent size={24} strokeWidth={2.5} />
                            {/if}
                        </div>

                        <h3
                            class="mb-2 text-[10px] font-bold tracking-wider text-slate-600 uppercase"
                        >
                            {stat.title}
                        </h3>
                        <div
                            class="font-display mb-2 text-4xl font-black tracking-tight text-slate-900"
                        >
                            {stat.value}
                        </div>

                        {#if stat.footer}
                            <div class="flex items-center gap-2">
                                <div
                                    class="h-1.5 w-1.5 rounded-full {stat.variant === 'success'
                                        ? 'bg-emerald-500'
                                        : 'bg-primary-500'}"
                                ></div>
                                <p
                                    class="text-[10px] font-bold tracking-widest text-slate-500 uppercase"
                                >
                                    {stat.footer}
                                </p>
                            </div>
                        {/if}
                    </div>
                </Card>
            {/each}
        </div>

        <!-- Analytics Section -->
        <div id="admin-analytics-charts" class="grid grid-cols-1 gap-6 lg:grid-cols-2">
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
                        <EmptyState
                            title="Tidak Ada Data"
                            description="Data distribusi tidak tersedia."
                        />
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
                        <EmptyState
                            title="Tidak Ada Data"
                            description="Data kompetensi tidak tersedia."
                        />
                    {/if}
                </div>
            </Card>
        </div>

        <div id="admin-activity-overview" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Top Students -->
            <div id="admin-top-students" class="lg:col-span-2">
                <Card hover={false}>
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

                        {#if state.student_progress && state.student_progress.length > 0}
                            <div class="space-y-3">
                                {#each state.student_progress as s, i}
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
                            <EmptyState
                                title="Data Kosong"
                                description="Tidak ada data mahasiswa."
                            />
                        {/if}
                    </div>
                </Card>
            </div>

            <!-- Popular Materials -->
            <div id="admin-popular-materials">
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

                        {#if state.popular_materials && state.popular_materials.length > 0}
                            <div class="space-y-4">
                                {#each state.popular_materials as m}
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
                            <EmptyState title="Data Kosong" description="Belum ada data materi." />
                        {/if}
                    </div>
                </Card>
            </div>
        </div>

        <!-- Material Statistics -->
        <div id="admin-material-stats">
            {#if state.material_stats && state.material_stats.length > 0}
                <DataTable
                    id="material-stats-table"
                    title="Statistik Materi"
                    items={state.material_stats}
                    hideSearch={true}
                    columns={materialColumns}
                    rowClass={() => ''}
                >
                    {#snippet row(m)}
                        <td class="px-6 py-4">
                            <span
                                class="text-xs font-bold tracking-widest text-slate-900 uppercase"
                            >
                                {m.title}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-bold text-slate-700">
                                {m.questions_count}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-bold text-slate-700">
                                {m.active_students}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-1">
                                    <ProgressBar
                                        value={m.completion_rate ?? 0}
                                        max={100}
                                        color={m.completion_rate >= 70
                                            ? 'emerald'
                                            : m.completion_rate >= 40
                                              ? 'amber'
                                              : 'rose'}
                                        height="h-2"
                                    />
                                </div>
                                <span
                                    class="w-12 shrink-0 text-right text-xs font-bold text-slate-700"
                                >
                                    {m.completion_rate ?? 0}%
                                </span>
                            </div>
                        </td>
                    {/snippet}
                </DataTable>
            {/if}
        </div>

        <!-- Recent Activity Timeline -->
        <div id="admin-recent-activity">
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

                    {#if state.recent_progress && state.recent_progress.length > 0}
                        <div class="space-y-4">
                            {#each state.recent_progress as item}
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
                                                {item.updated_at
                                                    ? formatDate(item.updated_at)
                                                    : '-'}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <ProgressBar
                                                value={item.progress ?? 0}
                                                color="blue"
                                                height="h-1.5"
                                            />
                                            <span
                                                class="shrink-0 text-[10px] font-bold text-slate-500"
                                                >{item.progress ?? 0}%</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            {/each}
                        </div>
                    {:else}
                        <EmptyState
                            title="Belum Ada Aktivitas"
                            description="Belum ada aktivitas terbaru."
                        />
                    {/if}
                </div>
            </Card>
        </div>
    </div>
</App>
