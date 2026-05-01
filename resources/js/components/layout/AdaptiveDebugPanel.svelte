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
        Cpu,
        GitBranch,
        Layers,
        Clock,
    } from 'lucide-svelte';
    import { fade, scale, slide } from 'svelte/transition';
    import type { QuestionShowState } from '@/states/Mahasiswa/QuizState.svelte.ts';

    interface Props {
        quizState: QuestionShowState;
        showDebug?: boolean;
    }

    let { quizState, showDebug = false }: Props = $props();

    let isDebugPanelCollapsed = $state(true);
    let activeTab = $state<'overview' | 'chaining' | 'state' | 'raw'>('overview');

    function toggleDebugCollapse() {
        isDebugPanelCollapsed = !isDebugPanelCollapsed;
    }

    const metadata = $derived(quizState.feedbackData?.adaptive_result?.engine_metadata);
    const factCodes = $derived(quizState.adaptiveFacts || []);
    const triggeredRule = $derived(quizState.adaptiveTriggeredRule);
    const evaluatedRules = $derived(quizState.adaptiveTriggeredRules || []);
    const newState = $derived(quizState.feedbackData?.adaptive_result?.new_state);

    const factCategories = $derived.by(() => {
        if (!metadata) return { primary: [], virtual: [] };

        const groups: { primary: string[]; virtual: string[] } = { primary: [], virtual: [] };
        factCodes.forEach((code) => {
            // Assume codes starting with V are virtual (inferred diagnoses), others are primary observations
            const cat = code.startsWith('V') ? 'virtual' : 'primary';
            groups[cat].push(code);
        });
        return groups;
    });

    function getFactLabel(factCode: string) {
        // Strip out values like G01:85 to just get the base code if needed,
        // though the engine now sends exact codes like G01.
        return metadata?.fact_labels?.[factCode] || factCode;
    }

    const stateChanges = $derived.by(() => {
        if (!newState) return [];

        const keys = [
            'accuracy',
            'xp',
            'streak',
            'target_difficulty',
            'stagnant_count',
            'hints_available',
        ];
        const stateObj = newState as Record<string, any>;

        return keys
            .map((key) => ({
                key,
                value: stateObj[key],
            }))
            .filter((item) => item.value !== undefined && item.value !== null);
    });
</script>

<style>
    .debug-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .debug-scrollbar::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 0.5);
        border-radius: 4px;
    }
    .debug-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(51, 65, 85, 0.8);
        border-radius: 4px;
    }
    .debug-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(71, 85, 105, 1);
    }

    .code-block {
        font-family:
            ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New',
            monospace;
    }
</style>

{#if showDebug}
    <div
        class="fixed right-4 bottom-4 left-4 z-1001 lg:right-6 lg:left-auto lg:w-[580px]"
        transition:scale={{ duration: 400, start: 0.95, opacity: 0 }}
    >
        <div
            class="overflow-hidden rounded-2xl border border-slate-700/60 bg-slate-900/95 shadow-2xl ring-1 ring-white/10 backdrop-blur-2xl transition-all duration-500"
        >
            <!-- Header Bar -->
            <button
                class="flex w-full cursor-pointer items-center justify-between border-b border-slate-800/80 px-5 py-3.5 transition-colors hover:bg-slate-800/50 focus:outline-none"
                onclick={toggleDebugCollapse}
            >
                <div class="flex items-center gap-3.5">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-linear-to-br from-indigo-500 to-purple-600 shadow-inner ring-1 ring-white/20"
                        class:animate-pulse={quizState.isProcessing}
                    >
                        <Cpu size={20} class="text-white" />
                    </div>
                    <div class="text-left">
                        <div class="flex items-center gap-2.5">
                            <span class="text-[13px] font-black tracking-wide text-slate-100"
                                >ENGINE MONITOR</span
                            >
                            {#if isDebugPanelCollapsed}
                                <div class="flex gap-1.5" in:fade>
                                    <div
                                        class="flex items-center gap-1 rounded-md bg-slate-800 px-1.5 py-0.5 ring-1 ring-slate-700"
                                    >
                                        <Database size={10} class="text-indigo-400" />
                                        <span class="text-[9px] font-bold text-slate-300"
                                            >{factCodes.length} Facts</span
                                        >
                                    </div>
                                    {#if triggeredRule}
                                        <div
                                            class="flex items-center gap-1 rounded-md bg-emerald-900/40 px-1.5 py-0.5 ring-1 ring-emerald-500/30"
                                        >
                                            <Zap size={10} class="text-emerald-400" />
                                            <span class="text-[9px] font-bold text-emerald-300"
                                                >Rule Hit</span
                                            >
                                        </div>
                                    {/if}
                                </div>
                            {/if}
                        </div>
                        <div
                            class="mt-0.5 flex items-center gap-2 text-[10px] font-medium tracking-widest text-slate-400 uppercase"
                        >
                            <span class="flex items-center gap-1">
                                <Clock size={10} />
                                {quizState.isProcessing ? 'EVALUATING...' : 'IDLE'}
                            </span>
                            <span class="h-1 w-1 rounded-full bg-slate-600"></span>
                            <span
                                >{metadata?.engine_version
                                    ? `v${metadata.engine_version}`
                                    : 'Scanning...'}</span
                            >
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 text-slate-400">
                    {#if quizState.isProcessing}
                        <Activity size={16} class="animate-spin text-indigo-400" />
                    {/if}
                    <div
                        class="transition-transform duration-300 hover:text-slate-200"
                        class:rotate-180={!isDebugPanelCollapsed}
                    >
                        <ChevronUp size={20} />
                    </div>
                </div>
            </button>

            {#if !isDebugPanelCollapsed}
                <div transition:slide={{ duration: 400 }} class="flex flex-col">
                    <!-- Tabs Navigation -->
                    <div class="flex gap-1 border-b border-slate-800 bg-slate-900/50 px-3 py-2">
                        {#each [{ id: 'overview', label: 'Overview', icon: Brain }, { id: 'chaining', label: 'Chaining', icon: GitBranch }, { id: 'state', label: 'State', icon: TrendingUp }, { id: 'raw', label: 'Raw I/O', icon: Terminal }] as Tab}
                            <button
                                class="relative flex flex-1 items-center justify-center gap-1.5 rounded-lg px-2 py-1.5 text-[10px] font-bold tracking-widest uppercase transition-all duration-200 {activeTab ===
                                Tab.id
                                    ? 'bg-slate-800 text-indigo-300 shadow-sm'
                                    : 'text-slate-500 hover:bg-slate-800/50 hover:text-slate-300'}"
                                onclick={() => (activeTab = Tab.id as any)}
                            >
                                <Tab.icon
                                    size={12}
                                    class={activeTab === Tab.id ? 'text-indigo-400' : 'opacity-70'}
                                />
                                {Tab.label}
                            </button>
                        {/each}
                    </div>

                    <div class="debug-scrollbar h-[380px] overflow-y-auto bg-[#0b1120] p-5">
                        {#if activeTab === 'overview'}
                            <div in:fade={{ duration: 200 }} class="space-y-6">
                                <!-- Final Decision Panel -->
                                <section>
                                    <h6
                                        class="mb-3 flex items-center gap-2 text-[10px] font-black tracking-widest text-slate-500 uppercase"
                                    >
                                        <CheckCircle size={12} class="text-emerald-500" /> Final Decision
                                    </h6>

                                    {#if triggeredRule}
                                        <div
                                            class="relative overflow-hidden rounded-xl border border-indigo-500/20 bg-linear-to-br from-indigo-900/20 to-slate-900 p-4 shadow-lg"
                                        >
                                            <div
                                                class="absolute top-0 right-0 h-full w-1 bg-indigo-500"
                                            ></div>
                                            <div class="flex items-start gap-4">
                                                <div
                                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-500/10 ring-1 ring-indigo-500/30"
                                                >
                                                    <Zap size={18} class="text-indigo-400" />
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex items-center justify-between">
                                                        <h4
                                                            class="text-[13px] font-bold text-indigo-100"
                                                        >
                                                            {triggeredRule?.name || 'Unknown Rule'}
                                                        </h4>
                                                        <Badge
                                                            variant="primary"
                                                            size="xs"
                                                            class="border-indigo-500/30 bg-indigo-500/20 py-0.5 text-indigo-300"
                                                        >
                                                            P{triggeredRule.priority}
                                                        </Badge>
                                                    </div>

                                                    <div class="mt-2 grid grid-cols-2 gap-2">
                                                        <div
                                                            class="rounded-lg bg-slate-800/50 p-2 ring-1 ring-slate-700/50"
                                                        >
                                                            <span
                                                                class="block text-[8px] font-black tracking-widest text-slate-500 uppercase"
                                                                >Action Code</span
                                                            >
                                                            <span
                                                                class="mt-0.5 font-mono text-[10px] text-amber-300"
                                                                >{triggeredRule.action ||
                                                                    'DEFAULT'}</span
                                                            >
                                                        </div>
                                                        <div
                                                            class="rounded-lg bg-slate-800/50 p-2 ring-1 ring-slate-700/50"
                                                        >
                                                            <span
                                                                class="block text-[8px] font-black tracking-widest text-slate-500 uppercase"
                                                                >Rule ID</span
                                                            >
                                                            <span
                                                                class="mt-0.5 font-mono text-[10px] text-slate-300"
                                                                >{triggeredRule.id}</span
                                                            >
                                                        </div>
                                                    </div>

                                                    {#if quizState.feedbackData?.message}
                                                        <div
                                                            class="mt-3 flex gap-2.5 rounded-lg bg-emerald-900/20 p-3 ring-1 ring-emerald-500/20"
                                                        >
                                                            <MessageSquare
                                                                size={14}
                                                                class="mt-0.5 shrink-0 text-emerald-400"
                                                            />
                                                            <p
                                                                class="text-[11px] leading-relaxed text-emerald-100/90 italic"
                                                            >
                                                                "{quizState.feedbackData.message}"
                                                            </p>
                                                        </div>
                                                    {/if}
                                                </div>
                                            </div>
                                        </div>
                                    {:else}
                                        <div
                                            class="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-700 bg-slate-800/30 p-6 text-center"
                                        >
                                            <Info size={20} class="mb-2 text-slate-500" />
                                            <span class="text-xs font-bold text-slate-300"
                                                >No Rule Triggered</span
                                            >
                                            <span class="mt-1 text-[10px] text-slate-500"
                                                >System fell back to default linear progression.</span
                                            >
                                        </div>
                                    {/if}
                                </section>

                                <!-- Facts Working Memory -->
                                <section>
                                    <h6
                                        class="mb-3 flex items-center gap-2 text-[10px] font-black tracking-widest text-slate-500 uppercase"
                                    >
                                        <Layers size={12} class="text-blue-400" /> Working Memory ({factCodes.length})
                                    </h6>

                                    <div
                                        class="space-y-3 rounded-xl border border-slate-800 bg-slate-900 p-3"
                                    >
                                        <!-- Primary Facts (Observations) -->
                                        <div>
                                            <span
                                                class="mb-2 block text-[9px] font-black tracking-widest text-slate-500 uppercase"
                                                >Raw Observations (G-Codes)</span
                                            >
                                            <div class="flex flex-wrap gap-1.5">
                                                {#each factCategories.primary || [] as code}
                                                    <div
                                                        class="flex items-center overflow-hidden rounded-md border border-slate-700 bg-slate-800 text-[10px]"
                                                    >
                                                        <span
                                                            class="bg-slate-700 px-1.5 py-0.5 font-mono font-bold text-slate-300"
                                                            >{code}</span
                                                        >
                                                        <span
                                                            class="px-2 py-0.5 font-medium text-slate-400"
                                                            >{getFactLabel(code)}</span
                                                        >
                                                    </div>
                                                {:else}
                                                    <span class="text-[10px] text-slate-600 italic"
                                                        >No primary facts collected.</span
                                                    >
                                                {/each}
                                            </div>
                                        </div>

                                        <!-- Virtual Facts (Inferences) -->
                                        <div class="border-t border-slate-800/80 pt-2">
                                            <span
                                                class="mb-2 block text-[9px] font-black tracking-widest text-slate-500 uppercase"
                                                >Inferred Diagnoses (V-Codes)</span
                                            >
                                            <div class="flex flex-wrap gap-1.5">
                                                {#each factCategories.virtual || [] as code}
                                                    <div
                                                        class="flex items-center overflow-hidden rounded-md border border-indigo-900 bg-indigo-950 text-[10px] shadow-[0_0_10px_rgba(79,70,229,0.1)]"
                                                    >
                                                        <span
                                                            class="bg-indigo-900 px-1.5 py-0.5 font-mono font-bold text-indigo-300"
                                                            >{code}</span
                                                        >
                                                        <span
                                                            class="px-2 py-0.5 font-medium text-indigo-200"
                                                            >{getFactLabel(code)}</span
                                                        >
                                                    </div>
                                                {:else}
                                                    <span class="text-[10px] text-slate-600 italic"
                                                        >No inferences made yet.</span
                                                    >
                                                {/each}
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        {:else if activeTab === 'chaining'}
                            <div in:fade={{ duration: 200 }}>
                                <h6
                                    class="mb-4 flex items-center gap-2 text-[10px] font-black tracking-widest text-slate-500 uppercase"
                                >
                                    <GitBranch size={12} class="text-purple-400" /> Evaluation Trace
                                </h6>

                                <div class="relative pl-3">
                                    <!-- Vertical Line -->
                                    <div
                                        class="absolute top-2 bottom-2 left-4 w-px bg-slate-800"
                                    ></div>

                                    <div class="relative space-y-4">
                                        <!-- Show evaluated rules if array exists -->
                                        {#each evaluatedRules as rule}
                                            <div class="flex gap-4">
                                                <div
                                                    class="relative z-10 mt-1 flex h-3 w-3 shrink-0 items-center justify-center rounded-full border-2 border-slate-900 {rule.id ===
                                                    triggeredRule?.id
                                                        ? 'bg-emerald-500 ring-2 ring-emerald-500/30'
                                                        : 'bg-slate-600'}"
                                                ></div>
                                                <div
                                                    class="flex-1 rounded-lg border border-slate-800 bg-slate-800/40 p-2.5 transition-all hover:bg-slate-800"
                                                >
                                                    <div class="flex items-center justify-between">
                                                        <span
                                                            class="text-xs font-bold {rule.id ===
                                                            triggeredRule?.id
                                                                ? 'text-emerald-400'
                                                                : 'text-slate-300'}"
                                                            >{rule.name}</span
                                                        >
                                                        <span
                                                            class="font-mono text-[9px] text-slate-500"
                                                            >{rule.id}</span
                                                        >
                                                    </div>
                                                    {#if rule.id === triggeredRule?.id}
                                                        <p
                                                            class="mt-1 text-[10px] text-emerald-500/80"
                                                        >
                                                            ➔ Conditions met. Action executed.
                                                        </p>
                                                    {:else}
                                                        <p class="mt-1 text-[10px] text-slate-500">
                                                            ⨯ Conditions not met. Skipped.
                                                        </p>
                                                    {/if}
                                                </div>
                                            </div>
                                        {/each}

                                        <!-- If array is empty but we have a triggered rule (fallback) -->
                                        {#if evaluatedRules.length === 0 && triggeredRule}
                                            <div class="flex gap-4">
                                                <div
                                                    class="relative z-10 mt-1 flex h-3 w-3 shrink-0 items-center justify-center rounded-full border-2 border-slate-900 bg-emerald-500 ring-2 ring-emerald-500/30"
                                                ></div>
                                                <div
                                                    class="flex-1 rounded-lg border border-emerald-900/30 bg-emerald-900/10 p-2.5"
                                                >
                                                    <div class="flex items-center justify-between">
                                                        <span
                                                            class="text-xs font-bold text-emerald-400"
                                                            >{triggeredRule.name}</span
                                                        >
                                                        <span
                                                            class="font-mono text-[9px] text-slate-500"
                                                            >{triggeredRule.id}</span
                                                        >
                                                    </div>
                                                    <p class="mt-1 text-[10px] text-emerald-500/80">
                                                        ➔ Single rule trigger recorded.
                                                    </p>
                                                </div>
                                            </div>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        {:else if activeTab === 'state'}
                            <div in:fade={{ duration: 200 }}>
                                <h6
                                    class="mb-4 flex items-center justify-between text-[10px] font-black tracking-widest text-slate-500 uppercase"
                                >
                                    <div class="flex items-center gap-2">
                                        <Activity size={12} class="text-amber-400" /> State Mutations
                                    </div>
                                    <Badge
                                        variant="primary"
                                        size="xs"
                                        class="border-slate-700 bg-slate-800 text-slate-400"
                                        >POST-EVALUATION</Badge
                                    >
                                </h6>

                                {#if stateChanges.length > 0}
                                    <div class="grid grid-cols-2 gap-3">
                                        {#each stateChanges as item}
                                            <div
                                                class="flex flex-col rounded-xl border border-slate-800 bg-slate-800/40 p-3.5 transition-colors hover:border-slate-700 hover:bg-slate-800"
                                            >
                                                <span
                                                    class="text-[9px] font-black tracking-widest text-slate-500 uppercase"
                                                >
                                                    {item.key.replace(/_/g, ' ')}
                                                </span>
                                                <span
                                                    class="mt-1 font-mono text-sm font-bold text-slate-200"
                                                >
                                                    {#if typeof item.value === 'boolean'}
                                                        <span
                                                            class={item.value
                                                                ? 'text-emerald-400'
                                                                : 'text-rose-400'}
                                                            >{item.value ? 'TRUE' : 'FALSE'}</span
                                                        >
                                                    {:else if typeof item.value === 'number' && item.key === 'accuracy'}
                                                        <span
                                                            class={item.value >= 80
                                                                ? 'text-emerald-400'
                                                                : item.value < 50
                                                                  ? 'text-rose-400'
                                                                  : 'text-amber-400'}
                                                            >{item.value}%</span
                                                        >
                                                    {:else}
                                                        {item.value}
                                                    {/if}
                                                </span>
                                            </div>
                                        {/each}
                                    </div>
                                {:else}
                                    <div
                                        class="rounded-xl border border-dashed border-slate-700 bg-slate-800/30 p-6 text-center"
                                    >
                                        <span class="text-xs text-slate-400"
                                            >No significant state mutations recorded.</span
                                        >
                                    </div>
                                {/if}
                            </div>
                        {:else if activeTab === 'raw'}
                            <div in:fade={{ duration: 200 }} class="flex h-full flex-col">
                                <h6
                                    class="mb-3 flex shrink-0 items-center gap-2 text-[10px] font-black tracking-widest text-slate-500 uppercase"
                                >
                                    <Terminal size={12} class="text-emerald-500" /> API Payload
                                </h6>

                                <div
                                    class="flex flex-1 flex-col overflow-hidden rounded-xl border border-slate-800 bg-[#0f172a]"
                                >
                                    <div
                                        class="flex shrink-0 items-center justify-between border-b border-slate-800 bg-slate-900 px-3 py-1.5"
                                    >
                                        <div class="flex gap-1.5">
                                            <div
                                                class="h-2.5 w-2.5 rounded-full bg-rose-500/80"
                                            ></div>
                                            <div
                                                class="h-2.5 w-2.5 rounded-full bg-amber-500/80"
                                            ></div>
                                            <div
                                                class="h-2.5 w-2.5 rounded-full bg-emerald-500/80"
                                            ></div>
                                        </div>
                                        <span class="font-mono text-[9px] text-slate-500"
                                            >adaptive_response.json</span
                                        >
                                    </div>
                                    <div class="debug-scrollbar flex-1 overflow-auto p-3">
                                        <pre
                                            class="code-block text-[11px] leading-relaxed break-all whitespace-pre-wrap text-emerald-400/90">{JSON.stringify(
                                                quizState.feedbackData?.adaptive_result || {},
                                                null,
                                                2
                                            )}</pre>
                                    </div>
                                </div>
                            </div>
                        {/if}
                    </div>

                    <!-- Footer Info -->
                    <div
                        class="flex items-center justify-between border-t border-slate-800 bg-slate-900 px-5 py-3"
                    >
                        <div
                            class="flex items-center gap-3 text-[10px] font-bold tracking-tighter text-slate-500 uppercase"
                        >
                            <span class="flex items-center gap-1.5">
                                <Database size={10} class="text-indigo-500" /> Fact Storage
                            </span>
                            <span class="h-1 w-1 rounded-full bg-slate-700"></span>
                            <span>{metadata?.rule_count || 0} Rules</span>
                        </div>
                        <div
                            class="flex items-center gap-2 rounded-full bg-emerald-500/10 px-2 py-1 ring-1 ring-emerald-500/20"
                        >
                            <div class="relative flex h-1.5 w-1.5 items-center justify-center">
                                <div
                                    class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"
                                ></div>
                                <div
                                    class="relative inline-flex h-1.5 w-1.5 rounded-full bg-emerald-500"
                                ></div>
                            </div>
                            <span class="text-[9px] font-black tracking-widest text-emerald-400"
                                >ENGINE ACTIVE</span
                            >
                        </div>
                    </div>
                </div>
            {/if}
        </div>
    </div>
{/if}
