<script lang="ts">
    import { Target, Plus, X } from '@lucide/svelte';
    import type { AdaptiveRuleEditorState } from '@/states/Admin/AdaptiveRuleEditorState.svelte';

    let { form, state, allActions } = $props<{
        form: any;
        state: AdaptiveRuleEditorState;
        allActions: any[];
    }>();
</script>

<div
    class="overflow-hidden rounded-3xl border-2 border-b-6 border-slate-200 bg-white shadow-sm {state.invalidDropZone ===
    'action'
        ? 'animate-shake ring-4 ring-rose-500'
        : ''}"
>
    <div class="p-7">
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl border-2 border-b-4 border-amber-100 bg-amber-50 text-amber-600 shadow-sm"
                >
                    <Target size={24} />
                </div>
                <div class="flex flex-col">
                    <span class="text-primary-500 text-xs font-black tracking-[0.15em] uppercase"
                        >DO (Execution)</span
                    >
                    <span class="mt-0.5 text-[10px] font-bold text-amber-400"
                        >Aksi yang dijalankan sistem</span
                    >
                </div>
            </div>
        </div>

        <div
            role="region"
            aria-label="Drop zone for actions"
            class="rounded-2xl border-2 border-dashed border-slate-100 p-2 transition-all {state.isDraggingOver ===
            'action'
                ? 'border-amber-200 bg-amber-50/30'
                : ''}"
            ondragover={(e) => {
                e.preventDefault();
                state.isDraggingOver = 'action';
            }}
            ondragleave={() => (state.isDraggingOver = null)}
            ondrop={(e) => state.handleDrop(e, 'action', form)}
        >
            {#if form.actions.length === 0}
                <div class="flex flex-col items-center justify-center py-12 text-slate-300">
                    <Target size={32} class="mb-2 opacity-20" />
                    <span class="text-[10px] font-black tracking-widest uppercase"
                        >Drop actions here</span
                    >
                </div>
            {:else}
                <div class="space-y-6">
                    {#each form.actions as actionConfig, i}
                        {@const action = allActions.find((a: any) => a.id === actionConfig.id)}
                        <div
                            class="group rounded-2xl border-2 border-b-4 border-slate-200 bg-slate-50 p-5 transition-all hover:border-amber-300"
                        >
                            <div class="mb-4 flex items-center gap-6">
                                <div class="min-w-0 flex-1">
                                    <span
                                        class="text-[10px] font-black tracking-[0.2em] text-amber-500 uppercase"
                                        >{actionConfig.id}</span
                                    >
                                    <p class="text-sm font-bold text-slate-700">
                                        {action?.name || actionConfig.id}
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    onclick={() =>
                                        (form.actions = form.actions.filter(
                                            (_: any, idx: number) => idx !== i
                                        ))}
                                    class="press-active flex h-10 w-10 items-center justify-center rounded-xl border-2 border-transparent text-slate-300 hover:border-rose-100 hover:bg-rose-50 hover:text-rose-500"
                                >
                                    <X size={18} />
                                </button>
                            </div>

                            <div class="border-t border-slate-200/60 pt-4">
                                <div class="mb-3 flex items-center justify-between gap-4">
                                    <div class="flex flex-1 items-center gap-2">
                                        <select
                                            bind:value={state.selectedMetadataKey}
                                            class="rounded-lg border-none bg-slate-100 px-2 py-1 text-[10px] font-bold text-slate-500 outline-none focus:ring-1 focus:ring-amber-300"
                                        >
                                            {#each state.METADATA_KEYS as k}
                                                <option value={k.value}>{k.label}</option>
                                            {/each}
                                        </select>
                                    </div>
                                    <button
                                        type="button"
                                        onclick={() => state.addActionMetadata(form, i)}
                                        class="flex shrink-0 items-center gap-1 text-[9px] font-black text-amber-600 hover:text-amber-700"
                                    >
                                        <Plus size={10} /> ADD PARAM
                                    </button>
                                </div>

                                <div class="space-y-2">
                                    {#each Object.entries(actionConfig.metadata) as [key, _value]}
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-1/3 truncate text-[10px] font-bold text-slate-500"
                                                title={key}
                                            >
                                                {key}
                                            </div>
                                            <input
                                                type="text"
                                                bind:value={form.actions[i].metadata[key]}
                                                class="flex-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-bold text-slate-600 outline-none focus:border-amber-400 focus:ring-0"
                                                placeholder="Value..."
                                            />
                                            <button
                                                type="button"
                                                onclick={() =>
                                                    state.removeActionMetadata(form, i, key)}
                                                class="text-slate-300 hover:text-rose-500"
                                            >
                                                <X size={14} />
                                            </button>
                                        </div>
                                    {/each}
                                    {#if Object.keys(actionConfig.metadata).length === 0}
                                        <p class="text-[9px] text-slate-400 italic">
                                            No specific parameters for this action.
                                        </p>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    {/each}
                </div>
            {/if}
        </div>
    </div>
</div>
