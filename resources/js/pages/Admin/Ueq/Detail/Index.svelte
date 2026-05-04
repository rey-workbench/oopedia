<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Card from '@/components/ui/Card.svelte';
    import { ArrowLeft, MessageSquare, Target } from 'lucide-svelte';
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
        <div class="mb-8">
            <h1
                class="text-primary-500 font-display text-3xl font-black tracking-widest uppercase md:text-4xl"
            >
                Detail Analisis <span class="text-accent-500">UEQ</span>
            </h1>
            <div class="mt-3 flex items-center gap-2">
                <div class="bg-accent-500 h-2 w-16 rounded-full"></div>
                <div class="h-2 w-4 rounded-full bg-slate-200"></div>
            </div>
            <div class="mt-6 flex flex-wrap items-center justify-between gap-6">
                <Button
                    href={ROUTES.ADMIN.UEQ.INDEX}
                    variant="ghost"
                    icon={ArrowLeft}
                    size="sm"
                    class="hover:text-primary-500 font-black text-slate-400"
                    >KEMBALI KE DAFTAR</Button
                >

                <div
                    class="flex items-center gap-6 rounded-3xl bg-white p-4 shadow-xl shadow-slate-200/50"
                >
                    <UserAvatar name={state.user?.name ?? ''} size="lg" />
                    <div>
                        <div class="text-sm font-bold tracking-widest text-slate-900 uppercase">
                            {state.user ? state.user.name : 'Tamu'}
                        </div>
                        <div
                            class="mt-0.5 text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                        >
                            ID: {state.survey.id.substring(0, 8)} • {state.survey.assessment_type === 'pre' ? 'Pre-Test (Awal)' : 'Post-Test (Akhir)'}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
            <div id="ueq-respondent-profile" class="space-y-8">
                <Card class="rounded-3xl border-none bg-slate-900 p-10 text-white shadow-2xl">
                    <div class="mb-8 text-center">
                        <div
                            class="mb-4 inline-flex items-center gap-2 rounded-full bg-emerald-500/20 px-3 py-1 text-[9px] font-black text-emerald-400 uppercase"
                        >
                            <div class="h-1 w-1 rounded-full bg-emerald-400 animate-pulse"></div>
                            Verified Submission
                        </div>
                        <h4 class="mb-2 text-[10px] font-bold tracking-[0.3em] text-slate-500 uppercase">
                            UEQ Validitas
                        </h4>
                        <div class="text-4xl font-black tracking-widest text-white">
                            VALID
                        </div>
                    </div>

                    <div class="space-y-4 border-t border-white/10 pt-8">
                        <div>
                            <div class="mb-1 text-[9px] font-bold tracking-widest text-slate-500 uppercase">Submitted</div>
                            <div class="text-xs font-bold text-slate-300">
                                {formatDate(state.survey.created_at)}
                            </div>
                        </div>
                        <div class="h-px bg-white/5"></div>
                        <div>
                            <div class="mb-1 text-[9px] font-bold tracking-widest text-slate-500 uppercase">Tipe Asesmen</div>
                            <div class="text-xs font-bold text-slate-300">
                                {state.survey.assessment_type === 'pre' ? 'Pre-Test (Awal)' : 'Post-Test (Akhir)'}
                            </div>
                        </div>
                    </div>
                </Card>

                <div id="ueq-dimension-scores" class="space-y-4">
                    <h4 class="px-4 text-[10px] font-black tracking-widest text-slate-400 uppercase">Skor Dimensi</h4>
                    {#each Object.entries(state.dimensions) as [key, value]}
                        <div class="group space-y-3 rounded-2xl bg-slate-50 p-5 transition-all hover:bg-white hover:shadow-xl">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-black tracking-widest text-slate-600 uppercase">{key}</span>
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
                    <Card class="rounded-[2rem] border-amber-100 bg-amber-50/30">
                        <div class="mb-4 flex items-center gap-3 text-amber-600">
                            <MessageSquare size={18} />
                            <h4 class="text-[10px] font-black tracking-widest uppercase">Feedback Subjektif</h4>
                        </div>
                        <p class="text-sm leading-relaxed font-semibold text-slate-700 italic">
                            "{state.survey.comments || 'Tidak ada komentar.'}"
                        </p>
                    </Card>

                    <Card class="rounded-[2rem] border-primary-100 bg-primary-50/30">
                        <div class="mb-4 flex items-center gap-3 text-primary-600">
                            <Target size={18} />
                            <h4 class="text-[10px] font-black tracking-widest uppercase">Rekomendasi Fitur</h4>
                        </div>
                        <p class="text-sm leading-relaxed font-semibold text-slate-700 italic">
                            "{state.survey.suggestions || 'Tidak ada saran.'}"
                        </p>
                    </Card>
                </div>

                <Card class="border-duo overflow-hidden rounded-[3rem] border-slate-100 shadow-xl" padding="p-0">
                    <div class="border-b-2 border-slate-50 p-8">
                        <h3 class="text-primary-500 font-display text-lg font-black tracking-widest uppercase">Pola Jawaban Semantik</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-8 py-4 text-left text-[10px] font-black tracking-widest text-slate-400 uppercase">Pole Negatif</th>
                                    {#each Array(7) as _, i}
                                        <th class="w-10 px-2 py-4 text-center text-[10px] font-black tracking-widest text-slate-400 uppercase">{i + 1}</th>
                                    {/each}
                                    <th class="px-8 py-4 text-right text-[10px] font-black tracking-widest text-slate-400 uppercase">Pole Positif</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                {#each state.aspects as aspect}
                                    <tr class="group transition-colors hover:bg-slate-50/50">
                                        <td class="px-8 py-5 text-xs font-bold text-slate-500 group-hover:text-slate-900 transition-colors">{aspect.left}</td>
                                        {#each Array(7) as _, i}
                                            <td class="px-2 py-5 text-center">
                                                <div
                                                    class={`mx-auto flex h-9 w-9 items-center justify-center rounded-xl text-[10px] font-black transition-all
                                                    ${(state.survey as any)[aspect.name] === i + 1 
                                                        ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/20 scale-110' 
                                                        : 'bg-slate-100 text-slate-300'}`}
                                                >
                                                    {i + 1}
                                                </div>
                                            </td>
                                        {/each}
                                        <td class="px-8 py-5 text-right text-xs font-bold text-slate-500 group-hover:text-slate-900 transition-colors">{aspect.right}</td>
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
