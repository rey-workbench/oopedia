<script lang="ts">
    import { useForm } from '@inertiajs/svelte';
    import Input from '@/components/ui/Input.svelte';
    import Button from '@/components/ui/Button.svelte';
    import {
        Save,
        X,
        Zap,
        MessageSquareQuote,
        Target,
        Code2,
        Settings2,
        Plus,
    } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import type { AdaptiveFact, AdaptiveAction } from '@/types/models';
    import { fly, fade } from 'svelte/transition';
    import Toggle from '@/components/ui/Toggle.svelte';
    import LibraryDrawer from '@/components/rulebase/LibraryDrawer.svelte';
    import { AlertCircle } from 'lucide-svelte';

    let {
        show = false,
        rule = null,
        allFacts,
        allActions,
        totalRules = 0,
        onclose,
    } = $props<{
        show: boolean;
        rule: any | null;
        allFacts: AdaptiveFact[];
        allActions: AdaptiveAction[];
        totalRules?: number;
        onclose: () => void;
    }>();

    const isEdit = $derived(!!rule);

    let form = useForm({
        id: '',
        name: '',
        recommendation: '',
        priority: 10,
        action_ids: [] as string[],
        required_fact_ids: [] as string[],
        deduced_fact_ids: [] as string[],
        facts: [] as Array<{ id: string; key: string; name: string; operator: string; value: any; description?: string; isManual?: boolean }>,
        deduced_facts: [] as Array<{ id: string; name: string; description?: string; isManual?: boolean }>,
        is_active: true,
        description: '',
    });

    const CONDITION_KEYS = [
        { value: 'accuracy', label: 'Akurasi' },
        { value: 'hints_used', label: 'Bantuan' },
        { value: 'streak', label: 'Streak' },
        { value: 'level', label: 'Level' },
        { value: 'performance_metrics.trend', label: 'Tren' },
        { value: 'performance_metrics.speed', label: 'Kecepatan' },
        { value: 'performance_metrics.stagnant_count', label: 'Stagnan' },
        { value: 'current_session.hints', label: 'Bantuan Sesi' },
        { value: 'current_session.time_spent', label: 'Waktu Sesi' },
    ];

    const ALLOWED_VALUES: Record<string, Array<{ value: string; label: string }>> = {
        'performance_metrics.trend': [
            { value: 'up', label: 'Naik' },
            { value: 'stable', label: 'Stabil' },
            { value: 'down', label: 'Turun' },
        ],
        'performance_metrics.speed': [
            { value: 'fast', label: 'Cepat' },
            { value: 'normal', label: 'Normal' },
            { value: 'slow', label: 'Lambat' },
        ],
        'level': [
            { value: 'Beginner', label: 'Beginner' },
            { value: 'Intermediate', label: 'Intermediate' },
            { value: 'Expert', label: 'Expert' },
            { value: 'Ahli', label: 'Ahli' },
        ]
    };

    function getNextAutoId(prefix: 'F' | 'V' | 'R', currentList: any[], dbList: any[] = []) {
        const pattern = new RegExp(`^${prefix}(\d+)$`);
        const extractNums = (list: any[]) => list
            .map(item => {
                const id = typeof item === 'string' ? item : (item.id || item.key);
                const match = id?.match(pattern);
                return match ? parseInt(match[1]) : 0;
            })
            .filter(n => !isNaN(n));

        const nums = [...extractNums(currentList), ...extractNums(dbList)];
        const maxNum = nums.length > 0 ? Math.max(...nums) : 0;
        if (prefix === 'R' && maxNum === 0) return `R${String(totalRules + 1).padStart(2, '0')}`;
        return `${prefix}${String(maxNum + 1).padStart(2, '0')}`;
    }

    $effect(() => {
        if (show) {
            if (rule) {
                form.id = rule.id;
                form.name = rule.name;
                form.recommendation = rule.recommendation;
                form.priority = rule.priority;
                form.action_ids = rule.action_ids || [];
                form.required_fact_ids = rule.required_fact_ids || [];
                form.deduced_fact_ids = rule.deduced_fact_ids || [];
                
                form.facts = (rule.required_fact_ids || []).map((factId: string) => {
                    const existing = allFacts.find(f => f.id === factId);
                    let parsedOp = '==';
                    let parsedVal: any = 1;
                    let parsedKey = factId;
                    let parsedName = existing?.name || '';

                    if (existing && existing.description) {
                        try {
                            const descData = typeof existing.description === 'string' ? JSON.parse(existing.description) : existing.description;
                            if (descData.op) parsedOp = descData.op;
                            if (descData.val !== undefined) parsedVal = descData.val;
                            if (descData.key) parsedKey = descData.key;
                        } catch (e) {}
                    }

                    return { 
                        id: factId, 
                        key: parsedKey, 
                        name: parsedName,
                        operator: parsedOp, 
                        value: parsedVal,
                        isManual: false
                    };
                });

                form.deduced_facts = (rule.deduced_fact_ids || []).map((factId: string) => {
                    const existing = allFacts.find(f => f.id === factId);
                    return { id: factId, name: existing?.name || '', isManual: false };
                });
                
                form.is_active = rule.is_active;
                form.description = rule.description || '';
            } else {
                form.reset();
                form.id = getNextAutoId('R', [], []);
            }
        }
    });

    function handleSubmit(e: Event) {
        e.preventDefault();
        form.required_fact_ids = form.facts.map(f => f.id);
        form.deduced_fact_ids = form.deduced_facts.map(f => f.id);

        const url = isEdit ? ROUTES.ADMIN.ADAPTIVE_RULES.UPDATE(rule.id) : ROUTES.ADMIN.ADAPTIVE_RULES.STORE;
        form.submit(isEdit ? 'put' : 'post', url, {
            onSuccess: () => onclose(),
            preserveScroll: true,
        });
    }

    let draggingSourceType = $state<string | null>(null);
    let isDraggingOver = $state<string | null>(null);
    let invalidDropZone = $state<string | null>(null);

    function handleDragStart(e: DragEvent, id: string, type: string) {
        if (!e.dataTransfer) return;
        const dragData = JSON.stringify({ id, type });
        e.dataTransfer.setData('application/json', dragData);
        e.dataTransfer.effectAllowed = 'copy';
        draggingSourceType = type;
    }

    function handleDrop(e: DragEvent, targetZone: 'required' | 'deduced' | 'action') {
        e.preventDefault();
        draggingSourceType = null;
        isDraggingOver = null;
        if (!e.dataTransfer) return;

        try {
            const rawData = e.dataTransfer.getData('application/json');
            if (!rawData) return;
            const dragData = JSON.parse(rawData);
            const id = dragData.id;
            const sourceType = dragData.type;

            const isValid = (targetZone === 'required' && sourceType === 'fact') ||
                          (targetZone === 'action' && sourceType === 'action') ||
                          (targetZone === 'deduced' && sourceType === 'virtual-fact');

            if (!isValid) {
                invalidDropZone = targetZone;
                setTimeout(() => invalidDropZone = null, 400);
                return;
            }

            if (targetZone === 'action') {
                if (!form.action_ids.includes(id)) form.action_ids = [...form.action_ids, id];
            } else if (targetZone === 'required') {
                addCondition(id);
            } else {
                addDiagnosis(id);
            }
        } catch (err) {}
    }

    function addCondition(factId = '') {
        const finalId = factId || getNextAutoId('F', form.facts, allFacts);
        const existingFact = allFacts?.find(f => f.id === finalId);
        
        let parsedOp = '==';
        let parsedVal: any = 1;
        let parsedKey = finalId;
        let parsedName = existingFact?.name || '';

        if (existingFact && existingFact.description) {
            try {
                const descData = typeof existingFact.description === 'string' ? JSON.parse(existingFact.description) : existingFact.description;
                if (descData.op) parsedOp = descData.op;
                if (descData.val !== undefined) parsedVal = descData.val;
                if (descData.key) parsedKey = descData.key;
            } catch (e) {}
        }
        
        form.facts = [...form.facts, { 
            id: finalId, 
            key: parsedKey, 
            name: parsedName,
            operator: parsedOp, 
            value: parsedVal,
            isManual: !factId
        }];
    }

    function addDiagnosis(id = '') {
        const finalId = id || getNextAutoId('V', form.deduced_facts, allFacts);
        const existingFact = allFacts?.find(f => f.id === finalId);
        
        if (!form.deduced_facts.find(f => f.id === finalId)) {
            form.deduced_facts = [...form.deduced_facts, { 
                id: finalId, 
                name: existingFact?.name || '',
                isManual: !id
            }];
        }
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

{#if show}
    <!-- svelte-ignore a11y_click_events_have_key_events -->
    <!-- svelte-ignore a11y_no_static_element_interactions -->
    <div class="fixed inset-0 z-[100] bg-primary-500/60 backdrop-blur-sm" transition:fade={{ duration: 200 }} onclick={onclose}></div>

    <div class="fixed inset-y-0 right-0 z-[110] flex w-full max-w-[1100px] overflow-hidden border-l border-slate-200 bg-white shadow-2xl" transition:fly={{ x: 800, duration: 400 }}>
        <div class="flex h-full w-full overflow-hidden">
            <LibraryDrawer {allFacts} {allActions} {CONDITION_KEYS} {handleDragStart} />

            <div class="flex-1 flex flex-col overflow-hidden bg-slate-50">
                <!-- HEADER -->
                <div class="flex items-center justify-between bg-white px-8 py-5 border-b-2 border-slate-200 shadow-sm z-10">
                    <div class="flex items-center gap-4">
                        <div class="bg-primary-500 text-white rounded-xl p-3 border-2 border-b-4 border-primary-700 shadow-lg shadow-primary-500/10">
                            <Zap size={22} fill="white" />
                        </div>
                        <div>
                            <h3 class="font-display text-xl font-black text-primary-500 tracking-tight">{isEdit ? 'Edit Logic Rule' : 'New Logic Rule'}</h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="flex h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <p class="text-[9px] font-black tracking-[0.2em] text-slate-400 uppercase">Decision Engine</p>
                            </div>
                        </div>
                    </div>
                    <button 
                        onclick={onclose} 
                        class="press-active flex h-10 w-10 items-center justify-center rounded-xl bg-white text-slate-400 hover:bg-rose-50 hover:text-rose-500 border-2 border-b-4 border-slate-200 transition-all"
                    >
                        <X size={20} />
                    </button>
                </div>

                <!-- MAIN FORM CANVAS -->
                <div class="flex-1 overflow-y-auto p-8 custom-scrollbar bg-slate-50/50">
                    <form id="rule-form" onsubmit={handleSubmit} class="mx-auto max-w-5xl space-y-6 pb-20">
                        
                        <!-- Metadata Card -->
                        <div class="rounded-3xl border-2 border-b-6 border-slate-200 bg-white p-7 shadow-sm">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-primary-50 text-primary-500 border-2 border-slate-100">
                                    <Settings2 size={16} />
                                </div>
                                <h3 class="text-[10px] font-black tracking-[0.2em] text-primary-500 uppercase">Rule Configuration</h3>
                            </div>
                            
                            <div class="grid grid-cols-12 gap-8">
                                <div class="col-span-8">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">Rule Master Title</span>
                                    <input 
                                        type="text" 
                                        bind:value={form.name} 
                                        placeholder="Contoh: Optimasi Pembelajaran Siswa Berprestasi"
                                        class="w-full text-lg font-black text-primary-500 placeholder:text-slate-200 focus:outline-none"
                                    />
                                </div>
                                <div class="col-span-4">
                                    <Input label="Priority Level" variant="white" type="number" bind:value={form.priority} error={form.errors.priority} className="font-mono text-sm py-2" placeholder="1-100" />
                                </div>
                            </div>
                        </div>

                        <!-- WHEN Card -->
                        <div class="rounded-3xl border-2 border-b-6 border-slate-200 bg-white overflow-hidden shadow-sm {invalidDropZone === 'required' ? 'ring-4 ring-rose-500 animate-shake' : ''}">
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
                                    <Button variant="secondary" size="sm" onclick={() => addCondition()} icon={Plus}>
                                        ADD FACT
                                    </Button>
                                </div>
                                
                                <div 
                                    class="transition-all rounded-2xl border-2 border-dashed border-slate-100 p-2 {isDraggingOver === 'required' ? 'bg-indigo-50/30 border-indigo-200' : ''}"
                                    ondragover={(e) => { e.preventDefault(); isDraggingOver = 'required'; }}
                                    ondragleave={() => isDraggingOver = null}
                                    ondrop={(e) => handleDrop(e, 'required')}
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
                                                {@const allowedValues = ALLOWED_VALUES[fact.key]}
                                                
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
                                                        <select bind:value={form.facts[i].operator} disabled={isGCode} class="h-9 rounded-lg border-none bg-slate-50 px-3 text-[10px] font-black text-slate-900 focus:ring-0">
                                                            {#each [{ value: '==', label: '=' }, { value: '!=', label: '≠' }, { value: '>', label: '>' }, { value: '<', label: '<' }, { value: '>=', label: '≥' }, { value: '<=', label: '≤' }] as op}<option value={op.value}>{op.label}</option>{/each}
                                                        </select>

                                                        {#if allowedValues}
                                                            <select bind:value={form.facts[i].value} disabled={isGCode} class="h-9 min-w-[100px] rounded-lg border-none bg-slate-50 px-3 text-[10px] font-black text-slate-900 focus:ring-0">
                                                                {#each allowedValues as av}<option value={av.value}>{av.label}</option>{/each}
                                                            </select>
                                                        {:else}
                                                            <input type="text" bind:value={form.facts[i].value} readonly={isGCode} class="h-9 w-20 rounded-lg border-none bg-slate-50 px-3 text-center text-[10px] font-black text-slate-900 focus:ring-0" />
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

                        <!-- DEDUCE Card -->
                        <div class="rounded-3xl border-2 border-b-6 border-slate-200 bg-white overflow-hidden shadow-sm {invalidDropZone === 'deduced' ? 'ring-4 ring-rose-500 animate-shake' : ''}">
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
                                    <Button variant="secondary" size="sm" onclick={() => addDiagnosis('')} icon={Plus}>
                                        NEW DEDUCTION
                                    </Button>
                                </div>

                                <div 
                                    class="transition-all rounded-2xl border-2 border-dashed border-slate-100 p-2 {isDraggingOver === 'deduced' ? 'bg-emerald-50/30 border-emerald-200' : ''}"
                                    ondragover={(e) => { e.preventDefault(); isDraggingOver = 'deduced'; }}
                                    ondragleave={() => isDraggingOver = null}
                                    ondrop={(e) => handleDrop(e, 'deduced')}
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
                                                        onclick={() => form.deduced_facts = form.deduced_facts.filter((_, idx) => idx !== i)} 
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

                        <!-- DO Card -->
                        <div class="rounded-3xl border-2 border-b-6 border-slate-200 bg-white overflow-hidden shadow-sm {invalidDropZone === 'action' ? 'ring-4 ring-rose-500 animate-shake' : ''}">
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
                                    class="transition-all rounded-2xl border-2 border-dashed border-slate-100 p-2 {isDraggingOver === 'action' ? 'bg-amber-50/30 border-amber-200' : ''}"
                                    ondragover={(e) => { e.preventDefault(); isDraggingOver = 'action'; }}
                                    ondragleave={() => isDraggingOver = null}
                                    ondrop={(e) => handleDrop(e, 'action')}
                                >
                                    {#if form.action_ids.length === 0}
                                        <div class="flex flex-col items-center justify-center py-12 text-slate-300">
                                            <Target size={32} class="mb-2 opacity-20" />
                                            <span class="text-[10px] font-black uppercase tracking-widest">Drop actions here</span>
                                        </div>
                                    {:else}
                                        <div class="space-y-4">
                                            {#each form.action_ids as actionId, i}
                                                {@const action = allActions.find((a) => a.id === actionId)}
                                                <div class="flex items-center gap-6 p-5 rounded-2xl bg-slate-50 border-2 border-b-4 border-slate-200 transition-all hover:border-amber-300 group">
                                                    <div class="flex-1 min-w-0">
                                                        <span class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em]">{actionId}</span>
                                                        <p class="text-sm font-bold text-slate-700">{action?.name || actionId}</p>
                                                    </div>
                                                    <button 
                                                        type="button" 
                                                        onclick={() => form.action_ids = form.action_ids.filter(id => id !== actionId)} 
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

                        <!-- FEEDBACK -->
                        <div class="rounded-3xl border-2 border-b-6 border-slate-200 bg-white p-7 shadow-sm">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-50 text-rose-600 border-2 border-b-4 border-rose-100 shadow-sm">
                                    <MessageSquareQuote size={24} />
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-xs font-black uppercase tracking-[0.15em] text-primary-500">SAY (Feedback)</span>
                                    <span class="text-[10px] font-bold text-rose-400 mt-0.5">Pesan untuk mahasiswa</span>
                                </div>
                            </div>
                            <textarea bind:value={form.recommendation} class="w-full rounded-2xl bg-slate-50 border-2 border-slate-100 p-5 text-sm font-bold text-primary-500 focus:bg-white focus:border-rose-400 focus:outline-none transition-all min-h-[100px]" placeholder="Pesan motivasi atau instruksi perbaikan..."></textarea>
                        </div>
                    </form>
                </div>

                <!-- FOOTER ACTIONS -->
                <div class="flex items-center justify-between border-t-2 border-slate-200 bg-white px-8 py-5 z-10 shadow-2xl">
                    <div class="flex items-center gap-6">
                        <Toggle bind:checked={form.is_active} label="Active Status" />
                        <div class="h-8 w-px bg-slate-100"></div>
                        <div class="flex flex-col">
                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em] mb-0.5">Rule ID</span>
                            <span class="text-xs font-black text-primary-500 font-mono tracking-tight">{form.id}</span>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <Button variant="secondary" size="sm" onclick={onclose}>
                            Cancel
                        </Button>
                        <Button 
                            type="submit" 
                            form="rule-form" 
                            variant="primary" 
                            size="sm"
                            icon={Save} 
                            disabled={form.processing}
                        >
                            {form.processing ? 'Syncing...' : 'Publish Rule'}
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}
