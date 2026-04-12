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
        'mslq_intrinsic_goal_orientation': 'Intrinsic Goal Orientation',
        'mslq_extrinsic_goal_orientation': 'Extrinsic Goal Orientation',
        'mslq_task_value': 'Task Value',
        'mslq_control_of_learning_beliefs': 'Control of Learning Beliefs',
        'mslq_self_efficacy_for_learning_performance': 'Self-Efficacy for Learning & Performance',
        'mslq_test_anxiety': 'Test Anxiety',
        'mslq_rehearsal': 'Rehearsal',
        'mslq_elaboration': 'Elaboration',
        'mslq_organization': 'Organization',
        'mslq_critical_thinking': 'Critical Thinking',
        'mslq_metacognitive_self_regulation': 'Metacognitive Self-Regulation',
        'mslq_time_study_environment_management': 'Time & Study Environment Management',
        'mslq_effort_regulation': 'Effort Regulation',
        'mslq_peer_learning': 'Peer Learning',
        'mslq_help_seeking': 'Help Seeking'
    };

    const motivationScales = Object.keys(scaleLabels).slice(0, 6);
    const strategyScales = Object.keys(scaleLabels).slice(6);
</script>

<App title={`Detail MSLQ - ${result.user.name}`}>
    <div class="space-y-12 pb-20">
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl uppercase">
                Detail Analisis MSLQ
            </h1>
            <div class="mt-3 flex items-center gap-2">
                <div class="bg-indigo-600 h-1.5 w-12 rounded-full"></div>
                <div class="h-1.5 w-4 rounded-full bg-slate-200"></div>
            </div>
            <div class="mt-6">
                <Button href={ROUTES.ADMIN.MSLQ.INDEX} variant="ghost" icon={ArrowLeft}>KEMBALI KE DAFTAR</Button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
            <!-- Sidebar: Profile & Summaries -->
            <div class="space-y-8">
                <div class="relative overflow-hidden rounded-3xl bg-slate-900 p-8 text-white shadow-2xl">
                    <div class="absolute -top-10 -right-10 h-40 w-40 rounded-full bg-indigo-500/20 blur-3xl"></div>
                    <div class="relative z-10 flex flex-col items-center gap-6 text-center">
                        <UserAvatar name={result.user.name} size="lg" dark={true} class="border-2 border-white/10" />
                        <div>
                            <p class="text-[10px] font-bold tracking-widest text-indigo-400 uppercase">Mahasiswa</p>
                            <h3 class="text-xl font-bold uppercase tracking-widest">{result.user.name}</h3>
                            <p class="mt-1 text-xs text-slate-400">{result.nim} | {result.class}</p>
                        </div>
                    </div>
                    <div class="mt-8 grid grid-cols-2 gap-4 border-t border-white/5 pt-8">
                        <div class="text-center">
                            <div class="text-[10px] font-bold text-slate-500 uppercase">Motivasi</div>
                            <div class="text-2xl font-black text-indigo-400">{result.total_motivation}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-[10px] font-bold text-slate-500 uppercase">Strategi</div>
                            <div class="text-2xl font-black text-emerald-400">{result.total_strategy}</div>
                        </div>
                    </div>
                </div>

                <Card class="rounded-3xl border-slate-100 p-8 shadow-xl">
                    <div class="mb-6 flex items-center gap-3">
                        <TrendingUp size={18} class="text-indigo-600" />
                        <h4 class="text-sm font-bold tracking-widest text-slate-900 uppercase">Waktu Submit</h4>
                    </div>
                    <p class="text-xs font-medium text-slate-500">{formatDate(result.created_at)}</p>
                </Card>
            </div>

            <!-- Main Content: Scale Breakdowns -->
            <div class="space-y-10 lg:col-span-2">
                <!-- Motivation Breakdown -->
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-indigo-600 p-2 rounded-xl text-white shadow-lg">
                            <Target size={20} />
                        </div>
                        <h3 class="text-lg font-bold tracking-widest text-slate-900 uppercase">Breakdown: Motivasi</h3>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        {#each motivationScales as scale}
                            <div class="space-y-3 rounded-3xl bg-slate-50 p-6 transition-all hover:bg-slate-100">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold tracking-wide text-slate-600 uppercase">
                                        {scaleLabels[scale]}
                                    </span>
                                    <span class="text-sm font-black text-indigo-600">{result.scores_by_scale[scale] ?? 0}</span>
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

                <!-- Learning Strategies Breakdown -->
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-emerald-600 p-2 rounded-xl text-white shadow-lg">
                            <ClipboardList size={20} />
                        </div>
                        <h3 class="text-lg font-bold tracking-widest text-slate-900 uppercase">Breakdown: Strategi Belajar</h3>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        {#each strategyScales as scale}
                            <div class="space-y-3 rounded-3xl bg-slate-50 p-6 transition-all hover:bg-slate-100">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold tracking-wide text-slate-600 uppercase">
                                        {scaleLabels[scale]}
                                    </span>
                                    <span class="text-sm font-black text-emerald-600">{result.scores_by_scale[scale] ?? 0}</span>
                                </div>
                                <ProgressBar 
                                    value={((result.scores_by_scale[scale] ?? 0) / 7) * 100} 
                                    color="emerald" 
                                    height="h-2" 
                                />
                            </div>
                        {/each}
                    </div>
                </div>

                <!-- Footer Info -->
                <div class="rounded-[2rem] border border-indigo-100 bg-indigo-50 p-6 flex gap-4">
                    <Info class="text-indigo-600 shrink-0" size={20} />
                    <p class="text-xs leading-relaxed text-indigo-700 font-medium">
                        Hasil survey ini memberikan gambaran tentang orientasi tujuan belajar mahasiswa, nilai tugas, keyakinan kontrol belajar, efikasi diri, serta kecemasan dalam ujian. Selain itu, juga mengukur strategi kognitif dan metakognitif yang digunakan.
                    </p>
                </div>
            </div>
        </div>

        <!-- Answers List -->
        <Card class="overflow-hidden rounded-[3rem] border-slate-100 shadow-xl" padding="p-0">
            <div class="border-b border-slate-50 p-8">
                <h3 class="text-lg font-bold tracking-widest text-slate-900 uppercase">Detail Jawaban Item</h3>
            </div>
            <div class="divide-y divide-slate-50">
                {#each result.answers as answer}
                    <div class="group flex items-center justify-between p-6 transition-colors hover:bg-slate-50/50">
                        <div class="flex gap-6 items-center">
                            <div class="h-10 w-10 flex shrink-0 items-center justify-center rounded-xl bg-slate-100 text-[10px] font-black text-slate-400 group-hover:bg-indigo-100 group-hover:text-indigo-600 transition-colors">
                                {answer.question.order}
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm font-bold text-slate-700">{answer.question.text}</p>
                                <div class="flex gap-2">
                                    <span class="text-[9px] font-bold tracking-wider text-slate-400 uppercase bg-slate-50 px-2 py-0.5 rounded-full">
                                        {answer.question.scale.split('_').join(' ')}
                                    </span>
                                    {#if answer.question.is_reverse}
                                        <span class="text-[9px] font-bold tracking-wider text-rose-400 uppercase bg-rose-50 px-2 py-0.5 rounded-full">Reverse</span>
                                    {/if}
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-2xl font-black text-indigo-600">{answer.value}</div>
                            <div class="h-8 w-1 bg-slate-100 rounded-full group-hover:bg-indigo-100 transition-colors"></div>
                        </div>
                    </div>
                {/each}
            </div>
        </Card>
    </div>
</App>
