<script lang="ts">
    import { Zap, Plus, X } from 'lucide-svelte';
    import Button from '@/components/ui/Button.svelte';
    import type { AdaptiveRuleEditorState } from '@/states/Admin/AdaptiveRuleEditorState.svelte';

    let { form, state } = $props<{ form: any, state: AdaptiveRuleEditorState }>();
</script>

<div class="rounded-3xl border-2 border-b-6 border-slate-200 bg-white overflow-hidden shadow-sm {state.invalidDropZone === 'condition' ? 'ring-4 ring-rose-500 animate-shake' : ''}">
    <div class="p-7">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 border-2 border-b-4 border-indigo-100 shadow-sm">
                    <Zap size={24} />
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-black uppercase tracking-[0.15em] text-primary-500">WHEN (Conditions)</span>
                    <span class="text-[10px] font-bold text-indigo-400 mt-0.5">Syarat pemicu aturan ini</span>
                </div>
            </div>
            <Button variant="secondary" size="sm" onclick={() => state.addCondition(form)} icon={Plus}>
                ADD FACT
            </Button>
        </div>
        
        <div 
            role="region"
            aria-label="Drop zone for conditions"
            class="transition-all rounded-2xl border-2 border-dashed border-slate-100 p-2 {state.isDraggingOver === 'condition' ? 'bg-indigo-50/30 border-indigo-200' : ''}"
            ondragover={(e) => { e.preventDefault(); state.isDraggingOver = 'condition'; }}
            ondragleave={() => state.isDraggingOver = null}
            ondrop={(e) => state.handleDrop(e, 'condition', form)}
        >
            {#if form.facts.length === 0}
                <div class="flex flex-col items-center justify-center py-12 text-slate-300">
                    <Zap size={32} class="mb-2 opacity-20" />
                    <span class="text-[10px] font-black uppercase tracking-widest">Drop conditions here</span>
                </div>
            {:else}
                <div class="space-y-4">
                    {#each form.facts as fact, i}
                        {@const isGCode = fact.id.startsWith('G')}
                        {@const allowedValues = state.ALLOWED_VALUES[fact.key]}
                        
                        <div class="flex items-center gap-6 p-5 rounded-2xl bg-slate-50 border-2 border-b-4 border-slate-200 transition-all hover:border-indigo-300 group">
                            <div class="flex-1 min-w-0">
                                <span class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em]">{fact.id}</span>
                                <input 
                                    type="text" 
                                    bind:value={form.facts[i].name} 
                                    readonly={isGCode}
                                    class="w-full text-sm font-bold text-slate-700 bg-transparent border-none focus:ring-0 p-0 placeholder:text-slate-300"
                                    placeholder="Condition name..."
                                />
                            </div>
                            
                            <div class="flex items-center gap-2 bg-white p-1.5 rounded-xl border-2 border-slate-100 shadow-sm">
                                <select bind:value={form.facts[i].operator} disabled={isGCode} class="h-9 w-16 rounded-lg border-none bg-slate-50 pl-3 pr-8 text-base font-black text-slate-900 focus:ring-0 text-center text-center-last">
                                    {#each [{ value: '==', label: '=' }, { value: '!=', label: '≠' }, { value: '>', label: '>' }, { value: '<', label: '<' }, { value: '>=', label: '≥' }, { value: '<=', label: '≤' }] as op}<option value={op.value}>{op.label}</option>{/each}
                                </select>

                                {#if allowedValues}
                                    <select bind:value={form.facts[i].value} disabled={isGCode} class="h-9 min-w-[100px] rounded-lg border-none bg-slate-50 px-3 text-xs font-black text-slate-900 focus:ring-0 text-center-last">
                                        {#each allowedValues as av}<option value={av.value}>{av.label}</option>{/each}
                                    </select>
                                {:else}
                                    <input type="text" bind:value={form.facts[i].value} readonly={isGCode} class="h-9 w-20 rounded-lg border-none bg-slate-50 px-3 text-center text-xs font-black text-slate-900 focus:ring-0" />
                                {/if}
                            </div>

                            <button type="button" onclick={() => form.facts.splice(i, 1)} class="press-active h-10 w-10 flex items-center justify-center rounded-xl text-slate-300 hover:bg-rose-50 hover:text-rose-500 border-2 border-transparent hover:border-rose-100">
                                <X size={18} />
                            </button>
                        </div>
                    {/each}
                </div>
            {/if}
        </div>
    </div>
</div>
