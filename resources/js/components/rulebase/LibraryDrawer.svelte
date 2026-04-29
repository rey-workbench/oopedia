<script lang="ts">
    import { Library, Settings2, Code2, Target, Zap, ChevronDown } from 'lucide-svelte';
    import type { AdaptiveFact, AdaptiveAction } from '@/types/models';

    let { 
        allFacts, 
        allActions, 
        CONDITION_KEYS,
        handleDragStart 
    } = $props<{
        allFacts: AdaptiveFact[];
        allActions: AdaptiveAction[];
        CONDITION_KEYS: Array<{value: string, label: string}>;
        handleDragStart: (e: DragEvent, id: string, type: string) => void;
    }>();

    let activeTab = $state<'raw' | 'conditions' | 'virtual' | 'actions'>('raw');
    let limit = $state(6);

    // Reset limit when tab changes
    $effect(() => {
        activeTab;
        limit = 6;
    });

    const standardConditions = $derived(allFacts.filter((f) => f.category !== 'virtual'));
    const virtualFacts = $derived(allFacts.filter((f) => f.category === 'virtual'));
</script>

<div class="flex h-full w-72 flex-col border-r-2 border-slate-200 bg-cosmos-bg shadow-sm">
    <!-- HEADER & TABS -->
    <div class="border-b-2 border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-5 flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-50 text-primary-500 border-2 border-b-4 border-primary-100 shadow-sm">
                <Library size={20} />
            </div>
            <div>
                <h3 class="font-display text-xs font-black tracking-[0.2em] text-primary-500 uppercase">Library</h3>
                <div class="mt-0.5 flex items-center gap-1.5">
                    <div class="h-1.5 w-1.5 rounded-full bg-emerald-500"></div>
                    <p class="text-[9px] font-bold tracking-widest text-slate-400 uppercase leading-none">Assets Inventory</p>
                </div>
            </div>
        </div>
        
        <!-- Segmented Control Tabs - Modern Duo Style -->
        <div class="mt-6 grid grid-cols-2 gap-2 rounded-2xl bg-slate-100 p-1">
            {#each ['raw', 'conditions', 'virtual', 'actions'] as tab}
                <button 
                    onclick={() => activeTab = tab as any} 
                    class="press-active flex items-center justify-center rounded-xl px-2 py-2.5 text-[9px] font-black uppercase tracking-widest transition-all 
                    {activeTab === tab 
                        ? 'bg-white text-indigo-600 shadow-sm border-2 border-b-4 border-slate-200' 
                        : 'text-slate-500 hover:text-slate-800'}"
                >
                    {tab === 'raw' ? 'Triggers' : tab === 'virtual' ? 'Deduce' : tab.charAt(0).toUpperCase() + tab.slice(1)}
                </button>
            {/each}
        </div>
    </div>

    <!-- CONTENT AREA -->
    <div class="custom-scrollbar flex-1 overflow-y-auto bg-slate-50/30 p-5">
        
        {#if activeTab === 'raw'}
            <!-- RAW TRIGGERS -->
            <section class="animate-in fade-in slide-in-from-right-4 space-y-4 duration-300">
                <div class="flex items-center justify-between px-1">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Raw Triggers</h4>
                    <Zap size={14} class="text-brand-yellow" />
                </div>
                <div class="grid gap-3">
                    {#each CONDITION_KEYS.slice(0, limit) as key}
                        <!-- svelte-ignore a11y_no_static_element_interactions -->
                        <div
                            draggable="true"
                            ondragstart={(e) => handleDragStart(e, key.value, 'fact')}
                            class="group press-active cursor-grab rounded-2xl border-2 border-b-4 border-slate-200 bg-white p-4 transition-all hover:border-indigo-400 hover:shadow-lg active:scale-95"
                        >
                            <div class="text-[11px] font-black leading-tight text-slate-800 group-hover:text-indigo-600">{key.label}</div>
                            <div class="mt-1 font-mono text-[9px] uppercase tracking-tighter text-slate-400">{key.value}</div>
                        </div>
                    {/each}
                </div>
                {#if CONDITION_KEYS.length > limit}
                    <button onclick={() => limit += 6} class="press-active mt-2 flex w-full items-center justify-center gap-1 rounded-xl bg-white border-2 border-b-4 border-slate-200 py-3 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-primary-500">
                        SHOW MORE ({CONDITION_KEYS.length - limit}) <ChevronDown size={14} />
                    </button>
                {/if}
            </section>

        {:else if activeTab === 'conditions'}
            <!-- STANDARD CONDITIONS -->
            <section class="animate-in fade-in slide-in-from-right-4 space-y-4 duration-300">
                <div class="flex items-center justify-between px-1">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Standard Conditions</h4>
                    <Settings2 size={14} class="text-indigo-500" />
                </div>
                <div class="grid gap-3">
                    {#each standardConditions.slice(0, limit) as fact}
                        <!-- svelte-ignore a11y_no_static_element_interactions -->
                        <div
                            draggable="true"
                            ondragstart={(e) => handleDragStart(e, fact.id, 'fact')}
                            class="group press-active cursor-grab rounded-2xl border-2 border-b-4 border-slate-200 bg-white p-4 transition-all hover:border-indigo-400 hover:shadow-lg active:scale-95"
                        >
                            <div class="mb-2 flex items-center gap-2">
                                <span class="rounded-lg bg-indigo-50 px-2 py-1 text-[9px] font-black uppercase tracking-tight text-indigo-600 ring-1 ring-indigo-100">
                                    {fact.id}
                                </span>
                                <div class="h-px flex-1 bg-slate-100"></div>
                            </div>
                            <div class="text-[11px] font-black leading-tight text-slate-800 group-hover:text-indigo-600">{fact?.name}</div>
                        </div>
                    {/each}
                </div>
                {#if standardConditions.length > limit}
                    <button onclick={() => limit += 6} class="press-active mt-2 flex w-full items-center justify-center gap-1 rounded-xl bg-white border-2 border-b-4 border-slate-200 py-3 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-primary-500">
                        SHOW MORE ({standardConditions.length - limit}) <ChevronDown size={14} />
                    </button>
                {/if}
            </section>

        {:else if activeTab === 'virtual'}
            <!-- VIRTUAL FACTS (DIAGNOSIS) -->
            <section class="animate-in fade-in slide-in-from-right-4 space-y-4 duration-300">
                <div class="flex items-center justify-between px-1">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Virtual Facts (Deduce)</h4>
                    <Code2 size={14} class="text-emerald-500" />
                </div>
                <div class="grid gap-3">
                    {#each virtualFacts.slice(0, limit) as fact}
                        <!-- svelte-ignore a11y_no_static_element_interactions -->
                        <div
                            draggable="true"
                            ondragstart={(e) => handleDragStart(e, fact.id, 'virtual-fact')}
                            class="group press-active cursor-grab rounded-2xl border-2 border-b-4 border-slate-200 bg-white p-4 transition-all hover:border-emerald-400 hover:shadow-lg active:scale-95"
                        >
                            <div class="mb-2 flex items-center gap-2">
                                <span class="rounded-lg bg-emerald-50 px-2 py-1 text-[9px] font-black uppercase text-emerald-600 ring-1 ring-emerald-100">
                                    {fact.id}
                                </span>
                                <div class="h-px flex-1 bg-slate-100"></div>
                            </div>
                            <div class="text-[11px] font-black leading-tight text-slate-800 group-hover:text-emerald-600">{fact?.name || fact.id}</div>
                        </div>
                    {/each}
                    {#if virtualFacts.length === 0}
                        <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-6 text-center">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Belum ada diagnosa</span>
                        </div>
                    {/if}
                </div>
                {#if virtualFacts.length > limit}
                    <button onclick={() => limit += 6} class="press-active mt-2 flex w-full items-center justify-center gap-1 rounded-xl bg-white border-2 border-b-4 border-slate-200 py-3 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-primary-500">
                        SHOW MORE ({virtualFacts.length - limit}) <ChevronDown size={14} />
                    </button>
                {/if}
            </section>

        {:else if activeTab === 'actions'}
            <!-- ACTIONS -->
            <section class="animate-in fade-in slide-in-from-right-4 space-y-4 duration-300">
                <div class="flex items-center justify-between px-1">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Actions</h4>
                    <Target size={14} class="text-brand-yellow" />
                </div>
                <div class="grid gap-3">
                    {#each allActions.slice(0, limit) as action}
                        <!-- svelte-ignore a11y_no_static_element_interactions -->
                        <div
                            draggable="true"
                            ondragstart={(e) => handleDragStart(e, action.id, 'action')}
                            class="group press-active cursor-grab rounded-2xl border-2 border-b-4 border-slate-200 bg-white p-4 transition-all hover:border-brand-yellow hover:shadow-lg active:scale-95"
                        >
                            <div class="text-[11px] font-black leading-tight text-slate-800 group-hover:text-brand-yellow">{action?.name}</div>
                            <div class="mt-1 font-mono text-xs uppercase tracking-tighter text-slate-400">{action.id}</div>
                        </div>
                    {/each}
                </div>
                {#if allActions.length > limit}
                    <button onclick={() => limit += 6} class="press-active mt-2 flex w-full items-center justify-center gap-1 rounded-xl bg-white border-2 border-b-4 border-slate-200 py-3 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-primary-500">
                        SHOW MORE ({allActions.length - limit}) <ChevronDown size={14} />
                    </button>
                {/if}
            </section>
        {/if}
    </div>
</div>
