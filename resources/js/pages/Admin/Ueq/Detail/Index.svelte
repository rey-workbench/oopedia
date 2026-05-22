<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import { MessageSquare, Target } from '@lucide/svelte';
    import { ROUTES } from '@/utils/route';
    import { formatDate } from '@/utils/formatters';
    import UserAvatar from '@/components/ui/UserAvatar.svelte';
    import ProgressBar from '@/components/ui/ProgressBar.svelte';
    import { untrack } from 'svelte';
    import { UeqDetailState } from '@/states/Admin/UeqState.svelte';

    let { user, survey }: { user: any; survey: any } = $props();

    const state = untrack(() => new UeqDetailState(user, survey));
</script>

<App title={`Detail UEQ - ${state.targetUser?.name ?? 'Unknown'}`}>
    <div class="space-y-12 pb-20">
        <!-- Page Header -->
        <PageHeader
            title="Analisis UEQ"
            subtitle="Detail kuesioner User Experience Questionnaire (UEQ) responden."
            breadcrumbs={[
                { label: 'Analitik UEQ', href: ROUTES.ADMIN.UEQ.INDEX },
                { label: 'Detail Responden' },
            ]}
        >
            {#snippet actions()}
                <div
                    class="flex items-center gap-6 rounded-3xl bg-white p-4 shadow-xl ring-1 shadow-slate-200/50 ring-slate-100"
                >
                    <UserAvatar name={state.user?.name ?? ''} size="lg" />
                    <div>
                        <div class="text-sm font-bold tracking-widest text-slate-900 uppercase">
                            {state.user ? state.user.name : 'Tamu'}
                        </div>
                        <div
                            class="mt-0.5 text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                        >
                            ID: {state.survey.id.substring(0, 8)} • {state.survey.assessment_type === 'pre'
                                ? 'Pre-Test (Awal)'
                                : 'Post-Test (Akhir)'}
                        </div>
                    </div>
                </div>
            {/snippet}
        </PageHeader>

        <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
            <div id="ueq-respondent-profile" class="space-y-8">
                <Card padding="p-8">
                    <div class="mb-8 text-center">
                        <div
                            class="mb-4 inline-flex items-center gap-2 rounded-full bg-emerald-55 px-3 py-1 text-[9px] font-black text-emerald-600 border border-emerald-100 uppercase"
                        >
                            <div class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"></div>
                            Verified Submission
                        </div>
                        <h4
                            class="mb-2 text-[10px] font-bold tracking-[0.3em] text-slate-400 uppercase"
                        >
                            Validitas UEQ
                        </h4>
                        <div class="text-4xl font-black tracking-widest text-emerald-600">VALID</div>
                    </div>

                    <div class="space-y-4 border-t border-slate-100 pt-6">
                        <div>
                            <div
                                class="mb-1 text-[9px] font-bold tracking-widest text-slate-400 uppercase"
                            >
                                Submitted
                            </div>
                            <div class="text-xs font-bold text-slate-700">
                                {formatDate(state.survey.created_at)}
                            </div>
                        </div>
                        <div class="h-px bg-slate-100"></div>
                        <div>
                            <div
                                class="mb-1 text-[9px] font-bold tracking-widest text-slate-400 uppercase"
                            >
                                Tipe Asesmen
                            </div>
                            <div class="text-xs font-bold text-slate-700">
                                {state.survey.assessment_type === 'pre'
                                    ? 'Pre-Test (Awal)'
                                    : 'Post-Test (Akhir)'}
                            </div>
                        </div>
                    </div>
                </Card>

                <div id="ueq-dimension-scores" class="space-y-4">
                    <h4
                        class="px-4 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                    >
                        Skor Dimensi
                    </h4>
                    {#each Object.entries(state.dimensions) as [key, value]}
                        <div
                            class="group space-y-3 rounded-2xl bg-white border-2 border-slate-200/60 p-5 transition-all hover:shadow-lg"
                        >
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-[10px] font-black tracking-widest text-slate-600 uppercase"
                                    >{key}</span
                                >
                                <span class="text-primary-500 text-sm font-black">
                                    {typeof value === 'number' ? value.toFixed(2) : value}
                                </span>
                            </div>
                            {#if typeof value === 'number'}
                                <ProgressBar
                                    value={Math.min(100, Math.max(0, ((value + 3) / 6) * 100))}
                                    height="h-1.5"
                                    color="blue"
                                />
                            {/if}
                        </div>
                    {/each}
                </div>
            </div>

            <div class="space-y-10 lg:col-span-2">
                <div id="ueq-user-feedback" class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <Card class="border-amber-200 border-b-amber-300 bg-white">
                        <div class="mb-4 flex items-center gap-3 text-amber-600">
                            <MessageSquare size={18} />
                            <h4 class="text-[10px] font-black tracking-widest uppercase">
                                Feedback Subjektif
                            </h4>
                        </div>
                        <p class="text-sm leading-relaxed font-bold text-slate-700 italic">
                            "{state.survey.comments || 'Tidak ada komentar.'}"
                        </p>
                    </Card>

                    <Card class="border-primary-200 border-b-primary-300 bg-white">
                        <div class="text-primary-600 mb-4 flex items-center gap-3">
                            <Target size={18} />
                            <h4 class="text-[10px] font-black tracking-widest uppercase">
                                Rekomendasi Fitur
                            </h4>
                        </div>
                        <p class="text-sm leading-relaxed font-bold text-slate-700 italic">
                            "{state.survey.suggestions || 'Tidak ada saran.'}"
                        </p>
                    </Card>
                </div>

                <Card padding="p-0">
                    {#snippet header()}
                        <h3
                            class="text-primary-500 font-display text-sm font-black tracking-widest uppercase"
                        >
                            Pola Jawaban Semantik
                        </h3>
                    {/snippet}
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50/50">
                                    <th
                                        class="px-8 py-4 text-left text-[10px] font-black tracking-widest text-slate-400 uppercase"
                                        >Pole Negatif</th
                                    >
                                    {#each Array(7) as _, i}
                                        <th
                                            class="w-10 px-2 py-4 text-center text-[10px] font-black tracking-widest text-slate-400 uppercase"
                                            >{i + 1}</th
                                        >
                                    {/each}
                                    <th
                                        class="px-8 py-4 text-right text-[10px] font-black tracking-widest text-slate-400 uppercase"
                                        >Pole Positif</th
                                    >
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                {#each state.aspects as aspect}
                                    <tr class="group transition-colors hover:bg-slate-50/50">
                                        <td
                                            class="px-8 py-5 text-xs font-bold text-slate-500 transition-colors group-hover:text-slate-900"
                                            >{aspect.left}</td
                                        >
                                        {#each Array(7) as _, i}
                                            <td class="px-2 py-5 text-center">
                                                <div
                                                    class={`mx-auto flex h-9 w-9 items-center justify-center rounded-xl text-[10px] font-black transition-all
                                                    ${
                                                        (state.survey as any)[aspect.name] === i + 1
                                                            ? 'bg-primary-500 shadow-primary-500/20 scale-110 text-white shadow-lg'
                                                            : 'bg-slate-100 text-slate-300'
                                                    }`}
                                                >
                                                    {i + 1}
                                                </div>
                                            </td>
                                        {/each}
                                        <td
                                            class="px-8 py-5 text-right text-xs font-bold text-slate-500 transition-colors group-hover:text-slate-900"
                                            >{aspect.right}</td
                                        >
                                    </tr>
                                {/each}
                            </tbody>
                        </table>
                    </div>
                </Card>
            </div>
        </div>
    </div>
</App>
