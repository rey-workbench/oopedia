<script lang="ts">
    import { Code2, Plus, X } from 'lucide-svelte';
    import Button from '@/components/ui/Button.svelte';
    import type { AdaptiveRuleEditorState } from '@/states/Admin/AdaptiveRuleEditorState.svelte';

    let { form, state } = $props<{ form: any, state: AdaptiveRuleEditorState }>();
</script>

<div class="rounded-3xl border-2 border-b-6 border-slate-200 bg-white overflow-hidden shadow-sm {state.invalidDropZone === 'deduction' ? 'ring-4 ring-rose-500 animate-shake' : ''}">
    <div class="p-7">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 border-2 border-b-4 border-emerald-100 shadow-sm">
                    <Code2 size={24} />
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-black uppercase tracking-[0.15em] text-primary-500">DEDUCE (Diagnosis)</span>
                    <span class="text-[10px] font-bold text-emerald-400 mt-0.5">Diagnosa yang dihasilkan otomatis</span>
                </div>
            </div>
            <Button variant="secondary" size="sm" onclick={() => state.addDiagnosis(form)} icon={Plus}>
                NEW DEDUCTION
            </Button>
        </div>

        <div 
            role="region"
            aria-label="Drop zone for deductions"
            class="transition-all rounded-2xl border-2 border-dashed border-slate-100 p-2 {state.isDraggingOver === 'deduction' ? 'bg-emerald-50/30 border-emerald-200' : ''}"
            ondragover={(e) => { e.preventDefault(); state.isDraggingOver = 'deduction'; }}
            ondragleave={() => state.isDraggingOver = null}
            ondrop={(e) => state.handleDrop(e, 'deduction', form)}
        >
            {#if form.deduced_facts.length === 0}
                <div class="flex flex-col items-center justify-center py-12 text-slate-300">
                    <Code2 size={32} class="mb-2 opacity-20" />
                    <span class="text-[10px] font-black uppercase tracking-widest">Drop diagnosis here</span>
                </div>
            {:else}
                <div class="space-y-4">
                    {#each form.deduced_facts as fact, i}
                        <div class="flex items-center gap-6 p-5 rounded-2xl bg-slate-50 border-2 border-b-4 border-slate-200 transition-all hover:border-emerald-300 group">
                            <div class="flex-1 min-w-0">
                                <span class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.2em]">{fact.id}</span>
                                <input 
                                    type="text" 
                                    bind:value={form.deduced_facts[i].name} 
                                    class="w-full text-sm font-bold text-slate-700 bg-transparent border-none focus:ring-0 p-0 placeholder:text-slate-300" 
                                    placeholder="Diagnosis name..." 
                                />
                            </div>
                            <button 
                                type="button" 
                                onclick={() => form.deduced_facts = form.deduced_facts.filter((_: any, idx: number) => idx !== i)} 
                                class="press-active h-10 w-10 flex items-center justify-center rounded-xl text-slate-300 hover:bg-rose-50 hover:text-rose-500 border-2 border-transparent hover:border-rose-100"
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
