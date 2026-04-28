<script lang="ts">
    import Badge from '@/components/ui/Badge.svelte';
    import {
        Brain,
        Zap,
        CheckCircle,
        ChevronUp,
        MessageSquare,
        Database,
        Activity,
        Terminal,
        TrendingUp,
        Info,
    } from 'lucide-svelte';
    import { fade, scale, slide } from 'svelte/transition';
    import type { QuestionShowState } from '@/states/Mahasiswa/QuizState.svelte.ts';

    interface Props {
        quizState: QuestionShowState;
        showDebug?: boolean;
    }

    let { quizState, showDebug = false }: Props = $props();

    let isDebugPanelCollapsed = $state(true);
    let activeTab = $state<'overview' | 'raw' | 'state'>('overview');

    function toggleDebugCollapse() {
        isDebugPanelCollapsed = !isDebugPanelCollapsed;
    }

    const metadata = $derived(quizState.feedbackData?.adaptiveResult?.engine_metadata);
    const factCodes = $derived(quizState.adaptiveFacts);
    const triggeredRule = $derived(quizState.adaptiveTriggeredRule);
    const newState = $derived(quizState.feedbackData?.adaptiveResult?.new_state);

    const factCategories = $derived.by(() => {
        if (!metadata) return {};

        const groups: Record<string, string[]> = {};
        factCodes.forEach((code) => {
            // Logic to group by prefix: G for primary, V for virtual
            const cat = code.startsWith('V') ? 'virtual' : 'primary';
            if (!groups[cat]) groups[cat] = [];
            groups[cat].push(code);
        });
        return groups;
    });

    function getFactLabel(factCode: string) {
        return metadata?.fact_labels[factCode] || factCode;
    }

    const stateChanges = $derived.by(() => {
        if (!newState) return [];

        const keys = ['accuracy', 'xp', 'streak', 'target_difficulty'];
        // Cast newState to Record<string, any> to allow dynamic string indexing
        const stateObj = newState as Record<string, any>;

        return keys
            .map((key) => ({
                key,
                value: stateObj[key],
            }))
            .filter((item) => item.value !== undefined);
    });
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }
</style>

{#if showDebug}
    <div
        class="fixed right-4 bottom-4 left-4 z-1001 lg:right-8 lg:left-auto lg:w-[500px]"
        transition:scale={{ duration: 400, start: 0.9, opacity: 0 }}
    >
        <div
            class="overflow-hidden rounded-2xl border border-slate-200/60 bg-white/80 shadow-2xl backdrop-blur-xl transition-all duration-500 {!isDebugPanelCollapsed
                ? 'shadow-primary-500/10'
                : ''}"
        >
            <!-- Header Bar -->
            <button
                class="flex w-full cursor-pointer items-center justify-between px-5 py-3 transition-colors hover:bg-slate-50/50 focus:outline-none"
                onclick={toggleDebugCollapse}
            >
                <div class="flex items-center gap-3">
                    <div
                        class="bg-primary-600 shadow-primary-500/30 flex h-9 w-9 items-center justify-center rounded-xl shadow-lg"
                        class:animate-pulse={quizState.isProcessing}
                    >
                        <Brain size={20} class="text-white" />
                    </div>
                    <div class="text-left">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-black tracking-tight text-slate-800"
                                >Adaptive Intelligence</span
                            >
                            {#if isDebugPanelCollapsed}
                                <div class="flex gap-1" in:fade>
                                    <Badge
                                        variant="primary"
                                        size="xs"
                                        class="px-1.5 py-0 text-[10px]"
                                    >
                                        {factCodes.length} facts
                                    </Badge>
                                    {#if triggeredRule}
                                        <Badge
                                            variant="success"
                                            size="xs"
                                            class="px-1.5 py-0 text-[10px]"
                                        >
                                            Rule Hit
                                        </Badge>
                                    {/if}
                                </div>
                            {/if}
                        </div>
                        <p class="text-[10px] font-medium tracking-widest text-slate-400 uppercase">
                            {metadata?.engine_version
                                ? `v${metadata.engine_version}`
                                : 'Scanning...'}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    {#if quizState.isProcessing}
                        <Activity size={16} class="text-primary-500 animate-spin" />
                    {/if}
                    <div
                        class="text-slate-400 transition-transform duration-300"
                        class:rotate-180={!isDebugPanelCollapsed}
                    >
                        <ChevronUp size={20} />
                    </div>
                </div>
            </button>

            {#if !isDebugPanelCollapsed}
                <div
                    transition:slide={{ duration: 400 }}
                    class="flex flex-col border-t border-slate-100"
                >
                    <!-- Tabs Navigation -->
                    <div class="flex border-b border-slate-100 bg-slate-50/50 px-4 py-1">
                        {#each ['overview', 'state', 'raw'] as tab}
                            <button
                                class="relative px-3 py-2 text-[10px] font-bold tracking-widest uppercase transition-colors"
                                class:text-primary-600={activeTab === tab}
                                class:text-slate-400={activeTab !== tab}
                                onclick={() => (activeTab = tab as any)}
                            >
                                {tab}
                                {#if activeTab === tab}
                                    <div
                                        class="bg-primary-500 absolute right-2 bottom-0 left-2 h-0.5 rounded-full"
                                    ></div>
                                {/if}
                            </button>
                        {/each}
                    </div>

                    <div class="custom-scrollbar max-h-[60vh] overflow-y-auto p-5">
                        {#if activeTab === 'overview'}
                            <!-- Facts Section -->
                            <section class="mb-6" in:fade>
                                <div
                                    class="mb-3 flex items-center gap-2 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                                >
                                    <Database size={12} />
                                    <span>Knowledge Base ({factCodes.length} Facts)</span>
                                </div>
                                <div class="space-y-4">
                                    {#each Object.entries(factCategories) as [cat, codes]}
                                        <div>
                                            <p
                                                class="mb-1.5 text-[9px] font-bold tracking-tighter text-slate-400 uppercase"
                                            >
                                                {cat === 'virtual'
                                                    ? '✦ Inferred States (Virtual)'
                                                    : '○ Raw Observations (Primary)'}
                                            </p>
                                            <div class="flex flex-wrap gap-1.5">
                                                {#each codes as code}
                                                    <Badge
                                                        variant={cat === 'virtual'
                                                            ? 'info'
                                                            : 'secondary'}
                                                        size="xs"
                                                        class="border-none bg-slate-100 font-medium text-slate-700 transition-colors hover:bg-slate-200"
                                                    >
                                                        <span class="mr-1.5 font-mono opacity-40"
                                                            >{code}</span
                                                        >
                                                        {getFactLabel(code)}
                                                    </Badge>
                                                {/each}
                                            </div>
                                        </div>
                                    {/each}
                                </div>
                            </section>

                            <!-- Execution Result -->
                            <section in:fade>
                                <div
                                    class="mb-3 flex items-center gap-2 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                                >
                                    <Zap size={12} />
                                    <span>Inference Result</span>
                                </div>

                                {#if triggeredRule}
                                    <div
                                        class="overflow-hidden rounded-xl border border-emerald-100 bg-emerald-50/50 p-4"
                                    >
                                        <div class="flex items-start gap-3">
                                            <div
                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white shadow-sm ring-1 ring-emerald-200"
                                            >
                                                <CheckCircle size={20} class="text-emerald-500" />
                                            </div>
                                            <div>
                                                <h4
                                                    class="text-sm leading-tight font-bold text-slate-800"
                                                >
                                                    {triggeredRule?.name || 'Unknown Rule'}
                                                </h4>
                                                <div class="mt-1 flex flex-wrap items-center gap-2">
                                                    <Badge
                                                        variant="success"
                                                        size="xs"
                                                        class="py-0 font-mono text-[9px] lowercase"
                                                    >
                                                        {triggeredRule.id}
                                                    </Badge>
                                                    <span
                                                        class="text-[10px] font-medium text-slate-400"
                                                    >
                                                        Priority: {triggeredRule.priority}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        {#if quizState.feedbackData?.message}
                                            <div
                                                class="mt-4 flex gap-2 rounded-lg bg-white/60 p-3 ring-1 ring-emerald-100"
                                            >
                                                <MessageSquare
                                                    size={14}
                                                    class="mt-0.5 shrink-0 text-emerald-500"
                                                />
                                                <p
                                                    class="text-xs leading-relaxed text-slate-600 italic"
                                                >
                                                    "{quizState.feedbackData.message}"
                                                </p>
                                            </div>
                                        {/if}
                                    </div>
                                {:else}
                                    <div
                                        class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-100 bg-slate-50/30 px-4 py-8 text-center"
                                    >
                                        <Info size={24} class="mb-2 text-slate-300" />
                                        <p
                                            class="text-xs font-bold tracking-widest text-slate-400 uppercase"
                                        >
                                            No Active Rule Hit
                                        </p>
                                        <p class="mt-1 text-[10px] text-slate-400">
                                            System is using fallback navigation logic.
                                        </p>
                                    </div>
                                {/if}
                            </section>
                        {:else if activeTab === 'state'}
                            <!-- State Changes Section -->
                            <section in:fade>
                                <div class="mb-4 flex items-center justify-between">
                                    <div
                                        class="flex items-center gap-2 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                                    >
                                        <TrendingUp size={12} />
                                        <span>Student State Snapshot</span>
                                    </div>
                                    <Badge variant="primary" size="xs" class="font-mono">JSON</Badge
                                    >
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    {#each stateChanges as item}
                                        <div
                                            class="group hover:border-primary-200 flex flex-col rounded-xl border border-slate-100 bg-white p-3 shadow-sm transition-all"
                                        >
                                            <span
                                                class="group-hover:text-primary-500 text-[9px] font-black tracking-widest text-slate-400 uppercase transition-colors"
                                            >
                                                {item.key.replace(/_/g, ' ')}
                                            </span>
                                            <span
                                                class="mt-1 text-lg font-black tracking-tight text-slate-800"
                                            >
                                                {item.value}
                                            </span>
                                        </div>
                                    {/each}
                                </div>

                                <div class="mt-6 rounded-lg bg-amber-50 p-3 ring-1 ring-amber-100">
                                    <div
                                        class="flex items-center gap-2 text-[10px] font-bold text-amber-600 uppercase"
                                    >
                                        <Activity size={10} />
                                        <span>System Note</span>
                                    </div>
                                    <p class="mt-1 text-[10px] leading-normal text-amber-700">
                                        Data shown above reflects the state <strong>after</strong>
                                        rule execution. XP changes automatically trigger level recalculations
                                        via <code>StateProcessor</code>.
                                    </p>
                                </div>
                            </section>
                        {:else if activeTab === 'raw'}
                            <!-- Raw Instructions -->
                            <section in:fade>
                                <div
                                    class="mb-3 flex items-center gap-2 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                                >
                                    <Terminal size={12} />
                                    <span>Instruction Payload</span>
                                </div>
                                <div
                                    class="overflow-hidden rounded-xl border border-slate-200 bg-slate-900 shadow-inner"
                                >
                                    <div
                                        class="flex items-center justify-between bg-slate-800 px-4 py-2"
                                    >
                                        <div class="flex gap-1.5">
                                            <div class="h-2 w-2 rounded-full bg-red-400"></div>
                                            <div class="h-2 w-2 rounded-full bg-amber-400"></div>
                                            <div class="h-2 w-2 rounded-full bg-emerald-400"></div>
                                        </div>
                                        <span class="font-mono text-[9px] text-slate-500"
                                            >instructions.json</span
                                        >
                                    </div>
                                    <pre
                                        class="custom-scrollbar max-h-64 overflow-auto p-4 font-mono text-[10px] leading-normal text-emerald-400">
                                        {JSON.stringify(
                                            quizState.feedbackData?.adaptiveResult?.recommendations || [],
                                            null,
                                            2
                                        )}
                                    </pre>
                                </div>
                            </section>
                        {/if}
                    </div>

                    <!-- Footer Info -->
                    <div
                        class="flex items-center justify-between border-t border-slate-100 bg-slate-50/80 px-5 py-2.5"
                    >
                        <div
                            class="flex items-center gap-3 text-[10px] font-bold tracking-tighter text-slate-400 uppercase"
                        >
                            <span class="flex items-center gap-1"
                                ><Zap size={10} class="text-primary-500" /> Forward Chaining</span
                            >
                            <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                            <span>{metadata?.rule_count || '?'} Total Rules</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div
                                class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"
                            ></div>
                            <span class="text-[10px] font-black text-slate-800">AUDIT LIVE</span>
                        </div>
                    </div>
                </div>
            {/if}
        </div>
    </div>
{/if}
