<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Card from '@/components/ui/Card.svelte';
    import ProgressBar from '@/components/ui/ProgressBar.svelte';
    import UserAvatar from '@/components/ui/UserAvatar.svelte';
    import { ArrowLeft, Target, ClipboardList, TrendingUp, Info } from 'lucide-svelte';
    import { formatDate } from '@/utils/formatters';
    import { ROUTES } from '@/utils/route';
    import type { MslqResult } from '@/types';

    let { result }: { result: MslqResult } = $props();

    const scaleLabels: Record<string, string> = {
        mslq_intrinsic_goal_orientation: 'Intrinsic Goal Orientation',
        mslq_extrinsic_goal_orientation: 'Extrinsic Goal Orientation',
        mslq_task_value: 'Task Value',
        mslq_control_of_learning_beliefs: 'Control of Learning Beliefs',
        mslq_self_efficacy_for_learning_performance: 'Self-Efficacy for Learning & Performance',
        mslq_test_anxiety: 'Test Anxiety',
        mslq_rehearsal: 'Rehearsal',
        mslq_elaboration: 'Elaboration',
        mslq_organization: 'Organization',
        mslq_critical_thinking: 'Critical Thinking',
        mslq_metacognitive_self_regulation: 'Metacognitive Self-Regulation',
        mslq_time_study_environment_management: 'Time & Study Environment Management',
        mslq_effort_regulation: 'Effort Regulation',
        mslq_peer_learning: 'Peer Learning',
        mslq_help_seeking: 'Help Seeking',
    };

    const motivationScales = Object.keys(scaleLabels).slice(
        0,
        6
    ) as (keyof MslqResult['scores_by_scale'])[];
    const strategyScales = Object.keys(scaleLabels).slice(
        6
    ) as (keyof MslqResult['scores_by_scale'])[];
</script>

<App title={`Detail MSLQ - ${result.user.name}`}>
    <div class="space-y-12 pb-20">
        <div class="mb-8">
            <h1
                class="text-primary-500 font-display text-3xl font-black tracking-widest uppercase md:text-4xl"
            >
                Detail Analisis <span class="text-accent-500">MSLQ</span>
            </h1>
            <div class="mt-3 flex items-center gap-2">
                <div class="bg-accent-500 h-2 w-16 rounded-full"></div>
                <div class="h-2 w-4 rounded-full bg-slate-200"></div>
            </div>
            <div class="mt-6">
                <Button
                    href={ROUTES.ADMIN.MSLQ.INDEX}
                    variant="ghost"
                    icon={ArrowLeft}
                    size="sm"
                    class="hover:text-primary-500 font-black text-slate-400"
                    >KEMBALI KE DAFTAR</Button
                >
            </div>
        </div>

        <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
            <!-- Sidebar: Profile & Summaries -->
            <div class="space-y-8">
                <div
                    class="bg-primary-500 relative overflow-hidden rounded-3xl border-b-8 border-black p-8 text-white shadow-2xl"
                >
                    <div
                        class="bg-accent-500/20 absolute -top-10 -right-10 h-40 w-40 rounded-full blur-3xl"
                    ></div>
                    <div class="relative z-10 flex flex-col items-center gap-6 text-center">
                        <UserAvatar
                            name={result.user.name}
                            size="lg"
                            dark={true}
                            class="border-4 border-white/10 ring-4 ring-black/5"
                        />
                        <div>
                            <p
                                class="text-accent-400 text-[10px] font-black tracking-[0.2em] uppercase"
                            >
                                Mahasiswa
                            </p>
                            <h3 class="font-display text-xl font-black tracking-widest uppercase">
                                {result.user.name}
                            </h3>
                            <p
                                class="mt-1 rounded-full bg-white/5 px-3 py-1 text-xs font-bold tracking-wider text-slate-400"
                            >
                                {result.nim} | {result.class}
                            </p>
                        </div>
                    </div>
                    <div class="mt-8 grid grid-cols-2 gap-4 border-t border-white/5 pt-8">
                        <div class="text-center">
                            <div
                                class="text-[10px] font-black tracking-widest text-slate-500 uppercase"
                            >
                                Motivasi
                            </div>
                            <div class="text-accent-400 text-3xl font-black">
                                {result.total_motivation}
                            </div>
                        </div>
                        <div class="text-center">
                            <div
                                class="text-[10px] font-black tracking-widest text-slate-500 uppercase"
                            >
                                Strategi
                            </div>
                            <div class="text-3xl font-black text-emerald-400">
                                {result.total_strategy}
                            </div>
                        </div>
                    </div>
                </div>

                <Card class="border-duo rounded-[2rem] border-slate-100 p-8 shadow-xl">
                    <div class="mb-6 flex items-center gap-3">
                        <TrendingUp size={18} class="text-accent-500" />
                        <h4
                            class="text-primary-500 font-display text-sm font-black tracking-widest uppercase"
                        >
                            Waktu Submit
                        </h4>
                    </div>
                    <p class="text-xs font-black tracking-widest text-slate-400 uppercase">
                        {formatDate(result.created_at)}
                    </p>
                </Card>
            </div>

            <!-- Main Content: Scale Breakdowns -->
            <div class="space-y-10 lg:col-span-2">
                <!-- Motivation Breakdown -->
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div
                            class="bg-accent-500 shadow-accent-100 border-accent-700 rounded-xl border-b-4 p-2.5 text-white shadow-lg"
                        >
                            <Target size={20} />
                        </div>
                        <h3
                            class="text-primary-500 font-display text-lg font-black tracking-widest uppercase"
                        >
                            Breakdown: Motivasi
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        {#each motivationScales as scale}
                            <div
                                class="group hover:border-accent-200 space-y-3 rounded-[2rem] border-2 border-transparent bg-slate-50 p-6 transition-all hover:bg-white hover:shadow-xl"
                            >
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-[10px] font-black tracking-widest text-slate-500 uppercase"
                                    >
                                        {scaleLabels[scale]}
                                    </span>
                                    <span class="text-accent-500 text-sm font-black"
                                        >{result.scores_by_scale[scale] ?? 0}</span
                                    >
                                </div>
                                <ProgressBar
                                    value={((result.scores_by_scale[scale] ?? 0) / 7) * 100}
                                    color="accent"
                                    height="h-2"
                                />
                            </div>
                        {/each}
                    </div>
                </div>

                <!-- Learning Strategies Breakdown -->
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div
                            class="bg-primary-500 rounded-xl border-b-4 border-black p-2.5 text-white shadow-lg shadow-slate-200"
                        >
                            <ClipboardList size={20} />
                        </div>
                        <h3
                            class="text-primary-500 font-display text-lg font-black tracking-widest uppercase"
                        >
                            Breakdown: Strategi Belajar
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        {#each strategyScales as scale}
                            <div
                                class="group space-y-3 rounded-[2rem] border-2 border-transparent bg-slate-50 p-6 transition-all hover:border-slate-200 hover:bg-white hover:shadow-xl"
                            >
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-[10px] font-black tracking-widest text-slate-500 uppercase"
                                    >
                                        {scaleLabels[scale]}
                                    </span>
                                    <span class="text-primary-500 text-sm font-black"
                                        >{result.scores_by_scale[scale] ?? 0}</span
                                    >
                                </div>
                                <ProgressBar
                                    value={((result.scores_by_scale[scale] ?? 0) / 7) * 100}
                                    color="blue"
                                    height="h-2"
                                />
                            </div>
                        {/each}
                    </div>
                </div>

                <!-- Footer Info -->
                <div
                    class="border-accent-100 bg-accent-50 flex gap-6 rounded-3xl border-2 p-8 shadow-inner"
                >
                    <div class="h-fit rounded-2xl bg-white p-3 shadow-sm">
                        <Info class="text-accent-500 shrink-0" size={24} />
                    </div>
                    <p
                        class="text-accent-700 text-xs leading-relaxed font-bold tracking-wide uppercase"
                    >
                        Hasil survey ini memberikan gambaran tentang orientasi tujuan belajar
                        mahasiswa, nilai tugas, keyakinan kontrol belajar, efikasi diri, serta
                        kecemasan dalam ujian. Selain itu, juga mengukur strategi kognitif dan
                        metakognitif yang digunakan.
                    </p>
                </div>
            </div>
        </div>

        <!-- Answers List -->
        <Card
            class="border-duo overflow-hidden rounded-[3rem] border-slate-100 shadow-xl"
            padding="p-0"
        >
            <div class="border-b-2 border-slate-50 p-8">
                <h3
                    class="text-primary-500 font-display text-lg font-black tracking-widest uppercase"
                >
                    Detail Jawaban Item
                </h3>
            </div>
            <div class="divide-y-2 divide-slate-50">
                {#each result.answers as answer}
                    <div
                        class="group flex items-center justify-between p-8 transition-colors hover:bg-slate-50/50"
                    >
                        <div class="flex items-center gap-8">
                            <div
                                class="group-hover:bg-accent-100 group-hover:text-accent-600 flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-[10px] font-black text-slate-400 transition-all group-hover:scale-110"
                            >
                                {(answer as any).question.order}
                            </div>
                            <div class="space-y-2">
                                <p
                                    class="group-hover:text-primary-500 text-sm font-bold text-slate-700 transition-colors"
                                >
                                    {(answer as any).question.text}
                                </p>
                                <div class="flex gap-2">
                                    <span
                                        class="rounded-full border border-slate-100 bg-slate-50 px-3 py-1 text-[9px] font-black tracking-widest text-slate-400 uppercase"
                                    >
                                        {(answer as any).question.scale.split('_').join(' ')}
                                    </span>
                                    {#if (answer as any).question.is_reverse}
                                        <span
                                            class="text-accent-400 bg-accent-50 border-accent-100 rounded-full border px-3 py-1 text-[9px] font-black tracking-widest uppercase"
                                            >Reverse</span
                                        >
                                    {/if}
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-6">
                            <div
                                class="text-primary-500 group-hover:text-accent-500 text-3xl font-black transition-colors"
                            >
                                {answer.value}
                            </div>
                            <div
                                class="group-hover:bg-accent-200 h-10 w-1.5 rounded-full bg-slate-100 transition-colors"
                            ></div>
                        </div>
                    </div>
                {/each}
            </div>
        </Card>
    </div>
</App>
