<script lang="ts">
    import { useForm } from '@inertiajs/svelte';
    import Input from '@/components/ui/Input.svelte';
    import Select from '@/components/ui/Select.svelte';
    import Button from '@/components/ui/Button.svelte';
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Alert from '@/components/ui/Alert.svelte';
    import type { AdaptiveFact, AdaptiveAction, AdaptiveRule } from '@/types/models';
    import { Save, ArrowLeft, CheckCircle2, X, Info, Zap, LayoutGrid } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';

    let { rule, allFacts, allActions } = $props<{ 
        rule: AdaptiveRule,
        allFacts: AdaptiveFact[], 
        allActions: AdaptiveAction[] 
    }>();

    const form = useForm({
        rule_code: rule.rule_code,
        name: rule.name,
        domain: rule.domain || 'Interaction',
        priority: rule.priority,
        action_id: rule.action_id as string | number,
        required_facts: rule.required_facts || [],
        forbidden_facts: rule.forbidden_facts || [],
        is_active: rule.is_active
    });

    const domains = ['Safety', 'Project', 'Achievement', 'Recovery', 'Progression', 'Interaction'];

    function handleSubmit(e: Event) {
        e.preventDefault();
        form.put(`${ROUTES.ADMIN.ADAPTIVE_ANALYTICS}/${rule.real_id}`);
    }

    function toggleFact(type: 'required' | 'forbidden', factCode: string) {
        const key = type === 'required' ? 'required_facts' : 'forbidden_facts';
        const current = form[key] as string[];
        if (current.includes(factCode)) {
            form[key] = current.filter(c => c !== factCode);
        } else {
            form[key] = [...current, factCode];
        }
    }
</script>

<App title="Edit Aturan Adaptif - Admin">
    <div class="space-y-12 pb-20">
        <PageHeader
            id="page-header"
            title="Suting Logika Adaptif"
            subtitle="Modifikasi orkestrasi aturan untuk optimasi personalisasi belajar."
        >
            {#snippet actions()}
                <Button href={ROUTES.ADMIN.ADAPTIVE_ANALYTICS} variant="ghost" icon={ArrowLeft}>
                    BATALKAN PERUBAHAN
                </Button>
            {/snippet}
        </PageHeader>

        <form onsubmit={handleSubmit} class="space-y-12">
            <div class="group relative overflow-hidden rounded-3xl border border-slate-100 bg-white p-6 shadow-2xl transition-transform duration-300 hover:-translate-y-1">
                <div class="mb-6 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <LayoutGrid size={18} class="text-primary-600" />
                        Identifikasi Aturan: {rule.rule_code}
                    </h3>
                    <div class="flex items-center gap-3 rounded-full bg-slate-50 px-4 py-1.5 border border-slate-100">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Status Aturan:</span>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold {form.is_active ? 'text-emerald-600' : 'text-slate-400'} uppercase">{form.is_active ? 'Aktif' : 'Nonaktif'}</span>
                            <input type="checkbox" bind:checked={form.is_active} class="w-4 h-4 rounded text-primary-600 border-slate-300 focus:ring-primary-500" />
                        </div>
                    </div>
                </div>

                <div class="space-y-10 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-6">
                            <div class="grid grid-cols-2 gap-6">
                                <Input 
                                    label="Kode Identifikasi" 
                                    placeholder="Misal: R01" 
                                    required
                                    bind:value={form.rule_code}
                                    error={form.errors.rule_code}
                                    className="font-bold tracking-widest"
                                />
                                <Input 
                                    label="Label Naratif" 
                                    placeholder="Misal: Promosi Standar" 
                                    required
                                    bind:value={form.name}
                                    error={form.errors.name}
                                />
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <Select 
                                    label="Domain Pedagogis" 
                                    options={domains.map(d => ({ value: d, label: d }))}
                                    required
                                    bind:value={form.domain}
                                    error={form.errors.domain}
                                />
                                <Input 
                                    label="Indeks Prioritas" 
                                    type="number" 
                                    required
                                    bind:value={form.priority}
                                    error={form.errors.priority}
                                />
                            </div>

                            <Select 
                                label="Aksi Adaptif (Output Keputusan)" 
                                placeholder="Pilih aksi yang akan dipicu..."
                                options={allActions.map((a: AdaptiveAction) => ({ value: a.id, label: `[${a.code}] ${a.name}` }))}
                                required
                                bind:value={form.action_id}
                                error={form.errors.action_id}
                            />
                        </div>

                        <div class="space-y-6">
                            <Alert variant="primary" class="bg-primary-50/50 border-primary-100 p-6">
                                <div class="flex gap-4">
                                    <Info size={20} class="text-primary-600 shrink-0 mt-1" />
                                    <div class="space-y-1">
                                        <h4 class="text-[11px] font-black text-primary-900 uppercase tracking-wider">Perhatian Modifikasi</h4>
                                        <p class="text-[10px] leading-relaxed font-bold text-slate-500 uppercase">
                                            Perubahan pada aturan ini akan berdampak langsung pada seluruh sesi kuis mahasiswa yang sedang berjalan. Pastikan logika konsisten.
                                        </p>
                                    </div>
                                </div>
                            </Alert>

                            <div class="rounded-3xl border border-slate-100 bg-slate-50/30 p-8 flex flex-col items-center justify-center text-center gap-4">
                                <div class="h-14 w-14 rounded-full bg-slate-900 flex items-center justify-center text-white shadow-xl shadow-slate-200">
                                    <Zap size={24} fill="currentColor" />
                                </div>
                                <div class="space-y-1">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Metadata Aturan</span>
                                    <p class="text-xs font-bold text-slate-800">Rule database ID: {rule.real_id}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 pt-10 border-t border-slate-100">
                        <!-- Required Facts -->
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-black text-emerald-600 uppercase tracking-widest flex items-center gap-2">
                                    <CheckCircle2 size={16} />
                                    Fakta Prasyarat (Eksis)
                                </h4>
                                <span class="text-[9px] font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded border border-slate-100 uppercase tracking-tighter">Must Exist</span>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                                {#each allFacts as fact}
                                    <button 
                                        type="button"
                                        onclick={() => toggleFact('required', fact.code)}
                                        class="group flex items-center gap-3 p-4 rounded-2xl border-2 transition-all text-left {form.required_facts.includes(fact.code) ? 'bg-emerald-50 border-emerald-500 shadow-lg shadow-emerald-500/10' : 'bg-white border-slate-50 hover:border-slate-100'}"
                                    >
                                        <div class="flex flex-col gap-0.5">
                                            <span class="text-[9px] font-black {form.required_facts.includes(fact.code) ? 'text-emerald-600' : 'text-slate-300'} tracking-widest uppercase transition-colors">{fact.code}</span>
                                            <span class="text-[11px] font-bold {form.required_facts.includes(fact.code) ? 'text-slate-900' : 'text-slate-500'} leading-tight transition-colors">{fact.name}</span>
                                        </div>
                                    </button>
                                {/each}
                            </div>
                        </div>

                        <!-- Forbidden Facts -->
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-black text-rose-600 uppercase tracking-widest flex items-center gap-2">
                                    <X size={16} />
                                    Fakta Penghalang (Absen)
                                </h4>
                                <span class="text-[9px] font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded border border-slate-100 uppercase tracking-tighter">Must Not Exist</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                                {#each allFacts as fact}
                                    <button 
                                        type="button"
                                        onclick={() => toggleFact('forbidden', fact.code)}
                                        class="group flex items-center gap-3 p-4 rounded-2xl border-2 transition-all text-left {form.forbidden_facts.includes(fact.code) ? 'bg-rose-50 border-rose-500 shadow-lg shadow-rose-500/10' : 'bg-white border-slate-50 hover:border-slate-100'}"
                                    >
                                        <div class="flex flex-col gap-0.5">
                                            <span class="text-[9px] font-black {form.forbidden_facts.includes(fact.code) ? 'text-rose-600' : 'text-slate-300'} tracking-widest uppercase transition-colors">{fact.code}</span>
                                            <span class="text-[11px] font-bold {form.forbidden_facts.includes(fact.code) ? 'text-slate-900' : 'text-slate-500'} leading-tight transition-colors">{fact.name}</span>
                                        </div>
                                    </button>
                                {/each}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-6 border-t border-slate-100 pt-10">
                        <div class="flex items-center gap-3">
                            <Zap size={14} class="text-primary-600" />
                            <span class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">Status Validasi: Logika Siap Disimpan</span>
                        </div>

                        <Button 
                            type="submit" 
                            variant="primary" 
                            size="lg"
                            class="shadow-xl shadow-primary-900/20 px-8"
                            icon={Save} 
                            disabled={form.processing}
                        >
                            {form.processing ? 'MEMPROSES...' : 'PERBARUI ATURAN'}
                        </Button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</App>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }
</style>
