<script lang="ts">
    import { useForm } from '@inertiajs/svelte';
    import Input from '@/components/ui/Input.svelte';
    import Select from '@/components/ui/Select.svelte';
    import Button from '@/components/ui/Button.svelte';
    import {
        Save,
        X,
        Zap,
        Activity,
        Shield,
        Trophy,
        RefreshCw,
        Target,
        TrendingUp,
        Code2,
    } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import type { AdaptiveFact, AdaptiveAction } from '@/types/models';
    import { fly, fade } from 'svelte/transition';

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
        code: '',
        name: '',
        domain: 'Interaction',
        priority: 10,
        action_id: '',
        required_facts: [] as string[],
        deduced_facts: [] as string[],
        is_active: true,
        description: '',
    });

    $effect(() => {
        if (show) {
            if (rule) {
                form.code = rule.code;
                form.name = rule.name;
                form.domain = rule.domain;
                form.priority = rule.priority;
                form.action_id = rule.action_id;
                form.required_facts = rule.required_facts || [];
                form.deduced_facts = rule.deduced_facts || [];
                form.is_active = rule.is_active;
                form.description = rule.description || '';
            } else {
                form.reset();
            }
        }
    });

    const domains = [
        { id: 'Safety', icon: Shield, color: 'text-rose-500', bg: 'bg-rose-50' },
        { id: 'Project', icon: Target, color: 'text-blue-500', bg: 'bg-blue-50' },
        { id: 'Achievement', icon: Trophy, color: 'text-amber-500', bg: 'bg-amber-50' },
        { id: 'Recovery', icon: RefreshCw, color: 'text-emerald-500', bg: 'bg-emerald-50' },
        { id: 'Progression', icon: TrendingUp, color: 'text-purple-500', bg: 'bg-purple-50' },
        { id: 'Interaction', icon: Activity, color: 'text-cyan-500', bg: 'bg-cyan-50' },
        { id: 'Deduction', icon: Code2, color: 'text-slate-500', bg: 'bg-slate-50' },
    ];

    function handleSubmit(e: Event) {
        e.preventDefault();
        const url = isEdit
            ? `${ROUTES.ADMIN.ADAPTIVE_RULES}/${rule.real_id || rule.id}`
            : ROUTES.ADMIN.ADAPTIVE_RULES;

        form.submit(isEdit ? 'put' : 'post', url, {
            onSuccess: () => onclose(),
            preserveScroll: true,
        });
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

{#if show}
    <!-- Overlay -->
    <!-- svelte-ignore a11y_click_events_have_key_events -->
    <!-- svelte-ignore a11y_no_static_element_interactions -->
    <div
        class="fixed inset-0 z-200 bg-slate-900/40 backdrop-blur-sm"
        transition:fade={{ duration: 200 }}
        onclick={onclose}
    ></div>

    <!-- n8n-style Side Panel -->
    <div
        class="fixed inset-y-0 right-0 z-210 flex w-full max-w-xl flex-col border-l border-slate-200 bg-white shadow-2xl"
        transition:fly={{ x: 500, duration: 300 }}
    >
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="bg-primary-50 text-primary-600 rounded-xl p-2">
                    <Zap size={20} />
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-800">
                        {isEdit ? 'Node Editor: Aturan' : 'Node Editor: Aturan Baru'}
                    </h3>
                    <p class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">
                        Configure logic & flow
                    </p>
                </div>
            </div>
            <button
                onclick={onclose}
                class="rounded-full p-2 text-slate-400 transition-colors hover:bg-slate-50 hover:text-slate-600"
                aria-label="Close"
            >
                <X size={20} />
            </button>
        </div>

        <!-- Content -->
        <div class="custom-scrollbar flex-1 overflow-y-auto bg-slate-50/50 p-6">
            <form id="rule-form" onsubmit={handleSubmit} class="space-y-8">
                <!-- 1. Trigger Section (Required Facts) -->
                <section class="space-y-4">
                    <div
                        class="flex items-center gap-2 text-[10px] font-black tracking-widest text-blue-500 uppercase"
                    >
                        <div class="h-px flex-1 bg-blue-100"></div>
                        <span>1. Triggers (IF)</span>
                        <div class="h-px w-4 bg-blue-100"></div>
                    </div>

                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                        {#each allFacts.filter((f: AdaptiveFact) => !f.code.startsWith('V')) as fact}
                            <button
                                type="button"
                                onclick={() => toggleFact('required', fact.code)}
                                class="group flex items-center gap-3 rounded-xl border-2 p-3 text-left transition-all {form.required_facts.includes(
                                    fact.code
                                )
                                    ? 'border-blue-500 bg-white shadow-md ring-2 ring-blue-50'
                                    : 'border-white bg-white/50 shadow-sm hover:border-blue-200'}"
                            >
                                <div class="flex flex-col">
                                    <span
                                        class="text-[9px] font-black tracking-widest uppercase {form.required_facts.includes(
                                            fact.code
                                        )
                                            ? 'text-blue-600'
                                            : 'text-slate-400'}">{fact.code}</span
                                    >
                                    <span
                                        class="text-[11px] font-bold {form.required_facts.includes(
                                            fact.code
                                        )
                                            ? 'text-slate-900'
                                            : 'text-slate-600'}">{fact.name}</span
                                    >
                                </div>
                            </button>
                        {/each}
                    </div>
                </section>

                <div class="flex justify-center">
                    <div class="h-8 w-px bg-slate-200"></div>
                </div>

                <!-- 2. Logic & Metadata Section -->
                <section class="space-y-4">
                    <div
                        class="flex items-center gap-2 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                    >
                        <div class="h-px flex-1 bg-slate-200"></div>
                        <span>2. Node Configuration</span>
                        <div class="h-px w-4 bg-slate-200"></div>
                    </div>

                    <div
                        class="space-y-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                    >
                        <div class="grid grid-cols-2 gap-4">
                            <Input
                                label="Kode Node"
                                bind:value={form.code}
                                placeholder="R-01"
                                className="font-mono font-bold"
                            />
                            <Input label="Prioritas" type="number" bind:value={form.priority} />
                        </div>
                        <Input
                            label="Nama Node / Aturan"
                            bind:value={form.name}
                            placeholder="Nama deskriptif..."
                        />
                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700">Domain Strategi</span>
                            <div class="flex flex-wrap gap-2">
                                {#each domains as domain}
                                    <button
                                        type="button"
                                        onclick={() => (form.domain = domain.id)}
                                        class="flex items-center gap-2 rounded-xl border-2 px-3 py-2 transition-all {form.domain ===
                                        domain.id
                                            ? 'border-primary-500 bg-primary-50/50'
                                            : 'border-slate-50 bg-slate-50 text-slate-500 hover:border-slate-200'}"
                                    >
                                        <domain.icon
                                            size={14}
                                            class={form.domain === domain.id
                                                ? 'text-primary-600'
                                                : ''}
                                        />
                                        <span class="text-xs font-bold">{domain.id}</span>
                                    </button>
                                {/each}
                            </div>
                        </div>
                        <Input
                            label="Deskripsi (Opsional)"
                            bind:value={form.description}
                            placeholder="Tujuan dari aturan ini..."
                        />
                    </div>
                </section>

                <div class="flex justify-center">
                    <div class="h-8 w-px bg-slate-200"></div>
                </div>

                <!-- 3. Output Section (Action & Deductions) -->
                <section class="space-y-4 pb-12">
                    <div
                        class="flex items-center gap-2 text-[10px] font-black tracking-widest text-emerald-500 uppercase"
                    >
                        <div class="h-px flex-1 bg-emerald-100"></div>
                        <span>3. Output (THEN)</span>
                        <div class="h-px w-4 bg-emerald-100"></div>
                    </div>

                    <div class="space-y-4">
                        <Select
                            label="Aksi Utama"
                            options={allActions.map((a: AdaptiveAction) => ({
                                value: a.id,
                                label: `[${a.code}] ${a.name}`,
                            }))}
                            bind:value={form.action_id}
                        />

                        <div class="space-y-3">
                            <span class="text-xs font-bold text-slate-700"
                                >Deduksi Fakta (Logic Pipe)</span
                            >
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                {#each allFacts.filter( (f: AdaptiveFact) => f.code.startsWith('V') ) as fact}
                                    <button
                                        type="button"
                                        onclick={() => toggleFact('deduced', fact.code)}
                                        class="group flex items-center gap-3 rounded-xl border-2 p-3 text-left transition-all {form.deduced_facts.includes(
                                            fact.code
                                        )
                                            ? 'border-purple-500 bg-purple-50 shadow-sm'
                                            : 'border-slate-100 bg-white hover:border-purple-200'}"
                                    >
                                        <div class="flex flex-col">
                                            <span
                                                class="text-[9px] font-black tracking-widest uppercase {form.deduced_facts.includes(
                                                    fact.code
                                                )
                                                    ? 'text-purple-600'
                                                    : 'text-slate-400'}">{fact.code}</span
                                            >
                                            <span
                                                class="text-[11px] font-bold {form.deduced_facts.includes(
                                                    fact.code
                                                )
                                                    ? 'text-slate-900'
                                                    : 'text-slate-600'}">{fact.name}</span
                                            >
                                        </div>
                                    </button>
                                {/each}
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>

        <!-- Footer -->
        <div
            class="flex items-center justify-between border-t border-slate-100 bg-white px-6 py-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]"
        >
            <div class="flex items-center gap-4">
                <span class="text-xs font-bold text-slate-400">Status Node</span>
                <button
                    type="button"
                    onclick={() => (form.is_active = !form.is_active)}
                    class="relative h-6 w-12 rounded-full transition-colors {form.is_active
                        ? 'bg-emerald-500'
                        : 'bg-slate-200'}"
                    aria-label="Toggle Node Active Status"
                >
                    <div
                        class="absolute top-1 left-1 h-4 w-4 rounded-full bg-white transition-transform {form.is_active
                            ? 'translate-x-6'
                            : 'translate-x-0'}"
                    ></div>
                </button>
            </div>
            <div class="flex gap-2">
                <Button variant="ghost" onclick={onclose}>Batal</Button>
                <Button
                    type="submit"
                    form="rule-form"
                    variant="primary"
                    icon={Save}
                    disabled={form.processing}
                >
                    {form.processing ? 'Menyimpan...' : 'Simpan Node'}
                </Button>
            </div>
        </div>
    </div>
{/if}
