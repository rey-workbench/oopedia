<script>
    import Badge from "@/components/ui/Badge.svelte";
    import {
        Brain,
        Zap,
        Target,
        CheckCircle,
        ChevronUp,
        ChevronDown,
    } from "lucide-svelte";
    import { fade, scale, slide } from "svelte/transition";

    let { quizState, showDebug = false } = $props();

    let isDebugPanelCollapsed = $state(true);

    function toggleDebugCollapse() {
        isDebugPanelCollapsed = !isDebugPanelCollapsed;
    }

    const factCategories = $derived({
        score: quizState.adaptiveFacts.filter((f) =>
            ["G01", "G02", "G03", "G04"].includes(f),
        ),
        time: quizState.adaptiveFacts.filter((f) => ["G05", "G06"].includes(f)),
        style: quizState.adaptiveFacts.filter((f) =>
            ["G07", "G08"].includes(f),
        ),
        error: quizState.adaptiveFacts.filter((f) =>
            ["G09", "G10"].includes(f),
        ),
        hint: quizState.adaptiveFacts.filter((f) => ["G11", "G12"].includes(f)),
        module: quizState.adaptiveFacts.filter((f) =>
            ["G13", "G14", "G23", "G24", "G25"].includes(f),
        ),
        difficulty: quizState.adaptiveFacts.filter((f) =>
            ["G15", "G16", "G17", "G18"].includes(f),
        ),
        status: quizState.adaptiveFacts.filter((f) =>
            ["G19", "G20", "G21", "G22", "G26"].includes(f),
        ),
    });

    function getFactLabel(factCode) {
        const labels = {
            G01: "Critical (<40)",
            G02: "Remedial (40-69)",
            G03: "Standard (70-89)",
            G04: "Mastery (≥90)",
            G05: "Fast (<50%)",
            G06: "Normal (≥50%)",
            G07: "Visual Learner",
            G08: "Textual Learner",
            G09: "Syntax Error",
            G10: "Logic Error",
            G11: "No Hint",
            G12: "Used Hint",
            G13: "Module 1: Foundation",
            G14: "Module 2: Encapsulation",
            G23: "Module 3: Inheritance",
            G24: "Module 4: Polymorphism",
            G25: "Module 5: Abstraction",
            G15: "Easy Level",
            G16: "Medium Level",
            G17: "Advanced Level",
            G18: "Final Project",
            G19: "Next Locked",
            G20: "Next Unlocked",
            G21: "Prev Unlocked",
            G22: "Persistent Fail (≥3x)",
            G26: "Satisfactory Progress (≥60%)",
        };
        return labels[factCode] || factCode;
    }

    function getCategoryLabel(category) {
        const labels = {
            score: "Skor",
            time: "Waktu",
            style: "Gaya Belajar",
            error: "Tipe Error",
            hint: "Bantuan",
            module: "Modul",
            difficulty: "Kesulitan",
            status: "Status",
        };
        return labels[category] || category;
    }
</script>

{#if showDebug}
    <div
        class="fixed bottom-0 left-0 right-0 z-[110] border-t border-slate-200 bg-white shadow-2xl transition-all duration-300"
        transition:scale={{ duration: 300, start: 0.95 }}
    >
        <button
            class="w-full bg-primary-600 px-6 py-2 text-white flex items-center justify-between hover:bg-primary-700 transition-all cursor-pointer"
            onclick={toggleDebugCollapse}
        >
            <div class="flex items-center gap-4">
                <div
                    class={`w-8 h-8 rounded-lg bg-white/20 backdrop-blur-md flex items-center justify-center ${quizState.isProcessing ? "animate-pulse" : ""}`}
                >
                    <Brain size={18} class="text-white" />
                </div>
                <div class="text-left">
                    <h3
                        class="text-xs font-bold tracking-wide flex items-center gap-2"
                    >
                        Adaptive Debug Panel
                        {#if isDebugPanelCollapsed}
                            <Badge
                                variant="secondary"
                                size="sm"
                                class="bg-white/20 text-white border-none text-[10px]"
                            >
                                {quizState.adaptiveFacts.length} Facts • {quizState.adaptiveTriggeredRule
                                    ? "Rule Active"
                                    : "No Rule"}
                            </Badge>
                        {/if}
                    </h3>
                </div>
            </div>

            <div class="flex items-center gap-4">
                {#if quizState.isProcessing}
                    <div class="flex gap-1">
                        <span
                            class="w-1.5 h-1.5 bg-white rounded-full animate-bounce"
                            style="animation-delay: 0ms;"
                        ></span>
                        <span
                            class="w-1.5 h-1.5 bg-white rounded-full animate-bounce"
                            style="animation-delay: 150ms;"
                        ></span>
                        <span
                            class="w-1.5 h-1.5 bg-white rounded-full animate-bounce"
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
                <div class="max-w-7xl mx-auto">
                    <div
                        class="grid grid-cols-1 lg:grid-cols-2 gap-4 p-4 border-b border-slate-100"
                    >
                        <div
                            class="bg-slate-50 p-4 rounded-xl border border-slate-200"
                        >
                            <div
                                class="text-[10px] font-bold text-slate-500 mb-3 uppercase tracking-wider flex items-center justify-between"
                            >
                                <span
                                    >Facts Gathered ({quizState.adaptiveFacts
                                        .length})</span
                                >
                                <span
                                    class="text-slate-400 normal-case font-medium"
                                    >Auto-extracted from user state &
                                    performance</span
                                >
                            </div>

                            <div
                                class="space-y-3 max-h-48 overflow-y-auto pr-2 custom-scrollbar"
                            >
                                {#each Object.entries(factCategories) as [category, categoryFacts] (category)}
                                    {#if categoryFacts.length > 0}
                                        <div
                                            transition:fade={{ duration: 200 }}
                                        >
                                            <div
                                                class="text-[9px] font-bold text-slate-400 uppercase mb-1.5 flex items-center gap-2"
                                            >
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full bg-slate-300"
                                                ></span>
                                                {getCategoryLabel(category)}
                                            </div>
                                            <div class="flex flex-wrap gap-1.5">
                                                {#each categoryFacts as fact (fact)}
                                                    <Badge
                                                        variant="secondary"
                                                        size="sm"
                                                        class="text-[10px] bg-white border border-slate-200 text-slate-700 font-mono py-0.5"
                                                    >
                                                        {fact}
                                                        <span
                                                            class="ml-1 text-[9px] text-slate-400 font-sans italic"
                                                        >
                                                            • {getFactLabel(
                                                                fact,
                                                            )}
                                                        </span>
                                                    </Badge>
                                                {/each}
                                            </div>
                                        </div>
                                    {/if}
                                {/each}

                                {#if quizState.adaptiveFacts.length === 0}
                                    <div
                                        class="text-center py-4 text-slate-400 italic text-xs"
                                    >
                                        No facts gathered for this session yet.
                                    </div>
                                {/if}
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <div
                                class="text-[10px] font-bold text-slate-500 mb-3 uppercase tracking-wider"
                            >
                                Rule Execution Status
                            </div>

                            {#if quizState.adaptiveTriggeredRule}
                                <div
                                    class="flex-1 p-5 bg-emerald-50 border border-emerald-200 rounded-xl flex flex-col justify-center"
                                    transition:fade={{ duration: 300 }}
                                >
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0 shadow-sm"
                                        >
                                            <CheckCircle
                                                size={20}
                                                class="text-emerald-600"
                                            />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div
                                                class="text-[10px] font-black text-emerald-600 uppercase mb-1 tracking-tighter"
                                            >
                                                Rule Successfully Triggered
                                            </div>
                                            <div
                                                class="text-base font-bold text-slate-800 mb-1.5 leading-tight"
                                            >
                                                {quizState.adaptiveTriggeredRule
                                                    .name}
                                            </div>
                                            <div
                                                class="flex items-center gap-2 flex-wrap text-[11px]"
                                            >
                                                <span
                                                    class="px-2 py-0.5 rounded bg-emerald-200 text-emerald-800 font-mono font-bold border border-emerald-300"
                                                >
                                                    ID: {quizState
                                                        .adaptiveTriggeredRule
                                                        .id}
                                                </span>
                                                <span
                                                    class="px-2 py-0.5 rounded bg-primary-100 text-primary-700 font-mono font-bold border border-primary-200"
                                                >
                                                    ACTION: {quizState
                                                        .adaptiveTriggeredRule
                                                        .action}
                                                </span>
                                                <span
                                                    class="text-[10px] text-slate-500 font-bold bg-slate-100 px-2 py-0.5 rounded"
                                                >
                                                    PRIORITY: {quizState
                                                        .adaptiveTriggeredRule
                                                        .priority}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {:else}
                                <div
                                    class="flex-1 p-6 bg-slate-50 border border-dashed border-slate-300 rounded-xl flex flex-col items-center justify-center text-center"
                                >
                                    <div
                                        class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mb-3"
                                    >
                                        <Zap size={20} class="text-slate-300" />
                                    </div>
                                    <p
                                        class="text-xs font-bold text-slate-400 uppercase tracking-widest"
                                    >
                                        Awaiting Engine Inference
                                    </p>
                                    <p
                                        class="text-[10px] text-slate-400 mt-1 max-w-[200px]"
                                    >
                                        Data is being processed using Forward
                                        Chaining matching strategy.
                                    </p>
                                </div>
                            {/if}
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white px-6 py-2 flex items-center justify-between border-t border-slate-50 text-[9px] font-bold text-slate-400 uppercase tracking-widest"
                >
                    <div class="flex items-center gap-4">
                        <span class="flex items-center gap-1"
                            ><Zap size={10} /> Forward Chaining</span
                        >
                        <span class="flex items-center gap-1 text-slate-300"
                            >•</span
                        >
                        <span class="flex items-center gap-1"
                            ><Target size={10} /> First Match Conflict Resolution</span
                        >
                    </div>
                    <div class="text-primary-400">
                        Adaptive Engine v2 • Stable
                    </div>
                </div>
            </div>
        {/if}
    </div>
{/if}
