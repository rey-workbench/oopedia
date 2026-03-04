<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Button from '@/components/ui/Button.svelte';
    import EmptyState from '@/components/ui/EmptyState.svelte';
    import Panel from '@/components/ui/Panel.svelte';
    import {
        BookOpen,
        Brain,
        Flame,
        Trophy,
        Star,
        ClipboardList,
        Ghost,
        Code2,
        Plus,
    } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import { untrack } from 'svelte';
    import { DashboardState } from '@/states/Mahasiswa/DashboardState.svelte';
    import type { MahasiswaDashboardProps } from '@/types';

    const {
        totalMaterials = 0,
        totalQuestions = 0,
        easyQuestions = 0,
        mediumQuestions = 0,
        hardQuestions = 0,
        materialProgressPercentage = 0,
        questionProgressPercentage = 0,
        completedMaterials = 0,
        inProgressMaterials = 0,
        totalMaterialProgress = 0,
        totalAnsweredQuestions = 0,
        totalCorrectQuestions = 0,
        recentActivities = [],
        allMaterials = [],
        currentUserRank = null,
    }: Omit<MahasiswaDashboardProps, 'auth' | 'flash' | 'errors'> = $props();

    const state = untrack(
        () =>
            new DashboardState({
                totalMaterials,
                totalQuestions,
                easyQuestions,
                mediumQuestions,
                hardQuestions,
                materialProgressPercentage,
                questionProgressPercentage,
                completedMaterials,
                inProgressMaterials,
                totalMaterialProgress,
                totalAnsweredQuestions,
                totalCorrectQuestions,
                recentActivities,
                allMaterials,
                currentUserRank,
            })
    );

    const dashboardStats = $derived([
        {
            title: 'Materi Tersedia',
            value: state.totalMaterials,
            icon: BookOpen,
            variant: 'primary',
            footer: 'Konsep PBO dari Dasar',
        },
        {
            title: 'Total Soal',
            value: state.totalQuestions,
            icon: Brain,
            variant: 'success',
            footer: 'Latihan & Tantangan',
        },
        {
            title: 'Level Hard',
            value: state.hardQuestions,
            icon: Flame,
            variant: 'danger',
            footer: 'Tingkat Kesulitan Tinggi',
        },
        {
            title: 'Peringkat',
            value: state.currentUserRank ? `#${state.currentUserRank.rank}` : '-',
            icon: Trophy,
            variant: 'warning',
            footer: 'Peringkat global Anda',
        },
    ]);
</script>

<App title="Dashboard">
    <div class="space-y-12">
        <PageHeader title="Dashboard" subtitle="Selamat datang di pusat kendali belajar Anda." />

        <Panel rounded="full" class="shadow-2xl shadow-slate-200" padding="p-12">
            <div class="flex flex-col items-center gap-10 md:flex-row">
                <div class="group relative">
                    <div
                        class="flex h-32 w-32 rotate-3 items-center justify-center overflow-hidden rounded-3xl bg-white shadow-2xl transition-transform duration-500 group-hover:rotate-0"
                    >
                        <Star size={80} class="text-primary-500" />
                    </div>
                </div>

                <div class="text-center md:text-left">
                    <h2
                        class="mb-4 text-4xl font-bold tracking-tight text-white uppercase md:text-5xl"
                    >
                        Siap Belajar, <span class="text-primary-400">{$page.props['auth'].user.name}</span>?
                    </h2>
                    <p class="max-w-xl text-lg font-medium text-slate-400">
                        Lanjutkan perjalanan belajar Anda hari ini dan kuasai konsep
                        <span class="text-white">Object-Oriented Programming</span> dengan cara yang menyenangkan!
                    </p>
                </div>
            </div>
        </Panel>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            {#each dashboardStats as stat (stat.title)}
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
                            {stat.variant === 'success' ? 'bg-emerald-100 text-emerald-600' : 
                             stat.variant === 'danger' ? 'bg-rose-100 text-rose-600' :
                             stat.variant === 'warning' ? 'bg-amber-100 text-amber-600' :
                             'bg-primary-100 text-primary-600'}"
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
                                        : stat.variant === 'danger'
                                        ? 'bg-rose-500'
                                        : stat.variant === 'warning'
                                        ? 'bg-amber-500'
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

        <div class="grid grid-cols-1 gap-12 lg:grid-cols-3">
            <div class="space-y-8 lg:col-span-2">
                <div class="space-y-8">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold tracking-widest text-slate-900 uppercase">
                            Aktivitas Terbaru
                        </h3>
                        <Button variant="ghost" size="sm">Lihat Semua</Button>
                    </div>

                    <!-- Activity Feed Inline -->
                    <div class="space-y-6">
                        {#each state.recentActivities as activity}
                            {@const ActivityIcon =
                                activity.type === 'achievement'
                                    ? Trophy
                                    : activity.type === 'milestone'
                                      ? Star
                                      : ClipboardList}
                            <Card
                                padding="p-0"
                                class="hover:border-primary-400 group overflow-hidden border-slate-100 shadow-xl transition-all"
                            >
                                <div class="flex items-center gap-8 p-8">
                                    <div
                                        class={`flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl border border-black/5 shadow-inner transition-colors
                                    ${activity.type === 'achievement' ? 'bg-emerald-50 text-emerald-500' : activity.type === 'milestone' ? 'bg-amber-50 text-amber-500' : 'bg-primary-50 text-primary-500'}`}
                                    >
                                        <ActivityIcon size={24} strokeWidth={2.5} />
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <h4
                                                    class="text-sm font-bold tracking-widest text-slate-900 uppercase"
                                                >
                                                    {activity.type === 'achievement'
                                                        ? 'Pencapaian Baru!'
                                                        : activity.type === 'milestone'
                                                          ? 'Milestone Tercapai!'
                                                          : 'Progres Belajar'}
                                                </h4>
                                                <p
                                                    class="mt-1 text-sm leading-relaxed font-medium text-slate-500"
                                                >
                                                    {#if activity.type === 'achievement'}
                                                        Menyelesaikan <span
                                                            class="font-bold text-emerald-500"
                                                            >{activity.total_correct}
                                                            soal</span
                                                        >
                                                        di materi
                                                        <span
                                                            class="font-bold text-slate-900 uppercase"
                                                            >{activity.material_title}</span
                                                        >
                                                    {:else if activity.type === 'milestone'}
                                                        Berhasil menyelesaikan soal <span
                                                            class="font-bold text-amber-500"
                                                            >level hard</span
                                                        >
                                                        di materi
                                                        <span
                                                            class="font-bold text-slate-900 uppercase"
                                                            >{activity.material_title}</span
                                                        >
                                                    {:else}
                                                        Mengerjakan soal <span
                                                            class="text-primary-500 font-bold capitalize"
                                                            >{activity.difficulty}</span
                                                        >
                                                        di materi
                                                        <span
                                                            class="font-bold text-slate-900 uppercase"
                                                            >{activity.material_title}</span
                                                        >
                                                    {/if}
                                                </p>
                                            </div>
                                            <span
                                                class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                                >{activity.time_ago || 'Baru saja'}</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </Card>
                        {:else}
                            <EmptyState
                                title="Aktivitas Kosong"
                                description="Belum ada aktivitas tercatat untuk akun ini."
                                icon={Ghost}
                            />
                        {/each}
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                <div class="space-y-8">
                    <h3 class="text-xl font-bold tracking-widest text-slate-900 uppercase">
                        Materi Unggulan
                    </h3>

                    <div class="space-y-6">
                        <Card
                            padding="p-8"
                            class="group relative overflow-hidden border border-slate-100 bg-white shadow-2xl"
                        >
                            <div
                                class="bg-primary-100/50 absolute -top-10 -right-10 h-32 w-32 rounded-full blur-2xl transition-transform duration-1000 group-hover:scale-150"
                            ></div>
                            <div class="text-primary-600 mb-6">
                                <Code2 size={32} strokeWidth={2.5} />
                            </div>
                            <h4
                                class="mb-2 text-lg font-bold tracking-widest text-slate-900 uppercase"
                            >
                                Dasar PBO: Class & Object
                            </h4>
                            <p class="mb-8 text-sm leading-relaxed font-medium text-slate-500">
                                Fundamental utama pemrograman berorientasi objek yang harus
                                dikuasai.
                            </p>
                            <Button
                                variant="primary"
                                size="sm"
                                class="w-full py-4 font-bold tracking-widest uppercase"
                                href={ROUTES.MAHASISWA.MATERIALS.INDEX}>PELAJARI SEKARANG</Button
                            >
                        </Card>

                        <Card
                            padding="p-8"
                            class="hover:border-primary-400 hover:bg-primary-50/20 group cursor-pointer border-2 border-dashed border-slate-200 shadow-none transition-all"
                        >
                            <Link
                                href={ROUTES.MAHASISWA.MATERIALS.INDEX}
                                class="flex flex-col items-center text-center"
                            >
                                <div
                                    class="group-hover:bg-primary-600 mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100/50 text-slate-500 transition-all group-hover:text-white"
                                >
                                    <Plus size={24} strokeWidth={3} />
                                </div>
                                <h4
                                    class="group-hover:text-primary-600 text-[10px] font-bold tracking-widest text-slate-600 uppercase"
                                >
                                    Eksplorasi Katalog Materi
                                </h4>
                                <p
                                    class="text-primary-600 mt-2 text-[9px] font-bold tracking-widest uppercase opacity-0 transition-opacity group-hover:opacity-100"
                                >
                                    LIHAT SEMUA
                                </p>
                            </Link>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </div>
</App>
