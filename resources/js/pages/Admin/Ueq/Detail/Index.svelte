<script lang="ts">
    import App from "@/layouts/App.svelte";
        import Button from "@/components/ui/Button.svelte";
    import { ArrowLeft } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";
    import { formatDate } from "@/utils/formatters";
    import UserAvatar from "@/components/ui/UserAvatar.svelte";
    import ProgressBar from "@/components/ui/ProgressBar.svelte";
    import { untrack } from 'svelte';
    import { UeqDetailState } from "@/states/Admin/UeqState.svelte";

    let { user, survey }: { user: any; survey: any } = $props();

    const state = untrack(() => new UeqDetailState(user, survey));
</script>

<App title={`Detail UEQ - ${state.user.name}`}>
    <div class="space-y-12 pb-20">
        
<div class="mb-8">
    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 leading-tight font-display">
        Detail Evaluasi UEQ
    </h1>
    <div class="flex items-center gap-2 mt-3" role="presentation">
        <div class="h-1.5 w-12 bg-primary-600 rounded-full"></div>
        <div class="h-1.5 w-4 bg-slate-200 rounded-full"></div>
        <div class="h-1.5 w-2 bg-slate-100 rounded-full"></div>
    </div>
    <p class="mt-4 text-slate-500 font-medium leading-relaxed max-w-3xl">
        {`Analisis mendalam pengalaman pengguna untuk ${state.user.name}.`}
    </p>
    <div class="mt-6 flex flex-wrap gap-4">
        <div>
                <Button
                    href={ROUTES.ADMIN.UEQ.INDEX}
                    variant="ghost"
                    icon={ArrowLeft}>KEMBALI KE DAFTAR</Button
                >
            </div>
    </div>
</div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="space-y-10">
                <div>
                    <div
                        class="bg-slate-900 text-white p-8 rounded-[2rem] relative overflow-hidden"
                    >
                        <div
                            class="absolute -top-8 -right-8 w-32 h-32 bg-primary-600/10 rounded-full blur-2xl"
                        ></div>
                        <div
                            class="relative z-10 flex flex-col items-center gap-6 text-center"
                        >
                            <UserAvatar
                                name={state.user?.name ?? ""}
                                size="lg"
                                dark={true}
                                class="backdrop-blur-md border border-white/10 shadow-xl"
                            />
                            <div>
                                <p
                                    class="text-[10px] font-bold uppercase tracking-widest text-primary-400 mb-1"
                                >
                                    Responden
                                </p>
                                <h3
                                    class="text-xl font-bold tracking-widest uppercase"
                                >
                                    {state.user ? state.user.name : "Tamu"}
                                </h3>
                                <p
                                    class="text-[10px] font-bold text-white/40 uppercase tracking-widest mt-1"
                                >
                                    {state.survey.nim || "NIM tidak tercatat"}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div class="p-4 bg-slate-50 rounded-2xl">
                            <p
                                class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1"
                            >
                                Kelas
                            </p>
                            <p class="font-bold text-slate-900 text-sm">
                                {state.survey.class || "-"}
                            </p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl">
                            <p
                                class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1"
                            >
                                Validitas
                            </p>
                            <p class="font-bold text-emerald-600 text-sm">
                                VALID
                            </p>
                        </div>
                    </div>

                    {#if state.survey.created_at}
                        <div class="p-4 bg-slate-50 rounded-2xl mt-4">
                            <p
                                class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1"
                            >
                                Submitted
                            </p>
                            <p class="font-bold text-slate-600 text-xs">
                                {formatDate(state.survey.created_at)}
                            </p>
                        </div>
                    {/if}
                </div>

                <div class="space-y-4">
                    {#each Object.entries(state.dimensions) as [key, value]}
                        <div class="p-4 bg-slate-50 rounded-2xl space-y-2">
                            <div class="flex justify-between items-center">
                                <span
                                    class="text-[10px] font-bold uppercase tracking-widest text-slate-600"
                                    >{key}</span
                                >
                                <span class="text-sm font-bold text-primary-600"
                                    >{typeof value === "number"
                                        ? value.toFixed(2)
                                        : value}</span
                                >
                            </div>
                            {#if typeof value === "number"}
                                <ProgressBar
                                    value={Math.min(
                                        100,
                                        Math.max(0, (value / 7) * 100),
                                    )}
                                    height="h-2"
                                    color="blue"
                                />
                            {/if}
                        </div>
                    {/each}
                </div>
            </div>

            <div class="lg:col-span-2 space-y-10">
                <div class="space-y-6">
                    <div
                        class="p-6 bg-amber-50 rounded-[2rem] border border-amber-100"
                    >
                        <h4
                            class="text-[10px] font-bold uppercase tracking-widest text-amber-800 mb-3"
                        >
                            Feedback Subjektif
                        </h4>
                        <p
                            class="text-sm text-amber-700 font-medium leading-relaxed"
                        >
                            {state.survey.comments || "Tidak ada komentar."}
                        </p>
                    </div>

                    <div
                        class="p-6 bg-primary-50 rounded-[2rem] border border-primary-100"
                    >
                        <h4
                            class="text-[10px] font-bold uppercase tracking-widest text-primary-800 mb-3"
                        >
                            Rekomendasi Fitur
                        </h4>
                        <p
                            class="text-sm text-primary-700 font-medium leading-relaxed"
                        >
                            {state.survey.suggestions || "Tidak ada saran."}
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest text-slate-400 w-1/4"
                                    >Pole Negatif</th
                                >
                                {#each Array(7) as _, i}
                                    <th
                                        class="px-2 py-4 text-center text-[10px] font-bold uppercase tracking-widest text-slate-400 w-8"
                                        >{i + 1}</th
                                    >
                                {/each}
                                <th
                                    class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400 w-1/4"
                                    >Pole Positif</th
                                >
                            </tr>
                        </thead>
                        <tbody>
                            {#each state.aspects as aspect}
                                <tr
                                    class="border-b border-slate-50 hover:bg-slate-50 transition-colors"
                                >
                                    <td
                                        class="px-6 py-4 text-xs font-medium text-slate-500"
                                        >{aspect.left}</td
                                    >
                                    {#each Array(7) as _, i}
                                        <td class="px-2 py-4 text-center">
                                            <div
                                                class={`w-8 h-8 mx-auto rounded-lg flex items-center justify-center text-[10px] font-bold
                                                ${(state.survey as any)[aspect.name] === i + 1 ? "bg-primary-600 text-white shadow-lg shadow-primary-900/20" : "bg-slate-100 text-slate-300"}`}
                                            >
                                                {i + 1}
                                            </div>
                                        </td>
                                    {/each}
                                    <td
                                        class="px-6 py-4 text-xs font-medium text-slate-500 text-right uppercase"
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
