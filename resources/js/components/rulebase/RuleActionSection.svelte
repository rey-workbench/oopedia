<script lang="ts">
    import { Target, Plus, X } from 'lucide-svelte';
    import type { AdaptiveRuleEditorState } from '@/states/Admin/AdaptiveRuleEditorState.svelte';

    let { form, state, allActions } = $props<{ form: any, state: AdaptiveRuleEditorState, allActions: any[] }>();
</script>

<div class="rounded-3xl border-2 border-b-6 border-slate-200 bg-white overflow-hidden shadow-sm {state.invalidDropZone === 'action' ? 'ring-4 ring-rose-500 animate-shake' : ''}">
    <div class="p-7">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600 border-2 border-b-4 border-amber-100 shadow-sm">
                    <Target size={24} />
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-black uppercase tracking-[0.15em] text-primary-500">DO (Execution)</span>
                    <span class="text-[10px] font-bold text-amber-400 mt-0.5">Aksi yang dijalankan sistem</span>
                </div>
            </div>
        </div>

        <div 
            role="region"
            aria-label="Drop zone for actions"
            class="transition-all rounded-2xl border-2 border-dashed border-slate-100 p-2 {state.isDraggingOver === 'action' ? 'bg-amber-50/30 border-amber-200' : ''}"
            ondragover={(e) => { e.preventDefault(); state.isDraggingOver = 'action'; }}
            ondragleave={() => state.isDraggingOver = null}
            ondrop={(e) => state.handleDrop(e, 'action', form)}
        >
            {#if form.actions.length === 0}
                <div class="flex flex-col items-center justify-center py-12 text-slate-300">
                    <Target size={32} class="mb-2 opacity-20" />
                    <span class="text-[10px] font-black uppercase tracking-widest">Drop actions here</span>
                </div>
            {:else}
                <div class="space-y-6">
                    {#each form.actions as actionConfig, i}
                        {@const action = allActions.find((a: any) => a.id === actionConfig.id)}
                        <div class="p-5 rounded-2xl bg-slate-50 border-2 border-b-4 border-slate-200 transition-all hover:border-amber-300 group">
                            <div class="flex items-center gap-6 mb-4">
                                <div class="flex-1 min-w-0">
                                    <span class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em]">{actionConfig.id}</span>
                                    <p class="text-sm font-bold text-slate-700">{action?.name || actionConfig.id}</p>
                                </div>
                                <button 
                                    type="button" 
                                    onclick={() => form.actions = form.actions.filter((_: any, idx: number) => idx !== i)} 
                                    class="press-active h-10 w-10 flex items-center justify-center rounded-xl text-slate-300 hover:bg-rose-50 hover:text-rose-500 border-2 border-transparent hover:border-rose-100"
                                >
                                    <X size={18} />
                                </button>
                            </div>

                            <div class="pt-4 border-t border-slate-200/60">
                                <div class="flex items-center justify-between mb-3 gap-4">
                                    <div class="flex items-center gap-2 flex-1">
                                        <select 
                                            bind:value={state.selectedMetadataKey}
                                            class="text-[10px] font-bold text-slate-500 bg-slate-100 border-none rounded-lg px-2 py-1 outline-none focus:ring-1 focus:ring-amber-300"
                                        >
                                            {#each state.METADATA_KEYS as k}
                                                <option value={k.value}>{k.label}</option>
                                            {/each}
                                        </select>
                                    </div>
                                    <button 
                                        type="button"
                                        onclick={() => state.addActionMetadata(form, i)}
                                        class="text-[9px] font-black text-amber-600 hover:text-amber-700 flex items-center gap-1 shrink-0"
                                    >
                                        <Plus size={10} /> ADD PARAM
                                    </button>
                                </div>
                                
                                <div class="space-y-2">
                                    {#each Object.entries(actionConfig.metadata) as [key, _value]}
                                        <div class="flex items-center gap-3">
                                            <div class="w-1/3 text-[10px] font-bold text-slate-500 truncate" title={key}>{key}</div>
                                            <input 
                                                type="text" 
                                                bind:value={form.actions[i].metadata[key]}
                                                class="flex-1 text-[11px] font-bold text-slate-600 bg-white rounded-lg border border-slate-200 px-3 py-1.5 focus:border-amber-400 focus:ring-0 outline-none"
                                                placeholder="Value..."
                                            />
                                            <button 
                                                type="button" 
                                                onclick={() => state.removeActionMetadata(form, i, key)}
                                                class="text-slate-300 hover:text-rose-500"
                                            >
                                                <X size={14} />
                                            </button>
                                        </div>
                                    {/each}
                                    {#if Object.keys(actionConfig.metadata).length === 0}
                                        <p class="text-[9px] italic text-slate-400">No specific parameters for this action.</p>
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
