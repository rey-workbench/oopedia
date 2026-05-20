<script lang="ts">
    import Card from '@/components/ui/Card.svelte';
    import Badge from '@/components/ui/Badge.svelte';
    import { Brain, Calculator, Info, Users } from '@lucide/svelte';

    let { analysis } = $props<{ analysis: any }>();

    const reliability = $derived(analysis?.reliability || {});
    const comparison = $derived(analysis?.comparison || {});
    const descriptive = $derived(analysis?.descriptive || {});

    function formatNumber(num: any) {
        if (num === null || num === undefined) return '-';
        return typeof num === 'number' ? num.toFixed(3) : num;
    }
</script>

<div class="space-y-8">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Reliability Card -->
        <Card class="border-duo overflow-hidden rounded-3xl border-slate-100 shadow-xl">
            <div class="p-8">
                <div class="mb-6 flex items-center gap-4">
                    <div
                        class="bg-primary-50 text-primary-500 border-primary-100 flex h-10 w-10 items-center justify-center rounded-xl border-2"
                    >
                        <Brain size={20} />
                    </div>
                    <h3
                        class="text-primary-500 font-display text-lg font-black tracking-widest uppercase"
                    >
                        Reliabilitas
                    </h3>
                </div>

                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <span
                            class="text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >Cronbach's Alpha</span
                        >
                        <span class="text-2xl font-black text-slate-900"
                            >{formatNumber(reliability.cronbach_alpha)}</span
                        >
                    </div>

                    <div class="flex items-center justify-between">
                        <span
                            class="text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >Status</span
                        >
                        <Badge
                            variant={reliability.status === 'Reliabel' ? 'success' : 'danger'}
                            class="rounded-lg px-3 py-1 font-black"
                        >
                            {reliability.status || 'N/A'}
                        </Badge>
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="flex items-start gap-3">
                            <Info size={16} class="mt-1 text-slate-400" />
                            <p
                                class="text-[10px] leading-relaxed font-bold text-slate-500 uppercase"
                            >
                                Instrumen dikatakan reliabel jika Cronbach's Alpha > 0.60. Data
                                dihitung dari {reliability.n_samples} responden.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </Card>

        <!-- Hypothesis Testing Card -->
        <Card
            class="border-duo overflow-hidden rounded-3xl border-slate-100 shadow-xl lg:col-span-2"
        >
            <div class="p-8">
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div
                            class="bg-primary-50 text-primary-500 border-primary-100 flex h-10 w-10 items-center justify-center rounded-xl border-2"
                        >
                            <Calculator size={20} />
                        </div>
                        <h3
                            class="text-primary-500 font-display text-lg font-black tracking-widest uppercase"
                        >
                            Uji Hipotesis (Komparasi)
                        </h3>
                    </div>

                    {#if !comparison.t_test}
                        <div class="flex items-center gap-2 text-slate-400">
                            <Users size={16} />
                            <span class="text-[10px] font-black tracking-widest uppercase"
                                >Pilih 2 kelas untuk komparasi</span
                            >
                        </div>
                    {/if}
                </div>

                {#if comparison.t_test}
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                        <!-- Independent T-Test -->
                        <div class="space-y-4">
                            <h4
                                class="text-[11px] font-black tracking-widest text-slate-900 uppercase"
                            >
                                Independent T-Test (Parametrik)
                            </h4>
                            <div class="space-y-3 rounded-2xl border border-slate-100 p-5">
                                <div class="flex justify-between">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase"
                                        >T-Value</span
                                    >
                                    <span class="text-sm font-black text-slate-700"
                                        >{formatNumber(comparison.t_test.t)}</span
                                    >
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase"
                                        >P-Value (2-tailed)</span
                                    >
                                    <span
                                        class="font-black {comparison.t_test['p-value'] < 0.05
                                            ? 'text-emerald-500'
                                            : 'text-slate-700'}"
                                    >
                                        {formatNumber(comparison.t_test['p-value'])}
                                    </span>
                                </div>
                                <div class="mt-2 flex items-center gap-2">
                                    <div
                                        class="h-2 w-2 rounded-full {comparison.t_test['p-value'] <
                                        0.05
                                            ? 'bg-emerald-500'
                                            : 'bg-slate-300'}"
                                    ></div>
                                    <span class="text-[9px] font-black tracking-widest uppercase">
                                        {comparison.t_test['p-value'] < 0.05
                                            ? 'Signifikan (H1 Diterima)'
                                            : 'Tidak Signifikan (H0 Diterima)'}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Mann-Whitney U -->
                        <div class="space-y-4">
                            <h4
                                class="text-[11px] font-black tracking-widest text-slate-900 uppercase"
                            >
                                Mann-Whitney U (Non-Parametrik)
                            </h4>
                            <div class="space-y-3 rounded-2xl border border-slate-100 p-5">
                                <div class="flex justify-between">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase"
                                        >Z-Score</span
                                    >
                                    <span class="text-sm font-black text-slate-700"
                                        >{formatNumber(comparison.mann_whitney.z_score)}</span
                                    >
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase"
                                        >U-Min</span
                                    >
                                    <span class="text-sm font-black text-slate-700"
                                        >{comparison.mann_whitney.u_min}</span
                                    >
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase"
                                        >P-Value</span
                                    >
                                    <span
                                        class="font-black {comparison.mann_whitney['p-value'] < 0.05
                                            ? 'text-emerald-500'
                                            : 'text-slate-700'}"
                                    >
                                        {formatNumber(comparison.mann_whitney['p-value'])}
                                    </span>
                                </div>
                                <div class="mt-2 flex items-center gap-2">
                                    <div
                                        class="h-2 w-2 rounded-full {comparison.mann_whitney[
                                            'p-value'
                                        ] < 0.05
                                            ? 'bg-emerald-500'
                                            : 'bg-slate-300'}"
                                    ></div>
                                    <span class="text-[9px] font-black tracking-widest uppercase">
                                        {comparison.mann_whitney['p-value'] < 0.05
                                            ? 'Signifikan (H1 Diterima)'
                                            : 'Tidak Signifikan (H0 Diterima)'}
                                    </span>
                                </div>
                                <div
                                    class="mt-2 text-[9px] font-bold text-slate-400 uppercase italic"
                                >
                                    Digunakan jika data tidak berdistribusi normal.
                                </div>
                            </div>
                        </div>
                    </div>
                {:else}
                    <div
                        class="flex h-40 flex-col items-center justify-center rounded-3xl border-2 border-dashed border-slate-100 bg-slate-50"
                    >
                        <Users size={48} class="mb-4 text-slate-200" />
                        <p class="text-[10px] font-black tracking-widest text-slate-400 uppercase">
                            Gunakan filter kelas untuk membandingkan dua kelompok data
                        </p>
                    </div>
                {/if}
            </div>
        </Card>
    </div>

    {#if descriptive.group1}
        <Card class="border-duo overflow-hidden rounded-3xl border-slate-100 shadow-xl">
            <div class="p-8">
                <div class="mb-6 flex items-center gap-4">
                    <div
                        class="bg-primary-50 text-primary-500 border-primary-100 flex h-10 w-10 items-center justify-center rounded-xl border-2"
                    >
                        <Users size={20} />
                    </div>
                    <h3
                        class="text-primary-500 font-display text-lg font-black tracking-widest uppercase"
                    >
                        Statistik Deskriptif
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th
                                    class="pb-4 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                                    >Metrik</th
                                >
                                <th
                                    class="text-primary-500 pb-4 text-[10px] font-black tracking-widest uppercase"
                                    >Kelompok 1 (Aktif)</th
                                >
                                {#if descriptive.group2}
                                    <th
                                        class="text-accent-500 pb-4 text-[10px] font-black tracking-widest uppercase"
                                        >Kelompok 2</th
                                    >
                                {/if}
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            {#each ['mean', 'median', 'mode', 'standard_deviation', 'variance', 'skewness', 'kurtosis', 'min', 'max'] as key}
                                <tr class="group transition-colors hover:bg-slate-50/50">
                                    <td class="py-4 text-[11px] font-bold text-slate-600 uppercase"
                                        >{key.replace('_', ' ')}</td
                                    >
                                    <td class="font-display py-4 font-black text-slate-900"
                                        >{formatNumber(descriptive.group1[key])}</td
                                    >
                                    {#if descriptive.group2}
                                        <td class="font-display py-4 font-black text-slate-900"
                                            >{formatNumber(descriptive.group2[key])}</td
                                        >
                                    {/if}
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>
            </div>
        </Card>
    {/if}
</div>
