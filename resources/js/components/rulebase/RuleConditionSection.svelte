<script lang="ts">
    import { Zap, Plus, X } from 'lucide-svelte';
    import Button from '@/components/ui/Button.svelte';
    import type { AdaptiveRuleEditorState } from '@/states/Admin/AdaptiveRuleEditorState.svelte';

    let { form, state } = $props<{ form: any; state: AdaptiveRuleEditorState }>();
</script>

<div
    class="overflow-hidden rounded-3xl border-2 border-b-6 border-slate-200 bg-white shadow-sm {state.invalidDropZone ===
    'condition'
        ? 'animate-shake ring-4 ring-rose-500'
        : ''}"
>
    <div class="p-7">
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl border-2 border-b-4 border-indigo-100 bg-indigo-50 text-indigo-600 shadow-sm"
                >
                    <Zap size={24} />
                </div>
                <div class="flex flex-col">
                    <span class="text-primary-500 text-xs font-black tracking-[0.15em] uppercase"
                        >WHEN (Conditions)</span
                    >
                    <span class="mt-0.5 text-[10px] font-bold text-indigo-400"
                        >Syarat pemicu aturan ini</span
                    >
                </div>
            </div>
            <Button
                variant="secondary"
                size="sm"
                onclick={() => state.addCondition(form)}
                icon={Plus}
            >
                ADD FACT
            </Button>
        </div>

        <div
            role="region"
            aria-label="Drop zone for conditions"
            class="rounded-2xl border-2 border-dashed border-slate-100 p-2 transition-all {state.isDraggingOver ===
            'condition'
                ? 'border-indigo-200 bg-indigo-50/30'
                : ''}"
            ondragover={(e) => {
                e.preventDefault();
                state.isDraggingOver = 'condition';
            }}
            ondragleave={() => (state.isDraggingOver = null)}
            ondrop={(e) => state.handleDrop(e, 'condition', form)}
        >
            {#if form.facts.length === 0}
                <div class="flex flex-col items-center justify-center py-12 text-slate-300">
                    <Zap size={32} class="mb-2 opacity-20" />
                    <span class="text-[10px] font-black tracking-widest uppercase"
                        >Drop conditions here</span
                    >
                </div>
            {:else}
                <div class="space-y-4">
                    {#each form.facts as fact, i}
                        {@const isGCode = fact.id.startsWith('G')}
                        {@const allowedValues = state.ALLOWED_VALUES[fact.key]}

                        <div
                            class="group flex items-center gap-6 rounded-2xl border-2 border-b-4 border-slate-200 bg-slate-50 p-5 transition-all hover:border-indigo-300"
                        >
                            <div class="min-w-0 flex-1">
                                <span
                                    class="text-[10px] font-black tracking-[0.2em] text-indigo-400 uppercase"
                                    >{fact.id}</span
                                >
                                <input
                                    type="text"
                                    bind:value={form.facts[i].name}
                                    readonly={isGCode}
                                    class="w-full border-none bg-transparent p-0 text-sm font-bold text-slate-700 placeholder:text-slate-300 focus:ring-0"
                                    placeholder="Condition name..."
                                />
                            </div>

                            <div
                                class="flex items-center gap-2 rounded-xl border-2 border-slate-100 bg-white p-1.5 shadow-sm"
                            >
                                <select
                                    bind:value={form.facts[i].operator}
                                    disabled={isGCode}
                                    class="text-center-last h-9 w-16 rounded-lg border-none bg-slate-50 pr-8 pl-3 text-center text-base font-black text-slate-900 focus:ring-0"
                                >
                                    {#each [{ value: '==', label: '=' }, { value: '!=', label: '≠' }, { value: '>', label: '>' }, { value: '<', label: '<' }, { value: '>=', label: '≥' }, { value: '<=', label: '≤' }] as op}<option
                                            value={op.value}>{op.label}</option
                                        >{/each}
                                </select>

                                {#if allowedValues}
                                    <select
                                        bind:value={form.facts[i].value}
                                        disabled={isGCode}
                                        class="text-center-last h-9 min-w-[100px] rounded-lg border-none bg-slate-50 px-3 text-xs font-black text-slate-900 focus:ring-0"
                                    >
                                        {#each allowedValues as av}<option value={av.value}
                                                >{av.label}</option
                                            >{/each}
                                    </select>
                                {:else}
                                    <input
                                        type="text"
                                        bind:value={form.facts[i].value}
                                        readonly={isGCode}
                                        class="h-9 w-20 rounded-lg border-none bg-slate-50 px-3 text-center text-xs font-black text-slate-900 focus:ring-0"
                                    />
                                {/if}
                            </div>

                            <button
                                type="button"
                                onclick={() => form.facts.splice(i, 1)}
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
