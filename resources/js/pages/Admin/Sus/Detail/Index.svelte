<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Button from '@/components/ui/Button.svelte';
    import UserAvatar from '@/components/ui/UserAvatar.svelte';
    import { ArrowLeft, Info, MessageSquare, Target } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import { SusDetailState } from '@/states/Admin/SusState.svelte';
    import { formatDate } from '@/utils/formatters';
    import { ROUTES } from '@/utils/route';
    import type { AdminSusDetailProps } from '@/types';

    let { user, result, calculation }: AdminSusDetailProps = $props();

    const state = untrack(() => new SusDetailState(user, result, calculation));

    const getScoreVariant = (score: number) => {
        if (score >= 4) return 'bg-emerald-100 text-emerald-700';
        if (score >= 3) return 'bg-amber-100 text-amber-700';
        return 'bg-rose-100 text-rose-700';
    };
</script>

<App title={`Detail SUS - ${state.targetUser.name}`}>
    <div class="space-y-12 pb-20">
        <div class="mb-8">
            <h1
                class="text-primary-500 font-display text-3xl font-black tracking-widest uppercase md:text-4xl"
            >
                Detail Analisis <span class="text-accent-500">SUS</span>
            </h1>
            <div class="mt-3 flex items-center gap-2">
                <div class="bg-accent-500 h-2 w-16 rounded-full"></div>
                <div class="h-2 w-4 rounded-full bg-slate-200"></div>
            </div>
            <div class="mt-6 flex flex-wrap items-center justify-between gap-6">
                <Button
                    href={ROUTES.ADMIN.SUS.INDEX}
                    variant="ghost"
                    icon={ArrowLeft}
                    size="sm"
                    class="hover:text-primary-500 font-black text-slate-400"
                    >KEMBALI KE DAFTAR</Button
                >

                <div
                    class="flex items-center gap-6 rounded-3xl bg-white p-4 shadow-xl shadow-slate-200/50"
                >
                    <UserAvatar name={state.targetUser.name} size="lg" />
                    <div>
                        <div class="text-sm font-bold tracking-widest text-slate-900 uppercase">
                            {state.targetUser.name}
                        </div>
                        <div
                            class="mt-0.5 text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                        >
                            {state.result.nim || '-'} • {state.result.class || '-'}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-12 lg:grid-cols-3">
            <!-- Score Breakdown -->
            <div class="space-y-8 lg:col-span-2">
                <Card
                    padding="p-0"
                    class="overflow-hidden rounded-[3rem] border-slate-100 shadow-xl"
                >
                    <div class="border-b border-slate-50 bg-slate-50/50 px-10 py-8">
                        <div class="flex items-center gap-4">
                            <div
                                class="bg-primary-600 shadow-primary-100 flex h-10 w-10 items-center justify-center rounded-xl text-white shadow-lg"
                            >
                                <Target size={20} />
                            </div>
                            <h3 class="text-xl font-bold tracking-widest text-slate-900 uppercase">
                                Input Instrumen
                            </h3>
                        </div>
                    </div>
                    <div class="divide-y divide-slate-50 px-10 py-6">
                        {#each state.questions as question}
                            <div class="group flex items-center justify-between py-6">
                                <div class="flex items-start gap-6">
                                    <span
                                        class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-[10px] font-black text-slate-400"
                                    >
                                        {question.id}
                                    </span>
                                    <p
                                        class="max-w-md text-sm leading-relaxed font-bold text-slate-600 transition-colors group-hover:text-slate-900"
                                    >
                                        {question.text}
                                    </p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="text-right">
                                        <div
                                            class="text-[10px] font-bold tracking-widest text-slate-300 uppercase"
                                        >
                                            Original
                                        </div>
                                        <div class="text-lg font-black text-slate-900">
                                            {state.result[
                                                `q${question.id}` as keyof typeof state.result
                                            ]}
                                        </div>
                                    </div>
                                    <div class="h-8 w-[2px] bg-slate-50"></div>
                                    <div class="text-center">
                                        <div
                                            class="text-[10px] font-bold tracking-widest text-slate-300 uppercase"
                                        >
                                            Score
                                        </div>
                                        <div
                                            class={`inline-flex h-10 w-10 items-center justify-center rounded-xl text-sm font-black ${getScoreVariant(state.calculation.item_scores[`q${question.id}`] ?? 0)}`}
                                        >
                                            {state.calculation.item_scores[`q${question.id}`] ??
                                                '-'}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/each}
                    </div>
                </Card>

                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    <Card class="rounded-3xl border-emerald-100 bg-emerald-50/20">
                        <div class="mb-4 flex items-center gap-3 text-emerald-600">
                            <MessageSquare size={18} />
                            <h4 class="text-[10px] font-bold tracking-[0.2em] uppercase">
                                Komentar Subjektif
                            </h4>
                        </div>
                        <p class="text-sm leading-relaxed font-semibold text-slate-700 italic">
                            "{state.result.comments || 'Tidak ada komentar.'}"
                        </p>
                    </Card>

                    <Card class="rounded-3xl border-primary-100 bg-primary-50/20">
                        <div class="mb-4 flex items-center gap-3 text-primary-600">
                            <Info size={18} />
                            <h4 class="text-[10px] font-bold tracking-[0.2em] uppercase">
                                Saran Optimasi
                            </h4>
                        </div>
                        <p class="text-sm leading-relaxed font-semibold text-slate-700 italic">
                            "{state.result.suggestions || 'Tidak ada saran.'}"
                        </p>
                    </Card>
                </div>
            </div>

            <!-- Summary Sidebar -->
            <div class="space-y-8">
                <Card class="rounded-[3rem] border-none bg-slate-900 p-10 text-white shadow-2xl">
                    <div class="mb-10 text-center">
                        <h4
                            class="mb-2 text-[10px] font-bold tracking-[0.3em] text-slate-400 uppercase"
                        >
                            SUS Final Score
                        </h4>
                        <div class="text-7xl font-black tracking-tighter text-white">
                            {state.result.total_score}
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="rounded-2xl bg-white/5 p-6 backdrop-blur-md">
                            <p
                                class="mb-1 text-[9px] font-bold tracking-widest text-slate-500 uppercase"
                            >
                                Kontribusi Skor
                            </p>
                            <div class="flex items-end justify-between">
                                <div class="text-lg font-bold">Standard SUS</div>
                                <div class="text-primary-400 font-display text-xl font-bold">
                                    100%
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 border-t border-white/10 pt-4">
                            <div
                                class="flex items-center justify-between text-[10px] font-bold tracking-widest text-slate-500 uppercase"
                            >
                                <span>Item Ganjil (U)</span>
                                <span class="text-emerald-400">Sum(X-1)</span>
                            </div>
                            <div
                                class="flex items-center justify-between text-[10px] font-bold tracking-widest text-slate-500 uppercase"
                            >
                                <span>Item Genap (L)</span>
                                <span class="text-emerald-400">Sum(5-X)</span>
                            </div>
                            <div
                                class="bg-primary-600/20 text-primary-300 mt-4 flex items-center gap-4 rounded-xl p-4"
                            >
                                <Info size={16} class="shrink-0" />
                                <p class="text-[9px] leading-relaxed font-bold uppercase">
                                    Final Score = (U + L) * 2.5
                                </p>
                            </div>
                        </div>
                    </div>
                </Card>

                <Card class="rounded-3xl border-slate-100 shadow-lg">
                    <div class="space-y-6">
                        <div>
                            <div
                                class="mb-1 text-[9px] font-bold tracking-widest text-slate-400 uppercase"
                            >
                                Waktu Pengisian
                            </div>
                            <div class="font-bold text-slate-900">
                                {formatDate(state.result.created_at)}
                            </div>
                        </div>
                        <div class="h-px bg-slate-50"></div>
                        <div>
                            <div
                                class="mb-1 text-[9px] font-bold tracking-widest text-slate-400 uppercase"
                            >
                                Status Validasi
                            </div>
                            <div
                                class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-[9px] font-black text-emerald-700 uppercase"
                            >
                                <div class="h-1 w-1 rounded-full bg-emerald-500"></div>
                                Verified Submission
                            </div>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
    </div>
</App>
