<script lang="ts">
    import Badge from '@/components/ui/Badge.svelte';
    import Panel from '@/components/ui/Panel.svelte';
    import Card from '@/components/ui/Card.svelte';
    import {
        Brain,
        Zap,
        Target,
        CheckCircle,
        ChevronUp,
        ChevronDown,
        ArrowRight,
        MessageSquare,
    } from 'lucide-svelte';
    import { fade, scale, slide } from 'svelte/transition';
    import type { QuestionShowState } from '@/states/Mahasiswa/QuizState.svelte.ts';

    interface Props {
        quizState: QuestionShowState;
        showDebug?: boolean;
    }

    let { quizState, showDebug = false }: Props = $props();

    let isDebugPanelCollapsed = $state(true);

    function toggleDebugCollapse() {
        isDebugPanelCollapsed = !isDebugPanelCollapsed;
    }

    const factCodes = $derived(quizState.adaptiveFacts as string[]);

    const factCategories = $derived({
        performance: factCodes.filter((f) => ['G01', 'G02', 'G03', 'G04', 'G05', 'G21'].includes(f)),
        style: factCodes.filter((f) => ['G06', 'G07', 'G22'].includes(f)),
        error: factCodes.filter((f) => ['G08', 'G09', 'G10', 'G20'].includes(f)),
        difficulty: factCodes.filter((f) => ['G13', 'G14', 'G15', 'G16'].includes(f)),
        quiz: factCodes.filter((f) => ['G11', 'G12', 'G17', 'G18', 'G19'].includes(f)),
    });

    function getFactLabel(factCode: string) {
        const labels: Record<string, string> = {
            G01: 'Critical Score',
            G02: 'Remedial Score',
            G03: 'Standard Score',
            G04: 'Mastery Score',
            G05: 'Fast Response',
            G06: 'Visual Learner',
            G07: 'Textual Learner',
            G08: 'Syntax Error',
            G09: 'Logic Error',
            G10: 'No Error',
            G11: 'Used Hint',
            G12: 'In Module',
            G13: 'Beginner Level',
            G14: 'Medium Level',
            G15: 'Hard Level',
            G16: 'Final Project',
            G17: 'Practice Mode',
            G18: 'Next Unlocked',
            G19: 'Prev Unlocked',
            G20: 'Persistent Fail',
            G21: 'Sat. Progress',
            G22: 'Mixed Learner',
        };
        return labels[factCode] || factCode;
    }

    function getCategoryLabel(category: string) {
        const labels: Record<string, string> = {
            score: 'Skor',
            time: 'Waktu',
            style: 'Gaya Belajar',
            error: 'Tipe Error',
            hint: 'Bantuan',
            module: 'Modul',
            difficulty: 'Kesulitan',
            status: 'Status',
        };
        return labels[category] || category;
    }
</script>

{#if showDebug}
    <div
        class="fixed right-0 bottom-0 left-0 z-[1001]"
        transition:scale={{ duration: 300, start: 0.95 }}
    >
        <Panel
            variant="none"
            rounded="none"
            padding="p-0"
            class="border-t border-slate-200 bg-white shadow-2xl transition-all duration-300"
        >
            <button
                class="bg-primary-600 hover:bg-primary-700 flex w-full cursor-pointer items-center justify-between px-6 py-2 text-white transition-all focus:outline-none"
                onclick={toggleDebugCollapse}
            >
                <div class="flex items-center gap-4">
                    <div
                        class={`flex h-8 w-8 items-center justify-center rounded-lg bg-white/20 backdrop-blur-md ${quizState.isProcessing ? 'animate-pulse' : ''}`}
                    >
                        <Brain size={18} class="text-white" />
                    </div>
                    <div class="text-left">
                        <h3 class="flex items-center gap-2 text-xs font-bold tracking-wide">
                            Adaptive Debug Panel
                            {#if isDebugPanelCollapsed}
                                <Badge
                                    variant="secondary"
                                    size="sm"
                                    class="text-xs"
                                >
                                    {quizState.adaptiveFacts.length} Facts • {quizState.adaptiveTriggeredRule
                                        ? 'Rule Active'
                                        : 'No Rule'}
                                </Badge>
                            {/if}
                        </h3>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    {#if quizState.isProcessing}
                        <div class="flex gap-1">
                            <span
                                class="h-1.5 w-1.5 animate-bounce rounded-full bg-white"
                                style="animation-delay: 0ms;"
                            ></span>
                            <span
                                class="h-1.5 w-1.5 animate-bounce rounded-full bg-white"
                                style="animation-delay: 150ms;"
                            ></span>
                            <span
                                class="h-1.5 w-1.5 animate-bounce rounded-full bg-white"
                                style="animation-delay: 300ms;"
                            ></span>
                        </div>
                    {/if}
                    {#if isDebugPanelCollapsed}
                        <ChevronUp size={20} />
                    {:else}
                        <ChevronDown size={20} />
                    {/if}
                </div>
            </button>

            {#if !isDebugPanelCollapsed}
                <div transition:slide={{ duration: 300 }}>
                    <div class="mx-auto max-w-7xl">
                        <div
                            class="grid grid-cols-1 gap-4 border-b border-slate-100 p-4 lg:grid-cols-2"
                        >
                            <Card
                                variant="none"
                                padding="p-4"
                                class="border border-slate-200 bg-slate-50"
                            >
                                <div
                                    class="mb-3 flex items-center justify-between text-xs font-bold tracking-wider text-slate-500 uppercase"
                                >
                                    <span>Facts Gathered ({quizState.adaptiveFacts.length})</span>
                                    <span class="font-medium text-slate-400 normal-case"
                                        >Auto-extracted from user state & performance</span
                                    >
                                </div>

                                <div
                                    class="custom-scrollbar max-h-48 space-y-3 overflow-y-auto pr-2"
                                >
                                    {#each Object.entries(factCategories) as [category, categoryFacts] (category)}
                                        {#if (categoryFacts as string[]).length > 0}
                                            <div transition:fade={{ duration: 200 }}>
                                                <div
                                                    class="mb-1.5 flex items-center gap-2 text-xs font-bold text-slate-400 uppercase"
                                                >
                                                    <span
                                                        class="h-1.5 w-1.5 rounded-full bg-slate-300"
                                                    ></span>
                                                    {getCategoryLabel(category)}
                                                </div>
                                                <div class="flex flex-wrap gap-1.5">
                                                    {#each categoryFacts as string[] as fact (fact)}
                                                        <Badge
                                                            variant="primary"
                                                            size="sm"
                                                            class="font-mono text-xs"
                                                        >
                                                            {fact} - {getFactLabel(fact as string)}
                                                        </Badge>
                                                    {/each}
                                                </div>
                                            </div>
                                        {/if}
                                    {/each}

                                    {#if quizState.adaptiveFacts.length === 0}
                                        <div class="py-4 text-center text-xs text-slate-400 italic">
                                            No facts gathered for this session yet.
                                        </div>
                                    {/if}
                                </div>
                            </Card>

                            <div class="flex flex-col">
                                <div
                                    class="mb-3 text-xs font-bold tracking-wider text-slate-500 uppercase"
                                >
                                    Rule Execution Status
                                </div>

                                {#if quizState.adaptiveTriggeredRule}
                                    {@const newState =
                                        quizState.feedbackData?.adaptiveResult?.new_state}
                                    <Panel
                                        variant="none"
                                        rounded="xl"
                                        padding="p-5"
                                        class="flex flex-1 flex-col justify-center border border-emerald-200 bg-emerald-50"
                                    >
                                        <div
                                            class="flex items-start gap-4"
                                            transition:fade={{ duration: 300 }}
                                        >
                                            <div
                                                class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-100 shadow-sm"
                                            >
                                                <CheckCircle size={20} class="text-emerald-600" />
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div
                                                    class="mb-1 text-xs font-black tracking-tighter text-emerald-600 uppercase"
                                                >
                                                    Rule Successfully Triggered
                                                </div>
                                                <div
                                                    class="mb-2 text-base leading-tight font-bold text-slate-800"
                                                >
                                                    {quizState.adaptiveTriggeredRule.name}
                                                </div>
                                                <div
                                                    class="mb-3 flex flex-wrap items-center gap-2 text-xs"
                                                >
                                                    <Badge
                                                        variant="success"
                                                        size="xs"
                                                        class="font-mono lowercase"
                                                    >
                                                        id: {quizState.adaptiveTriggeredRule.id}
                                                    </Badge>
                                                    <Badge
                                                        variant="primary"
                                                        size="xs"
                                                        class="font-mono lowercase"
                                                    >
                                                        action: {quizState.adaptiveTriggeredRule
                                                            .action}
                                                    </Badge>
                                                    <Badge
                                                        variant="secondary"
                                                        size="xs"
                                                        class="text-xs"
                                                    >
                                                        PRIORITY: {quizState.adaptiveTriggeredRule
                                                            .priority}
                                                    </Badge>
                                                    {#if newState?.next_action_data?.label}
                                                        <Badge
                                                            variant="info"
                                                            size="xs"
                                                            class="flex items-center gap-1 text-xs"
                                                        >
                                                            <ArrowRight size={8} />
                                                            {newState.next_action_data.label}
                                                            {#if newState.next_action_data.type}
                                                                <span class="opacity-60"
                                                                    >({newState.next_action_data
                                                                        .type})</span
                                                                >
                                                            {/if}
                                                        </Badge>
                                                    {/if}
                                                </div>

                                                {#if quizState.feedbackData?.message}
                                                    <div
                                                        class="mb-2 flex items-start gap-1.5 rounded-lg bg-emerald-100/60 px-3 py-2"
                                                    >
                                                        <MessageSquare
                                                            size={11}
                                                            class="mt-0.5 flex-shrink-0 text-emerald-600"
                                                        />
                                                        <p
                                                            class="text-xs leading-snug text-slate-700 italic"
                                                        >
                                                            "{quizState.feedbackData.message}"
                                                        </p>
                                                    </div>
                                                {/if}

                                                {#if newState?.recommendation || newState?.intervention_type || newState?.recovery_type}
                                                    <div class="flex flex-wrap gap-1.5">
                                                        {#if newState?.recommendation}
                                                            <Badge
                                                                variant="warning"
                                                                size="xs"
                                                                class="text-xs"
                                                            >
                                                                rec: {newState.recommendation}
                                                            </Badge>
                                                        {/if}
                                                        {#if newState?.intervention_type}
                                                            <Badge
                                                                variant="danger"
                                                                size="xs"
                                                                class="font-mono text-xs"
                                                            >
                                                                intervention: {newState.intervention_type}
                                                            </Badge>
                                                        {/if}
                                                        {#if newState?.recovery_type}
                                                            <Badge
                                                                variant="warning"
                                                                size="xs"
                                                                class="font-mono text-xs"
                                                            >
                                                                recovery: {newState.recovery_type}
                                                            </Badge>
                                                        {/if}
                                                    </div>
                                                {/if}

                                                {#if quizState.adaptiveTriggeredRules.length > 1}
                                                    <div
                                                        class="mt-4 border-t border-emerald-100 pt-3"
                                                    >
                                                        <div
                                                            class="mb-2 text-[10px] font-bold tracking-widest text-emerald-600 uppercase"
                                                        >
                                                            Other Matched Rules (Shadowed)
                                                        </div>
                                                        <div class="flex flex-wrap gap-1.5">
                                                            {#each quizState.adaptiveTriggeredRules.slice(1) as rule}
                                                                <Badge
                                                                    variant="outline"
                                                                    size="xs"
                                                                    class="opacity-60 transition-all hover:opacity-100"
                                                                >
                                                                    {rule.name}
                                                                    <span class="ml-1 opacity-50"
                                                                        >({rule.priority})</span
                                                                    >
                                                                </Badge>
                                                            {/each}
                                                        </div>
                                                    </div>
                                                {/if}
                                            </div>
                                        </div>
                                    </Panel>
                                {:else}
                                    <div
                                        class="flex flex-1 flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center"
                                    >
                                        <div
                                            class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100"
                                        >
                                            <Zap size={20} class="text-slate-300" />
                                        </div>
                                        <p
                                            class="text-xs font-bold tracking-widest text-slate-400 uppercase"
                                        >
                                            Awaiting Engine Inference
                                        </p>
                                        <p class="mt-1 max-w-[200px] text-xs text-slate-400">
                                            Data is being processed using Forward Chaining matching
                                            strategy.
                                        </p>
                                    </div>
                                {/if}
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-between border-t border-slate-50 bg-white px-6 py-2 text-xs font-bold tracking-widest text-slate-400 uppercase"
                    >
                        <div class="flex items-center gap-4">
                            <span class="flex items-center gap-1"
                                ><Zap size={10} /> Forward Chaining</span
                            >
                            <span class="flex items-center gap-1 text-slate-300">•</span>
                            <span class="flex items-center gap-1"
                                ><Target size={10} /> First Match Conflict Resolution</span
                            >
                        </div>
                        <div class="text-primary-400">30 Rules • Adaptive Engine v3.0.0-REFECTORED • Stable</div>
                    </div>
                </div>
            {/if}
        </Panel>
    </div>
{/if}
