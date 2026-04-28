<script lang="ts">
    import Button from '@/components/ui/Button.svelte';
    import type { AdminAdaptiveRuleProps } from '@/types/states/admin';
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import ForwardChaining from './ForwardChaining.svelte';
    import RuleEditorModal from './RuleEditorModal.svelte';
    import ActionEditorModal from './ActionEditorModal.svelte';
    import { untrack } from 'svelte';
    import { AdaptiveRuleState } from '@/states/Admin/AdaptiveRuleState.svelte';
    import { Plus, GitBranch, Target, Layers, Settings2, Trash2, Pencil } from 'lucide-svelte';
    import { router } from '@inertiajs/svelte';
    import { ROUTES } from '@/utils/route';

    let props: AdminAdaptiveRuleProps = $props();

    let activeTab = $state<'visualization' | 'rules' | 'actions'>('visualization');
    let isRuleModalOpen = $state(false);
    let isActionModalOpen = $state(false);
    let editingRule = $state<any>(null);
    let editingAction = $state<any>(null);

    function openCreateRule() {
        editingRule = null;
        isRuleModalOpen = true;
    }

    function openEditRule(rule: any) {
        editingRule = rule;
        isRuleModalOpen = true;
    }

    function openCreateAction() {
        editingAction = null;
        isActionModalOpen = true;
    }

    function openEditAction(action: any) {
        editingAction = action;
        isActionModalOpen = true;
    }

    function deleteAction(id: number) {
        if (confirm('Apakah Anda yakin ingin menghapus aksi ini?')) {
            router.delete(ROUTES.ADMIN.ADAPTIVE_ACTIONS.DESTROY(id));
        }
    }

    const analyticsState = untrack(() => new AdaptiveRuleState(props));

    const tabs = [
        { id: 'visualization', label: 'Visualisasi Strategi', icon: GitBranch },
        { id: 'rules', label: 'Daftar Aturan', icon: Layers },
        { id: 'actions', label: 'Daftar Aksi', icon: Target },
    ] as const;
</script>

<App title="Analytics Engine Adaptif - Admin">
    <div class="space-y-8">
        <PageHeader
            id="page-header"
            title="Analytics Engine Adaptif"
            subtitle="Orkestrasi alur logika dan visualisasi keputusan sistem adaptif."
        >
            {#snippet actions()}
                <div class="flex items-center gap-4">
                    {#if activeTab === 'rules'}
                        <Button
                            variant="primary"
                            size="md"
                            icon={Plus}
                            onclick={openCreateRule}
                            class="shadow-primary-900/10 shadow-xl"
                        >
                            TAMBAH ATURAN
                        </Button>
                    {:else if activeTab === 'actions'}
                        <Button
                            variant="primary"
                            size="md"
                            icon={Plus}
                            onclick={openCreateAction}
                            class="shadow-xl shadow-emerald-900/10"
                        >
                            TAMBAH AKSI
                        </Button>
                    {/if}
                </div>
            {/snippet}
        </PageHeader>

        <!-- Tabs Navigation -->
        <div class="flex items-center gap-2 border-b border-slate-100 px-2">
            {#each tabs as tab}
                <button
                    onclick={() => (activeTab = tab.id)}
                    class="relative flex items-center gap-2 px-6 py-4 text-xs font-black tracking-widest uppercase transition-all {activeTab ===
                    tab.id
                        ? 'text-primary-600'
                        : 'text-slate-400 hover:text-slate-600'}"
                >
                    <tab.icon size={16} />
                    {tab.label}
                    {#if activeTab === tab.id}
                        <div
                            class="bg-primary-500 absolute bottom-0 left-0 h-1 w-full rounded-t-full"
                        ></div>
                    {/if}
                </button>
            {/each}
        </div>

        {#if activeTab === 'visualization'}
            <div class="animate-in fade-in slide-in-from-bottom-4 duration-500">
                <ForwardChaining {analyticsState} onedit={openEditRule} />
            </div>
        {:else if activeTab === 'rules'}
            <div class="animate-in fade-in slide-in-from-bottom-4 space-y-6 duration-500">
                {#each props.rulesByDomain as domain}
                    <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="flex items-center gap-3 text-sm font-black text-slate-800">
                                <div class="bg-primary-500 h-2 w-2 rounded-full"></div>
                                Domain: {domain.domain}
                                <span
                                    class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] text-slate-500"
                                    >{domain.count} Aturan</span
                                >
                            </h3>
                        </div>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                            {#each domain.rules as rule}
                                <div
                                    class="group hover:border-primary-100 relative overflow-hidden rounded-2xl border border-slate-50 bg-slate-50/50 p-5 transition-all hover:bg-white hover:shadow-xl"
                                >
                                    <div class="mb-3 flex items-start justify-between">
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="text-primary-500 font-mono text-[10px] font-black tracking-widest uppercase"
                                                >{rule.code}</span
                                            >
                                            <h4 class="text-sm font-bold text-slate-900">
                                                {rule.name}
                                            </h4>
                                        </div>
                                        <button
                                            onclick={() => openEditRule(rule)}
                                            class="hover:bg-primary-50 hover:text-primary-600 rounded-xl p-2 text-slate-400 transition-colors"
                                        >
                                            <Pencil size={14} />
                                        </button>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5">
                                        {#each rule.required_facts as fact}
                                            <span
                                                class="rounded-lg bg-white px-2 py-1 text-[9px] font-bold text-slate-500 shadow-sm"
                                                >{fact}</span
                                            >
                                        {/each}
                                        <span class="mx-1 text-slate-300">→</span>
                                        <span
                                            class="rounded-lg bg-emerald-50 px-2 py-1 text-[9px] font-black text-emerald-600 shadow-sm"
                                            >{rule.action}</span
                                        >
                                    </div>
                                </div>
                            {/each}
                        </div>
                    </div>
                {/each}
            </div>
        {:else if activeTab === 'actions'}
            <div
                class="animate-in fade-in slide-in-from-bottom-4 grid grid-cols-1 gap-6 duration-500 md:grid-cols-2 lg:grid-cols-3"
            >
                {#each props.allActions as action}
                    <div
                        class="group relative overflow-hidden rounded-3xl border border-slate-100 bg-white p-6 transition-all hover:shadow-2xl"
                    >
                        <div class="mb-4 flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600"
                                >
                                    <Target size={20} />
                                </div>
                                <div class="flex flex-col gap-0.5">
                                    <span
                                        class="font-mono text-[10px] font-black tracking-widest text-slate-400 uppercase"
                                        >{action.code}</span
                                    >
                                    <h4 class="text-sm font-black text-slate-900">{action.name}</h4>
                                </div>
                            </div>
                            <div
                                class="flex items-center gap-1 opacity-0 transition-opacity group-hover:opacity-100"
                            >
                                <button
                                    onclick={() => openEditAction(action)}
                                    class="rounded-xl p-2 text-slate-400 transition-colors hover:bg-blue-50 hover:text-blue-600"
                                >
                                    <Settings2 size={16} />
                                </button>
                                <button
                                    onclick={() => deleteAction(action.id)}
                                    class="rounded-xl p-2 text-slate-400 transition-colors hover:bg-rose-50 hover:text-rose-600"
                                >
                                    <Trash2 size={16} />
                                </button>
                            </div>
                        </div>
                        <p
                            class="mb-4 line-clamp-2 text-xs leading-relaxed font-medium text-slate-500"
                        >
                            {action.description}
                        </p>
                        <div class="space-y-2">
                            <div
                                class="flex items-center justify-between text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                <span>Konfigurasi Flow</span>
                                <span class="text-emerald-600"
                                    >{action.instructions?.['flow'] || 'NONE'}</span
                                >
                            </div>
                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                                <div
                                    class="h-full bg-emerald-500"
                                    style="width: {action.instructions?.['flow'] ? '100%' : '20%'}"
                                ></div>
                            </div>
                        </div>
                    </div>
                {/each}
            </div>
        {/if}
    </div>

    <!-- Rule Editor Modal -->
    <RuleEditorModal
        show={isRuleModalOpen}
        rule={editingRule}
        allFacts={props.allFacts}
        allActions={props.allActions}
        onclose={() => (isRuleModalOpen = false)}
    />

    <!-- Action Editor Modal -->
    <ActionEditorModal
        show={isActionModalOpen}
        action={editingAction}
        onclose={() => (isActionModalOpen = false)}
    />
</App>
