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
        Trophy,
        Star,
        ClipboardList,
        Ghost,
        Code2,
        Plus,
        Rocket,
        CheckCircle2,
    } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import { untrack } from 'svelte';
    import { DashboardState } from '@/states/Mahasiswa/DashboardState.svelte';
    import type { MahasiswaDashboardProps } from '@/types';

    const {
        total_materials = 0,
        total_questions = 0,
        easy_questions = 0,
        medium_questions = 0,
        hard_questions = 0,
        material_progress_percentage = 0,
        question_progress_percentage = 0,
        completed_materials = 0,
        in_progress_materials = 0,
        total_material_progress = 0,
        total_answered = 0,
        total_correct_questions = 0,
        recent_activities = [],
        all_materials = [],
        current_user_rank = null,
        certifications = {},
    }: MahasiswaDashboardProps = $props();

    const state = untrack(
        () =>
            new DashboardState({
                total_materials,
                total_questions,
                easy_questions,
                medium_questions,
                hard_questions,
                material_progress_percentage,
                question_progress_percentage,
                completed_materials,
                in_progress_materials,
                total_material_progress,
                total_answered,
                total_correct_questions,
                recent_activities,
                all_materials,
                current_user_rank,
                certifications,
            })
    );

    const dashboardStats = $derived([
        {
            id: 'stat-total-materials',
            title: 'Materi Tersedia',
            value: state.total_materials,
            icon: BookOpen,
            variant: 'primary',
            footer: 'Konsep PBO dari Dasar',
            href: ROUTES.MAHASISWA.MATERIALS.INDEX,
        },
        {
            id: 'stat-inprogress-materials',
            title: 'Sedang Dipelajari',
            value: state.in_progress_materials,
            icon: Rocket,
            variant: 'info',
            footer: 'Lanjutkan Progresmu',
            href: ROUTES.MAHASISWA.IN_PROGRESS,
        },
        {
            id: 'stat-completed-materials',
            title: 'Materi Selesai',
            value: state.completed_materials,
            icon: CheckCircle2,
            variant: 'success',
            footer: 'Hall of Fame Materi',
            href: ROUTES.MAHASISWA.COMPLETED,
        },
        {
            id: 'stat-global-rank',
            title: 'Peringkat',
            value: state.current_user_rank ? `#${state.current_user_rank.rank}` : '-',
            icon: Trophy,
            variant: 'warning',
            footer: 'Peringkat global Anda',
            href: ROUTES.MAHASISWA.LEADERBOARD,
        },
    ]);
</script>

<App title="Dashboard">
    <div class="space-y-8">
        <PageHeader
            id="page-header"
            title="Dashboard"
            subtitle="Selamat datang di pusat kendali belajar Anda."
        />

        <Panel id="dashboard-hero" rounded="3xl" class="border-b-6 border-slate-700" padding="p-8 md:p-10">
            <div class="flex flex-col items-center gap-10 md:flex-row">
                <div class="group relative">
                    <div
                        class="group-hover:border-primary-400 flex h-24 w-24 items-center justify-center overflow-hidden rounded-3xl border-2 border-b-6 border-slate-200 bg-white shadow-xl shadow-slate-200/20 transition-all duration-300 group-hover:bg-slate-50"
                    >
                        <Star size={60} class="text-primary-500" />
                    </div>
                </div>

                <div class="text-center md:text-left">
                    <h2
                        class="mb-3 text-3xl font-bold tracking-tight text-white uppercase md:text-4xl"
                    >
                        Siap Belajar, <span class="text-primary-400"
                            >{page.props['auth'].user.name}</span
                        >?
                    </h2>
                    <p class="max-w-xl text-base font-medium text-slate-400">
                        Lanjutkan perjalanan belajar Anda hari ini dan kuasai konsep
                        <span class="text-white">Object-Oriented Programming</span> dengan cara yang menyenangkan!
                    </p>
                </div>
            </div>
        </Panel>

        {#if Object.keys(state.certifications).length > 0}
            <div id="dashboard-certificates" class="mb-12">
                <div class="mb-6 flex items-center justify-between">
                    <h3 class="text-xl font-bold tracking-widest text-slate-900 uppercase">
                        Sertifikat Saya
                    </h3>
                    <a
                        href={ROUTES.MAHASISWA.CERTIFICATES.INDEX}
                        class="text-primary-600 text-[10px] font-black tracking-widest uppercase hover:underline"
                        >Lihat Semua →</a
                    >
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    {#each Object.entries(state.certifications) as [materialId, type]}
                        {@const material = state.all_materials.find((m) => m.id === materialId)}
                        <a
                            href={ROUTES.MAHASISWA.CERTIFICATES.INDEX}
                            class="group press-active border-duo-lg relative block overflow-hidden rounded-2xl border-2 transition-all {type ===
                            'gold'
                                ? 'border-amber-400 bg-amber-50/10'
                                : type === 'silver'
                                  ? 'border-slate-300 bg-slate-50/10'
                                  : 'border-orange-300 bg-orange-50/10'}"
                        >
                            <div class="flex items-center gap-6 p-4">
                                <div
                                    class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl border-2 border-b-4 {type ===
                                    'gold'
                                        ? 'border-amber-200 bg-amber-100 text-amber-600'
                                        : type === 'silver'
                                          ? 'border-slate-300 bg-slate-200 text-slate-600'
                                          : 'border-orange-200 bg-orange-100 text-orange-600'}"
                                >
                                    <Trophy size={40} strokeWidth={2.5} />
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="text-[10px] font-black tracking-widest uppercase {type ===
                                            'gold'
                                                ? 'text-amber-600'
                                                : type === 'silver'
                                                  ? 'text-slate-500'
                                                  : 'text-orange-600'}"
                                        >
                                            CERTIFIED {String(type).toUpperCase()} ARCHITECT
                                        </span>
                                    </div>
                                    <h4
                                        class="mt-1 text-xl leading-none font-black text-slate-900 uppercase"
                                    >
                                        {material?.title || 'Object-Oriented Project'}
                                    </h4>
                                    <p
                                        class="group-hover:text-primary-600 mt-2 text-xs font-bold text-slate-500 transition-colors"
                                    >
                                        Klik untuk lihat &amp; unduh →
                                    </p>
                                </div>
                            </div>
                            <div class="absolute -right-8 -bottom-8 rotate-12 opacity-10">
                                <Trophy size={120} />
                            </div>
                        </a>
                    {/each}
                </div>
            </div>
        {/if}

        <div
            id="student-progress-overview"
            class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4"
        >
            {#each dashboardStats as stat (stat.title)}
                <div id={stat.id}>
                    <Link href={stat.href} class="block h-full">
                        <Card
                            class="group press-active border-duo-lg relative h-full overflow-hidden transition-all select-none"
                        >
                            <div class="absolute top-0 right-0 p-4 text-slate-400 opacity-10">
                                {#if typeof stat.icon !== 'string'}
                                    {@const IconComponent = stat.icon}
                                    <div
                                        class="scale-[4] opacity-5 transition-opacity duration-300 group-hover:opacity-10"
                                    >
                                        <IconComponent size={24} strokeWidth={2.5} />
                                    </div>
                                {/if}
                            </div>

                            <div class="relative z-10">
                                <div
                                    class="glass mb-6 flex h-14 w-14 items-center justify-center rounded-2xl border-2 border-b-4
                                    {stat.variant === 'success'
                                        ? 'border-emerald-200 bg-emerald-100 text-emerald-600'
                                        : stat.variant === 'danger'
                                          ? 'border-rose-200 bg-rose-100 text-rose-600'
                                          : stat.variant === 'warning'
                                            ? 'border-amber-200 bg-amber-100 text-amber-600'
                                            : stat.variant === 'info'
                                              ? 'border-indigo-200 bg-indigo-100 text-indigo-600'
                                              : 'bg-primary-100 text-primary-600 border-primary-200'}"
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
                                    class="font-display mb-1 text-3xl font-black tracking-tight text-slate-900"
                                >
                                    {stat.value}
                                </div>

                                {#if stat.footer}
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="h-1.5 w-1.5 rounded-full {stat.variant ===
                                            'success'
                                                ? 'bg-emerald-500'
                                                : stat.variant === 'danger'
                                                  ? 'bg-rose-500'
                                                  : stat.variant === 'warning'
                                                    ? 'bg-amber-500'
                                                    : stat.variant === 'info'
                                                      ? 'bg-indigo-500'
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
                    </Link>
                </div>
            {/each}
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div id="activity-feed" class="space-y-6 lg:col-span-2">
                <div class="space-y-8">
                    <div id="activity-feed-header" class="flex items-center justify-between">
                        <h3 class="text-xl font-bold tracking-widest text-slate-900 uppercase">
                            Aktivitas Terbaru
                        </h3>
                        <Button id="btn-view-all-activities" variant="ghost" size="sm"
                            >Lihat Semua</Button
                        >
                    </div>

                    <!-- Activity Feed Inline -->
                    <div class="space-y-6">
                        {#each state.recent_activities as activity}
                            {@const ActivityIcon =
                                activity.type === 'achievement'
                                    ? Trophy
                                    : activity.type === 'milestone'
                                      ? Star
                                      : ClipboardList}
                            <Card
                                padding="p-0"
                                class="hover:border-primary-400 group overflow-hidden border-b-6 transition-all"
                            >
                                <div class="flex items-center gap-6 p-6">
                                    <div
                                        class={`flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border-2 border-b-4 transition-colors
                                    ${activity.type === 'achievement' ? 'border-emerald-100 bg-emerald-50 text-emerald-500' : activity.type === 'milestone' ? 'border-amber-100 bg-amber-50 text-amber-500' : 'bg-primary-50 text-primary-500 border-primary-100'}`}
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

            <div id="active-materials-list" class="space-y-8">
                <div id="featured-materials-header" class="space-y-8">
                    <h3 class="text-xl font-bold tracking-widest text-slate-900 uppercase">
                        Materi Unggulan
                    </h3>

                    <div class="space-y-6">
                        <Card
                            padding="p-6 md:p-8"
                            class="group relative overflow-hidden border-b-6 bg-white"
                        >
                            <div
                                class="bg-primary-100/50 absolute -top-10 -right-10 h-32 w-32 rounded-full blur-2xl"
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
                                id="btn-learn-now"
                                variant="primary"
                                size="sm"
                                class="w-full py-4 font-bold tracking-widest uppercase"
                                href={ROUTES.MAHASISWA.MATERIALS.INDEX}>PELAJARI SEKARANG</Button
                            >
                        </Card>

                        <Card
                            padding="p-8"
                            class="group press-active hover:border-primary-400 hover:bg-primary-50/20 border-duo-lg cursor-pointer border-2 border-dashed border-slate-200 transition-all"
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
