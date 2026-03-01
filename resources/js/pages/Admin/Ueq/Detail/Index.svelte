<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Button from '@/components/ui/Button.svelte';
    import { ArrowLeft } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import { formatDate } from '@/utils/formatters';
    import UserAvatar from '@/components/ui/UserAvatar.svelte';
    import ProgressBar from '@/components/ui/ProgressBar.svelte';
    import { untrack } from 'svelte';
    import { UeqDetailState } from '@/states/Admin/UeqState.svelte';

    let { user, survey }: { user: any; survey: any } = $props();

    const state = untrack(() => new UeqDetailState(user, survey));
</script>

<App title={`Detail UEQ - ${state.user.name}`}>
    <div class="space-y-12 pb-20">
        <div class="mb-8">
            <h1
                class="font-display text-3xl leading-tight font-extrabold tracking-tight text-slate-900 md:text-4xl"
            >
                Detail Evaluasi UEQ
            </h1>
            <div class="mt-3 flex items-center gap-2" role="presentation">
                <div class="bg-primary-600 h-1.5 w-12 rounded-full"></div>
                <div class="h-1.5 w-4 rounded-full bg-slate-200"></div>
                <div class="h-1.5 w-2 rounded-full bg-slate-100"></div>
            </div>
            <p class="mt-4 max-w-3xl leading-relaxed font-medium text-slate-500">
                {`Analisis mendalam pengalaman pengguna untuk ${state.user.name}.`}
            </p>
            <div class="mt-6 flex flex-wrap gap-4">
                <div>
                    <Button href={ROUTES.ADMIN.UEQ.INDEX} variant="ghost" icon={ArrowLeft}
                        >KEMBALI KE DAFTAR</Button
                    >
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
            <div class="space-y-10">
                <div>
                    <div
                        class="relative overflow-hidden rounded-[2rem] bg-slate-900 p-8 text-white"
                    >
                        <div
                            class="bg-primary-600/10 absolute -top-8 -right-8 h-32 w-32 rounded-full blur-2xl"
                        ></div>
                        <div class="relative z-10 flex flex-col items-center gap-6 text-center">
                            <UserAvatar
                                name={state.user?.name ?? ''}
                                size="lg"
                                dark={true}
                                class="border border-white/10 shadow-xl backdrop-blur-md"
                            />
                            <div>
                                <p
                                    class="text-primary-400 mb-1 text-[10px] font-bold tracking-widest uppercase"
                                >
                                    Responden
                                </p>
                                <h3 class="text-xl font-bold tracking-widest uppercase">
                                    {state.user ? state.user.name : 'Tamu'}
                                </h3>
                                <p
                                    class="mt-1 text-[10px] font-bold tracking-widest text-white/40 uppercase"
                                >
                                    {state.survey.nim || 'NIM tidak tercatat'}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p
                                class="mb-1 text-[9px] font-bold tracking-widest text-slate-400 uppercase"
                            >
                                Kelas
                            </p>
                            <p class="text-sm font-bold text-slate-900">
                                {state.survey.class || '-'}
                            </p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p
                                class="mb-1 text-[9px] font-bold tracking-widest text-slate-400 uppercase"
                            >
                                Validitas
                            </p>
                            <p class="text-sm font-bold text-emerald-600">VALID</p>
                        </div>
                    </div>

                    {#if state.survey.created_at}
                        <div class="mt-4 rounded-2xl bg-slate-50 p-4">
                            <p
                                class="mb-1 text-[9px] font-bold tracking-widest text-slate-400 uppercase"
                            >
                                Submitted
                            </p>
                            <p class="text-xs font-bold text-slate-600">
                                {formatDate(state.survey.created_at)}
                            </p>
                        </div>
                    {/if}
                </div>

                <div class="space-y-4">
                    {#each Object.entries(state.dimensions) as [key, value]}
                        <div class="space-y-2 rounded-2xl bg-slate-50 p-4">
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-[10px] font-bold tracking-widest text-slate-600 uppercase"
                                    >{key}</span
                                >
                                <span class="text-primary-600 text-sm font-bold"
                                    >{typeof value === 'number' ? value.toFixed(2) : value}</span
                                >
                            </div>
                            {#if typeof value === 'number'}
                                <ProgressBar
                                    value={Math.min(100, Math.max(0, (value / 7) * 100))}
                                    height="h-2"
                                    color="blue"
                                />
                            {/if}
                        </div>
                    {/each}
                </div>
            </div>

            <div class="space-y-10 lg:col-span-2">
                <div class="space-y-6">
                    <div class="rounded-[2rem] border border-amber-100 bg-amber-50 p-6">
                        <h4
                            class="mb-3 text-[10px] font-bold tracking-widest text-amber-800 uppercase"
                        >
                            Feedback Subjektif
                        </h4>
                        <p class="text-sm leading-relaxed font-medium text-amber-700">
                            {state.survey.comments || 'Tidak ada komentar.'}
                        </p>
                    </div>

                    <div class="bg-primary-50 border-primary-100 rounded-[2rem] border p-6">
                        <h4
                            class="text-primary-800 mb-3 text-[10px] font-bold tracking-widest uppercase"
                        >
                            Rekomendasi Fitur
                        </h4>
                        <p class="text-primary-700 text-sm leading-relaxed font-medium">
                            {state.survey.suggestions || 'Tidak ada saran.'}
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th
                                    class="w-1/4 px-6 py-4 text-left text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                    >Pole Negatif</th
                                >
                                {#each Array(7) as _, i}
                                    <th
                                        class="w-8 px-2 py-4 text-center text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                        >{i + 1}</th
                                    >
                                {/each}
                                <th
                                    class="w-1/4 px-6 py-4 text-right text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                    >Pole Positif</th
                                >
                            </tr>
                        </thead>
                        <tbody>
                            {#each state.aspects as aspect}
                                <tr
                                    class="border-b border-slate-50 transition-colors hover:bg-slate-50"
                                >
                                    <td class="px-6 py-4 text-xs font-medium text-slate-500"
                                        >{aspect.left}</td
                                    >
                                    {#each Array(7) as _, i}
                                        <td class="px-2 py-4 text-center">
                                            <div
                                                class={`mx-auto flex h-8 w-8 items-center justify-center rounded-lg text-[10px] font-bold
                                                ${(state.survey as any)[aspect.name] === i + 1 ? 'bg-primary-600 shadow-primary-900/20 text-white shadow-lg' : 'bg-slate-100 text-slate-300'}`}
                                            >
                                                {i + 1}
                                            </div>
                                        </td>
                                    {/each}
                                    <td
                                        class="px-6 py-4 text-right text-xs font-medium text-slate-500 uppercase"
                                        >{aspect.right}</td
                                    >
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</App>
