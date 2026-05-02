<script lang="ts">
    import { slide, fade } from 'svelte/transition';
    import { quintOut } from 'svelte/easing';
    import { Activity, Zap, ChevronDown, Database, Cpu } from 'lucide-svelte';
    import { QuizState } from '@/states/Mahasiswa/QuizState.svelte';

    let { quizState }: { quizState: QuizState } = $props();

    let isDebugPanelCollapsed = $state(true);
    let activeTab = $state<'overview' | 'chaining' | 'state' | 'raw'>('overview');

    function toggleDebugCollapse() {
        isDebugPanelCollapsed = !isDebugPanelCollapsed;
    }

    const engineResult = $derived(quizState.feedbackData?.adaptive_result);
    const metadata = $derived(engineResult?.engine_metadata);
    const factCodes = $derived(engineResult?.facts || []);
    const deducedFacts = $derived(engineResult?.deduced_facts || []);
    const triggeredRule = $derived(engineResult?.triggered_rule);
    const ruleChain = $derived(metadata?.rule_chain || []);
</script>

<div class="pointer-events-none fixed right-4 top-1/2 z-10005 flex -translate-y-1/2 flex-col items-end gap-2">
    {#if !isDebugPanelCollapsed}
        <div
            transition:slide={{ duration: 400, easing: quintOut }}
            class="pointer-events-auto mb-2 w-96 max-w-[90vw] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl"
        >
            <!-- Header -->
            <div class="flex items-center justify-between bg-slate-900 px-4 py-3 text-white">
                <div class="flex items-center gap-2">
                    <div class="rounded-lg bg-emerald-500/20 p-1.5 text-emerald-400">
                        <Cpu size={18} />
                    </div>
                    <div>
                        <h3 class="text-sm leading-none font-bold">Adaptive Engine</h3>
                        <p class="mt-1 text-[10px] font-medium text-slate-400">
                            v{metadata?.engine_version || '4.1.0'} • {metadata?.iterations || 0} Iterations
                        </p>
                    </div>
                </div>
                <button
                    onclick={toggleDebugCollapse}
                    class="rounded-lg p-1 text-slate-400 transition-colors hover:bg-slate-800 hover:text-white"
                >
                    <ChevronDown size={20} />
                </button>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-slate-100 bg-slate-50/50 p-1">
                {#each ['overview', 'chaining', 'state', 'raw'] as tab}
                    <button
                        onclick={() => (activeTab = tab as any)}
                        class="flex-1 rounded-md px-2 py-1.5 text-[11px] font-bold transition-all {activeTab ===
                        tab
                            ? 'bg-white text-slate-900 shadow-sm'
                            : 'text-slate-500 hover:text-slate-800'}"
                    >
                        {tab.charAt(0).toUpperCase() + tab.slice(1)}
                    </button>
                {/each}
            </div>

            <!-- Content Area -->
            <div class="max-h-[450px] overflow-x-hidden overflow-y-auto p-4">
                {#if activeTab === 'overview'}
                    <div class="space-y-4" in:fade={{ duration: 200 }}>
                        <!-- Diagnosis Card -->
                        <div class="rounded-xl border border-indigo-100 bg-indigo-50/30 p-3">
                            <div class="mb-2 flex items-center gap-2 text-indigo-600">
                                <Zap size={14} />
                                <span class="text-[10px] font-bold tracking-wider uppercase"
                                    >Final Diagnosis</span
                                >
                            </div>
                            <h4 class="text-sm font-bold text-slate-900">
                                {triggeredRule?.rule?.name || 'Standard Progress'}
                            </h4>
                            <p class="mt-1 text-xs text-slate-600">
                                {triggeredRule?.rule?.recommendation ||
                                    'No pedagogical intervention triggered.'}
                            </p>
                        </div>

                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                                <div class="mb-1 text-[10px] font-bold text-slate-400 uppercase">
                                    Rules Eval
                                </div>
                                <div class="text-lg font-black text-slate-700">
                                    {metadata?.total_rules_evaluated || (ruleChain.length > 0 ? ruleChain.length + 2 : 0)}
                                </div>
                            </div>
                            <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                                <div class="mb-1 text-[10px] font-bold text-slate-400 uppercase">
                                    Exec Time
                                </div>
                                <div class="text-lg font-black text-slate-700">
                                    {metadata?.execution_time_ms || 0}ms
                                </div>
                            </div>
                        </div>
                    </div>
                {:else if activeTab === 'chaining'}
                    <div class="space-y-4" in:fade={{ duration: 200 }}>
                        <div class="mb-2 flex items-center gap-2 text-slate-400">
                            <Zap size={12} />
                            <span class="text-[10px] font-bold tracking-wider uppercase">Inference Chain</span>
                        </div>
                        <div class="space-y-2">
                            {#each ruleChain as ruleId, i}
                                <div class="flex items-center gap-3">
                                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-slate-100 text-[10px] font-bold text-slate-500">
                                        {i + 1}
                                    </div>
                                    <div class="flex-1 rounded-lg border border-slate-100 bg-slate-50 px-3 py-2 text-xs font-bold text-slate-700">
                                        {ruleId}
                                    </div>
                                </div>
                                {#if i < ruleChain.length - 1}
                                    <div class="ml-3 h-3 w-px bg-slate-200"></div>
                                {/if}
                            {:else}
                                <p class="text-center text-xs italic text-slate-400 py-4">No chaining records available.</p>
                            {/each}
                        </div>
                    </div>
                {:else if activeTab === 'state'}
                    <div class="space-y-4" in:fade={{ duration: 200 }}>
                         <!-- Active Facts -->
                         <div>
                            <div class="mb-2 flex items-center gap-2 text-blue-500">
                                <Database size={12} />
                                <span class="text-[10px] font-bold tracking-wider uppercase"
                                    >Active Facts (Input)</span
                                >
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                {#each factCodes as fact}
                                    <span
                                        class="rounded-md border border-blue-100 bg-blue-50 px-2 py-1 text-[10px] font-bold text-blue-600 shadow-sm"
                                    >
                                        {fact}
                                    </span>
                                {:else}
                                    <span class="text-[10px] italic text-slate-400"
                                        >No active facts</span
                                    >
                                {/each}
                            </div>
                        </div>

                        <!-- Deduced Facts -->
                        <div>
                            <div class="mb-2 flex items-center gap-2 text-emerald-500">
                                <Activity size={12} />
                                <span class="text-[10px] font-bold tracking-wider uppercase"
                                    >Deduced Facts (Output)</span
                                >
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                {#each deducedFacts as fact}
                                    <span
                                        class="rounded-md border border-emerald-100 bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-600 shadow-sm"
                                    >
                                        {fact}
                                    </span>
                                {:else}
                                    <span class="text-[10px] italic text-slate-400"
                                        >No facts deduced in this session</span
                                    >
                                {/each}
                            </div>
                        </div>
                    </div>
                {:else if activeTab === 'raw'}
                    <div class="rounded-xl bg-slate-900 p-4" in:fade={{ duration: 200 }}>
                        <pre class="overflow-x-auto font-mono text-[10px] leading-relaxed text-emerald-400"><code>{JSON.stringify(engineResult, null, 2)}</code></pre>
                    </div>
                {/if}
            </div>

            <!-- Footer -->
            <div
                class="flex items-center justify-between border-t border-slate-100 bg-slate-50 px-4 py-2"
            >
                <div class="flex items-center gap-1.5">
                    <div class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"></div>
                    <span class="text-[10px] font-bold tracking-tighter text-slate-500 uppercase"
                        >Engine Healthy</span
                    >
                </div>
                <span class="font-mono text-[9px] text-slate-400">
                    {metadata?.engine_version || '4.1.0'}
                </span>
            </div>
        </div>
    {/if}

    <button
        onclick={toggleDebugCollapse}
        class="group pointer-events-auto flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2.5 shadow-xl transition-all hover:scale-105 active:scale-95 {isDebugPanelCollapsed
            ? 'text-slate-600'
            : 'border-slate-800 bg-slate-900 text-white ring-4 ring-slate-900/10'}"
    >
        <Activity
            size={18}
            class={isDebugPanelCollapsed ? 'text-emerald-500' : 'text-emerald-400'}
        />
        <span class="text-sm font-black tracking-tight">Debug</span>
        {#if isDebugPanelCollapsed}
            <div
                class="ml-1 flex h-5 w-5 items-center justify-center rounded-full bg-slate-100 text-[10px] font-black"
            >
                {metadata?.iterations || 0}
            </div>
        {/if}
    </button>
</div>
