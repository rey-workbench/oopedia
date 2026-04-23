<script lang="ts">
    import { useForm } from '@inertiajs/svelte';
    import Modal from '@/components/ui/Modal.svelte';
    import Input from '@/components/ui/Input.svelte';
    import Select from '@/components/ui/Select.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Alert from '@/components/ui/Alert.svelte';
    import { Save, CheckCircle2, X, Info, Zap, LayoutGrid } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import type { AdaptiveFact, AdaptiveAction } from '@/types/models';

    let {
        show = false,
        rule = null,
        allFacts,
        allActions,
        onclose,
    } = $props<{
        show: boolean;
        rule: any | null;
        allFacts: AdaptiveFact[];
        allActions: AdaptiveAction[];
        onclose: () => void;
    }>();

    const isEdit = $derived(!!rule);

    let form = useForm({
        rule_code: '',
        name: '',
        domain: 'Interaction',
        priority: 10,
        action_id: '',
        required_facts: [] as string[],
        deduced_facts: [] as string[],
        is_active: true,
    });

    $effect(() => {
        if (show) {
            if (rule) {
                form.rule_code = rule.rule_code;
                form.name = rule.name;
                form.domain = rule.domain;
                form.priority = rule.priority;
                form.action_id = rule.action_id;
                form.required_facts = rule.required_facts || [];
                form.deduced_facts = rule.deduced_facts || [];
                form.is_active = rule.is_active;
            } else {
                form.reset();
            }
        }
    });

    const domains = [
        'Safety',
        'Project',
        'Achievement',
        'Recovery',
        'Progression',
        'Interaction',
        'Deduction',
        'Style',
    ];

    function handleSubmit(e: Event) {
        e.preventDefault();
        if (isEdit) {
            form.put(`${ROUTES.ADMIN.ADAPTIVE_ANALYTICS}/${rule.real_id || rule.id}`, {
                onSuccess: () => onclose(),
            });
        } else {
            form.post(ROUTES.ADMIN.ADAPTIVE_ANALYTICS, {
                onSuccess: () => onclose(),
            });
        }
    }

    function toggleFact(type: 'required' | 'deduced', factCode: string) {
        const key = type === 'required' ? 'required_facts' : 'deduced_facts';
        const current = form[key] as string[];
        if (current.includes(factCode)) {
            form[key] = current.filter((c: string) => c !== factCode);
        } else {
            form[key] = [...current, factCode];
        }
    }
</script>

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

<Modal {show} {onclose} maxWidth="2xl">
    <div class="flex max-h-[90vh] flex-col bg-white">
        <div
            class="z-10 flex shrink-0 items-center justify-between border-b border-slate-100 bg-white/80 px-6 py-4 backdrop-blur"
        >
            <h3 class="flex items-center gap-2 text-lg font-black text-slate-800">
                <LayoutGrid size={18} class="text-primary-600" />
                {isEdit ? 'Ubah Aturan' : 'Tambah Aturan Baru'}
            </h3>
            <button
                onclick={onclose}
                class="rounded-full p-2 text-slate-400 transition-colors hover:bg-slate-50 hover:text-slate-600"
            >
                <X size={20} />
            </button>
        </div>

        <div class="custom-scrollbar flex-1 overflow-y-auto p-6">
            <form id="rule-form" onsubmit={handleSubmit} class="space-y-8">
                <div
                    class="flex items-center justify-between rounded-2xl border border-slate-100 bg-slate-50 p-4"
                >
                    <span class="text-xs font-black tracking-widest text-slate-400 uppercase"
                        >Status Aturan</span
                    >
                    <div class="flex items-center gap-3">
                        <span
                            class="text-xs font-bold {form.is_active
                                ? 'text-emerald-600'
                                : 'text-slate-400'} uppercase"
                            >{form.is_active ? 'Aktif' : 'Nonaktif'}</span
                        >
                        <input
                            type="checkbox"
                            bind:checked={form.is_active}
                            class="text-primary-600 focus:ring-primary-500 h-5 w-5 rounded border-slate-300"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <Input
                        label="Kode Aturan"
                        placeholder="Misal: R01"
                        required
                        bind:value={form.rule_code}
                        error={form.errors.rule_code}
                        className="font-bold tracking-widest"
                    />
                    <Input
                        label="Nama Aturan"
                        placeholder="Misal: Promosi Standar"
                        required
                        bind:value={form.name}
                        error={form.errors.name}
                    />
                    <Select
                        label="Domain"
                        options={domains.map((d) => ({ value: d, label: d }))}
                        required
                        bind:value={form.domain}
                        error={form.errors.domain}
                    />
                    <Input
                        label="Prioritas"
                        type="number"
                        required
                        bind:value={form.priority}
                        error={form.errors.priority}
                    />
                </div>

                <Select
                    label="Aksi Adaptif (Output)"
                    placeholder="Pilih aksi..."
                    options={allActions.map((a: { id: number; code: string; name: string }) => ({
                        value: a.id,
                        label: `[${a.code}] ${a.name}`,
                    }))}
                    required
                    bind:value={form.action_id}
                    error={form.errors.action_id}
                />

                <Alert variant="primary" class="bg-primary-50/50 border-primary-100 p-4">
                    <div class="flex gap-4">
                        <Info size={16} class="text-primary-600 mt-0.5 shrink-0" />
                        <p class="text-[11px] leading-relaxed font-bold text-slate-600">
                            Pilih fakta yang menjadi syarat (Required) agar aturan ini tereksekusi,
                            dan fakta baru apa yang dihasilkan (Deduce) jika ada.
                        </p>
                    </div>
                </Alert>

                <div class="space-y-6">
                    <div class="space-y-3">
                        <h4
                            class="flex items-center gap-2 text-xs font-black tracking-widest text-emerald-600 uppercase"
                        >
                            <CheckCircle2 size={16} /> Fakta Prasyarat (Harus Ada)
                        </h4>
                        <div
                            class="custom-scrollbar grid max-h-[250px] grid-cols-1 gap-2 overflow-y-auto pr-2 sm:grid-cols-2"
                        >
                            {#each allFacts as fact}
                                <button
                                    type="button"
                                    onclick={() => toggleFact('required', fact.code)}
                                    class="group flex items-center gap-3 rounded-xl border-2 p-3 text-left transition-all {form.required_facts.includes(
                                        fact.code
                                    )
                                        ? 'border-emerald-500 bg-emerald-50 shadow-sm'
                                        : 'border-slate-50 bg-white hover:border-slate-100'}"
                                >
                                    <div class="flex flex-col gap-0.5">
                                        <span
                                            class="text-[9px] font-black {form.required_facts.includes(
                                                fact.code
                                            )
                                                ? 'text-emerald-600'
                                                : 'text-slate-300'} tracking-widest uppercase"
                                            >{fact.code}</span
                                        >
                                        <span
                                            class="text-[10px] font-bold {form.required_facts.includes(
                                                fact.code
                                            )
                                                ? 'text-slate-900'
                                                : 'text-slate-500'} line-clamp-1 leading-tight"
                                            >{fact.name}</span
                                        >
                                    </div>
                                </button>
                            {/each}
                        </div>
                    </div>

                    <div class="space-y-3">
                        <h4
                            class="flex items-center gap-2 text-xs font-black tracking-widest text-purple-600 uppercase"
                        >
                            <Zap size={16} /> Fakta Hasil Deduksi (Deduksi)
                        </h4>
                        <div
                            class="custom-scrollbar grid max-h-[250px] grid-cols-1 gap-2 overflow-y-auto pr-2 sm:grid-cols-2"
                        >
                            {#each allFacts as fact}
                                <button
                                    type="button"
                                    onclick={() => toggleFact('deduced', fact.code)}
                                    class="group flex items-center gap-3 rounded-xl border-2 p-3 text-left transition-all {form.deduced_facts.includes(
                                        fact.code
                                    )
                                        ? 'border-purple-500 bg-purple-50 shadow-sm'
                                        : 'border-slate-50 bg-white hover:border-slate-100'}"
                                >
                                    <div class="flex flex-col gap-0.5">
                                        <span
                                            class="text-[9px] font-black {form.deduced_facts.includes(
                                                fact.code
                                            )
                                                ? 'text-purple-600'
                                                : 'text-slate-300'} tracking-widest uppercase"
                                            >{fact.code}</span
                                        >
                                        <span
                                            class="text-[10px] font-bold {form.deduced_facts.includes(
                                                fact.code
                                            )
                                                ? 'text-slate-900'
                                                : 'text-slate-500'} line-clamp-1 leading-tight"
                                            >{fact.name}</span
                                        >
                                    </div>
                                </button>
                            {/each}
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div
            class="flex shrink-0 items-center justify-between rounded-b-3xl border-t border-slate-100 bg-slate-50 px-6 py-4"
        >
            <Button variant="ghost" onclick={onclose}>Batal</Button>
            <Button
                type="submit"
                form="rule-form"
                variant="primary"
                icon={Save}
                disabled={form.processing}
            >
                {form.processing ? 'Menyimpan...' : 'Simpan Aturan'}
            </Button>
        </div>
    </div>
</Modal>
