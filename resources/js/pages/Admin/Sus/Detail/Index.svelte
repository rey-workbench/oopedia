<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import UserAvatar from '@/components/ui/UserAvatar.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import { Info, MessageSquare, Target } from '@lucide/svelte';
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
        <!-- Page Header -->
        <PageHeader
            title="Analisis SUS"
            subtitle="Detail instrumen System Usability Scale (SUS) responden."
            breadcrumbs={[
                { label: 'Analitik SUS', href: ROUTES.ADMIN.SUS.INDEX },
                { label: 'Detail Responden' },
            ]}
        >
            {#snippet actions()}
                <div
                    class="flex items-center gap-6 rounded-3xl bg-white p-4 shadow-xl ring-1 shadow-slate-200/50 ring-slate-100"
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
            {/snippet}
        </PageHeader>

        <div class="grid grid-cols-1 gap-12 lg:grid-cols-3">
            <!-- Score Breakdown -->
            <div class="space-y-8 lg:col-span-2">
                <Card padding="p-0">
                    {#snippet header()}
                        <div class="flex items-center gap-4">
                            <div
                                class="bg-primary-50 text-primary-500 border-primary-100 border-2 flex h-10 w-10 items-center justify-center rounded-xl"
                            >
                                <Target size={20} />
                            </div>
                            <h3 class="text-sm font-black tracking-widest text-slate-900 uppercase">
                                Input Instrumen
                            </h3>
                        </div>
                    {/snippet}
                    <div class="divide-y divide-slate-100 px-8 py-2">
                        {#each state.questions as question}
                            <div class="group flex items-center justify-between py-5">
                                <div class="flex items-start gap-6">
                                    <span
                                        class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-[10px] font-black text-slate-400"
                                    >
                                        {question.id}
                                    </span>
                                    <p
                                        class="max-w-md text-xs leading-relaxed font-bold text-slate-600 transition-colors group-hover:text-slate-900"
                                    >
                                        {question.text}
                                    </p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="text-right">
                                        <div
                                            class="text-[9px] font-bold tracking-widest text-slate-300 uppercase"
                                        >
                                            Original
                                        </div>
                                        <div class="text-base font-black text-slate-900">
                                            {state.result[
                                                `q${question.id}` as keyof typeof state.result
                                            ]}
                                        </div>
                                    </div>
                                    <div class="h-8 w-[2px] bg-slate-100"></div>
                                    <div class="text-center">
                                        <div
                                            class="text-[9px] font-bold tracking-widest text-slate-300 uppercase"
                                        >
                                            Score
                                        </div>
                                        <div
                                            class={`inline-flex h-9 w-9 items-center justify-center rounded-xl text-xs font-black ${getScoreVariant(state.calculation.item_scores[`q${question.id}`] ?? 0)}`}
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
                    <Card class="border-emerald-200 border-b-emerald-300 bg-white">
                        <div class="mb-4 flex items-center gap-3 text-emerald-600">
                            <MessageSquare size={18} />
                            <h4 class="text-[10px] font-black tracking-widest uppercase">
                                Komentar Subjektif
                            </h4>
                        </div>
                        <p class="text-sm leading-relaxed font-bold text-slate-700 italic">
                            "{state.result.comments || 'Tidak ada komentar.'}"
                        </p>
                    </Card>

                    <Card class="border-primary-200 border-b-primary-300 bg-white">
                        <div class="text-primary-600 mb-4 flex items-center gap-3">
                            <Info size={18} />
                            <h4 class="text-[10px] font-black tracking-widest uppercase">
                                Saran Optimasi
                            </h4>
                        </div>
                        <p class="text-sm leading-relaxed font-bold text-slate-700 italic">
                            "{state.result.suggestions || 'Tidak ada saran.'}"
                        </p>
                    </Card>
                </div>
            </div>

            <!-- Summary Sidebar -->
            <div class="space-y-8">
                <Card padding="p-8">
                    <div class="mb-8 text-center">
                        <h4
                            class="mb-2 text-[10px] font-bold tracking-[0.3em] text-slate-400 uppercase"
                        >
                            Skor Akhir SUS
                        </h4>
                        <div class="text-7xl font-black tracking-tighter text-slate-900">
                            {state.result.total_score}
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="rounded-2xl bg-slate-50 p-6 border-2 border-slate-200/60">
                            <p
                                class="mb-1 text-[9px] font-bold tracking-widest text-slate-400 uppercase"
                            >
                                Kontribusi Skor
                            </p>
                            <div class="flex items-end justify-between">
                                <div class="text-sm font-bold text-slate-800">Standard SUS</div>
                                <div class="text-primary-500 font-display text-xl font-black">
                                    100%
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 border-t border-slate-100 pt-6">
                            <div
                                class="flex items-center justify-between text-[10px] font-bold tracking-widest text-slate-500 uppercase"
                            >
                                <span>Item Ganjil (U)</span>
                                <span class="text-emerald-600 font-black">Sum(X-1)</span>
                            </div>
                            <div
                                class="flex items-center justify-between text-[10px] font-bold tracking-widest text-slate-500 uppercase"
                            >
                                <span>Item Genap (L)</span>
                                <span class="text-emerald-600 font-black">Sum(5-X)</span>
                            </div>
                            <div
                                class="bg-primary-50 border-2 border-primary-100 text-primary-700 mt-4 flex items-center gap-4 rounded-2xl p-4"
                            >
                                <Info size={16} class="shrink-0" />
                                <p class="text-[9px] leading-relaxed font-black uppercase">
                                    Final Score = (U + L) * 2.5
                                </p>
                            </div>
                        </div>
                    </div>
                </Card>

                <Card padding="p-6">
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
                        <div class="h-px bg-slate-100"></div>
                        <div>
                            <div
                                class="mb-1 text-[9px] font-bold tracking-widest text-slate-400 uppercase"
                            >
                                Status Validasi
                            </div>
                            <div
                                class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-[9px] font-black text-emerald-700 uppercase border border-emerald-100"
                            >
                                <div class="h-1.5 w-1.5 rounded-full bg-emerald-500"></div>
                                Verified Submission
                            </div>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
    </div>
</App>
