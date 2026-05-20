<script lang="ts">
    import { Code2, Plus, X } from '@lucide/svelte';
    import Button from '@/components/ui/Button.svelte';
    import type { AdaptiveRuleEditorState } from '@/states/Admin/AdaptiveRuleEditorState.svelte';

    let { form, state } = $props<{ form: any; state: AdaptiveRuleEditorState }>();
</script>

<div
    class="overflow-hidden rounded-3xl border-2 border-b-6 border-slate-200 bg-white shadow-sm {state.invalidDropZone ===
    'deduction'
        ? 'animate-shake ring-4 ring-rose-500'
        : ''}"
>
    <div class="p-7">
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl border-2 border-b-4 border-emerald-100 bg-emerald-50 text-emerald-600 shadow-sm"
                >
                    <Code2 size={24} />
                </div>
                <div class="flex flex-col">
                    <span class="text-primary-500 text-xs font-black tracking-[0.15em] uppercase"
                        >DEDUCE (Diagnosis)</span
                    >
                    <span class="mt-0.5 text-[10px] font-bold text-emerald-400"
                        >Diagnosa yang dihasilkan otomatis</span
                    >
                </div>
            </div>
            <Button
                variant="secondary"
                size="sm"
                onclick={() => state.addDiagnosis(form)}
                icon={Plus}
            >
                NEW DEDUCTION
            </Button>
        </div>

        <div
            role="region"
            aria-label="Drop zone for deductions"
            class="rounded-2xl border-2 border-dashed border-slate-100 p-2 transition-all {state.isDraggingOver ===
            'deduction'
                ? 'border-emerald-200 bg-emerald-50/30'
                : ''}"
            ondragover={(e) => {
                e.preventDefault();
                state.isDraggingOver = 'deduction';
            }}
            ondragleave={() => (state.isDraggingOver = null)}
            ondrop={(e) => state.handleDrop(e, 'deduction', form)}
        >
            {#if form.deduced_facts.length === 0}
                <div class="flex flex-col items-center justify-center py-12 text-slate-300">
                    <Code2 size={32} class="mb-2 opacity-20" />
                    <span class="text-[10px] font-black tracking-widest uppercase"
                        >Drop diagnosis here</span
                    >
                </div>
            {:else}
                <div class="space-y-4">
                    {#each form.deduced_facts as fact, i}
                        <div
                            class="group flex items-center gap-6 rounded-2xl border-2 border-b-4 border-slate-200 bg-slate-50 p-5 transition-all hover:border-emerald-300"
                        >
                            <div class="min-w-0 flex-1">
                                <span
                                    class="text-[10px] font-black tracking-[0.2em] text-emerald-500 uppercase"
                                    >{fact.id}</span
                                >
                                <input
                                    type="text"
                                    bind:value={form.deduced_facts[i].name}
                                    class="w-full border-none bg-transparent p-0 text-sm font-bold text-slate-700 placeholder:text-slate-300 focus:ring-0"
                                    placeholder="Diagnosis name..."
                                />
                            </div>
                            <button
                                type="button"
                                onclick={() =>
                                    (form.deduced_facts = form.deduced_facts.filter(
                                        (_: any, idx: number) => idx !== i
                                    ))}
                                class="press-active flex h-10 w-10 items-center justify-center rounded-xl border-2 border-transparent text-slate-300 hover:border-rose-100 hover:bg-rose-50 hover:text-rose-500"
                            >
                                <X size={18} />
                            </button>
                        </div>
                    {/each}
                </div>
            {/if}
        </div>
    </div>
</div>
