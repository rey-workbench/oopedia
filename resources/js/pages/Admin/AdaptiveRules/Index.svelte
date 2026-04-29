
<script lang="ts">
    import StatCard from '@/components/ui/StatCard.svelte';
    import Card from '@/components/ui/Card.svelte';
    import DataTable from '@/components/ui/DataTable.svelte';
    import Badge from '@/components/ui/Badge.svelte';
    import Section from '@/components/ui/Section.svelte';
    import EmptyState from '@/components/ui/EmptyState.svelte';
    import UserAvatar from '@/components/ui/UserAvatar.svelte';
    import { 
        RefreshCw, 
        Maximize2, 
        Minimize2, 
        PlusCircle, 
        Zap, 
        Cpu, 
        Settings2, 
        BrainCircuit,
        Activity,
        GitBranch,
        Target,
        Edit2,
        Trash2,
        CircleHelp
    } from 'lucide-svelte';
    import Button from '@/components/ui/Button.svelte';
    import { ROUTES } from '@/utils/route';
    import { router } from '@inertiajs/svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import App from '@/layouts/App.svelte';
    import type { AdminAdaptiveRuleProps } from '@/types';
    import { AdaptiveRuleState } from '@/states/Admin/AdaptiveRuleState.svelte';
    import { tutorialState } from '@/states/ui/tutorialState.svelte';
    import { untrack } from 'svelte';
    import ForwardChaining from './Partials/ForwardChaining.svelte';
    import ActionEditorModal from './Partials/ActionEditorModal.svelte';

    let props: AdminAdaptiveRuleProps = $props();

    let isActionModalOpen = $state(false);
    let editingAction = $state<any>(null);

    function openEditRule(rule: any) {
        if (!rule) {
            router.visit(ROUTES.ADMIN.ADAPTIVE_RULES.CREATE);
            return;
        }
        router.visit(ROUTES.ADMIN.ADAPTIVE_RULES.EDIT(rule.id));
    }

    function openEditAction(action: any) {
        editingAction = action;
        isActionModalOpen = true;
    }

    const analyticsState = untrack(() => new AdaptiveRuleState(props));

    $effect(() => {
        analyticsState.sync(props as any);
    });

    let isFullscreen = $state(false);
    let chainingRef = $state<any>(null);
    let activeTab = $state<'table' | 'canvas'>('table');

    // Flatten rules for DataTable
    const flattenedRules = $derived(
        analyticsState.rulesByDiagnosis.flatMap((d: any) => d.rules.map((r: any) => ({
            ...r,
            diagnosis_group: d.diagnosis_name
        })))
    );

    const columns = [
        { key: 'name', label: 'Nama Aturan', align: 'left' },
        { key: 'diagnosis', label: 'Diagnosis / Kondisi', align: 'left' },
        { key: 'actions', label: 'Respon Sistem', align: 'left' },
        { key: 'priority', label: 'Prioritas', align: 'center' },
        { key: 'status', label: 'Status', align: 'center' },
        { key: 'operations', label: 'Aksi', align: 'right' },
    ];

    function handleDelete(id: string) {
        if (!confirm('Apakah Anda yakin ingin menghapus aturan ini?')) return;
        router.delete(ROUTES.ADMIN.ADAPTIVE_RULES.DELETE(id));
    }
</script>

<App title="Strategi Adaptif - Admin">
    <div class="{!isFullscreen ? 'space-y-12' : ''}">
        {#if !isFullscreen}
            <PageHeader
                title="Engine Strategi Adaptif"
                subtitle="Pusat kendali logika pembelajaran adaptif berbasis Forward Chaining."
            >
                {#snippet actions()}
                    <Button
                        id="adaptive-rule-create-btn"
                        href={ROUTES.ADMIN.ADAPTIVE_RULES.CREATE}
                        variant="primary"
                        size="sm"
                        icon={PlusCircle}
                        class="shadow-xl shadow-primary-500/20"
                    >
                        BUAT ATURAN BARU
                    </Button>
                {/snippet}
            </PageHeader>

            <!-- Stats Overview -->
            <div id="adaptive-rules-stats" class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <StatCard
                    title="Total Aturan"
                    value={analyticsState.totalRules}
                    icon={BrainCircuit as any}
                    variant="primary"
                    footer="Logika aktif"
                />
                <StatCard
                    title="Fakta & Gejala"
                    value={analyticsState.totalFacts}
                    icon={Cpu as any}
                    variant="success"
                    footer="Variabel kondisi"
                />
                <StatCard
                    title="Intervensi"
                    value={analyticsState.totalActions}
                    icon={Settings2 as any}
                    variant="warning"
                    footer="Aksi pedagogis"
                />
            </div>

            <!-- Tabs Navigation -->
            <div id="adaptive-rules-tabs" class="flex gap-4 border-b-2 border-slate-100 mt-8 mb-4">
                <button 
                    class="px-4 pb-3 text-xs font-black tracking-widest uppercase transition-all {activeTab === 'table' ? 'border-b-4 border-primary-500 text-primary-600' : 'text-slate-400 hover:text-slate-600'}"
                    onclick={() => activeTab = 'table'}
                >
                    Daftar Aturan
                </button>
                <button 
                    class="px-4 pb-3 text-xs font-black tracking-widest uppercase transition-all {activeTab === 'canvas' ? 'border-b-4 border-primary-500 text-primary-600' : 'text-slate-400 hover:text-slate-600'}"
                    onclick={() => activeTab = 'canvas'}
                >
                    Visual Builder
                </button>
            </div>
        {/if}

        {#if activeTab === 'table' && !isFullscreen}
            <!-- Rules Table -->
            <Section 
                title="Manajemen Aturan" 
                subtitle="Daftar seluruh logika inferensi yang terdaftar dalam sistem."
            >
                <DataTable
                    items={flattenedRules}
                    {columns}
                    hideSearch={false}
                >
                    {#snippet empty()}
                        <EmptyState 
                            icon={Zap}
                            title="Belum Ada Aturan"
                            description="Sistem adaptif memerlukan aturan untuk menentukan strategi belajar."
                        />
                    {/snippet}

                    {#snippet row(rule)}
                        <td class="px-6 py-5">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-black text-slate-900 uppercase tracking-tight">{rule.name}</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{rule.id}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col gap-1.5">
                                <Badge variant="secondary" size="xs" class="w-fit">{rule.diagnosis_group}</Badge>
                                <div class="flex flex-wrap gap-1">
                                    {#each rule.required_fact_ids as factId}
                                        <span class="text-[9px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">{factId}</span>
                                    {/each}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-wrap gap-2">
                                {#each rule.actions as action}
                                    <div class="flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 border border-emerald-100">
                                        <div class="h-1 w-1 rounded-full bg-emerald-500"></div>
                                        <span class="text-[10px] font-black text-emerald-700 uppercase">{action.id}</span>
                                    </div>
                                {/each}
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="text-xs font-black text-slate-700">P{rule.priority}</span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <Badge variant={rule.is_active ? 'success' : 'secondary'} size="xs">
                                {rule.is_active ? 'AKTIF' : 'NON-AKTIF'}
                            </Badge>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    icon={Edit2}
                                    onclick={() => openEditRule(rule)}
                                    class="text-slate-400 hover:text-primary-500"
                                />
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    icon={Trash2}
                                    onclick={() => handleDelete(rule.id)}
                                    class="text-slate-300 hover:text-rose-500"
                                />
                            </div>
                        </td>
                    {/snippet}
                </DataTable>
            </Section>
        {/if}

        {#if activeTab === 'canvas'}
            {#if isFullscreen}
                <div class="fixed inset-0 z-200 bg-slate-50 w-screen h-screen">
                    <div class="absolute right-8 top-8 z-50 flex gap-3">
                        <button
                            onclick={() => {
                                isFullscreen = true;
                                setTimeout(() => {
                                    chainingRef?.resetView();
                                    tutorialState.startTour('admin_adaptive_rules_canvas', true, false);
                                }, 300);
                            }}
                            class="flex h-12 w-12 items-center justify-center rounded-2xl border-2 border-b-4 border-slate-200 bg-white text-slate-400 transition-all hover:bg-slate-50 hover:text-amber-500"
                            title="Panduan Kanvas"
                        >
                            <CircleHelp size={20} />
                        </button>
                        <button
                            onclick={() => (isFullscreen = false)}
                            class="flex h-12 w-12 items-center justify-center rounded-2xl border-2 border-b-4 border-slate-200 bg-white text-slate-400 transition-all hover:bg-slate-50 hover:text-primary-500"
                        >
                            <Minimize2 size={20} />
                        </button>
                        <button
                            onclick={() => chainingRef?.resetView()}
                            class="flex h-12 w-12 items-center justify-center rounded-2xl border-2 border-b-4 border-slate-200 bg-white text-slate-400 transition-all hover:bg-slate-50 hover:text-primary-500"
                        >
                            <RefreshCw size={20} />
                        </button>
                    </div>

                    <div class="h-full w-full">
                        <ForwardChaining
                            bind:this={chainingRef}
                            {analyticsState}
                            onedit={openEditRule}
                            oneditaction={openEditAction}
                            {isFullscreen}
                        />
                    </div>
                </div>
            {:else}
                <!-- Visual Builder Section -->
                <Section 
                    title="Visual Strategy Builder" 
                    subtitle="Representasi grafis dari hubungan antara fakta, diagnosis, dan intervensi."
                >
                    <div class="relative overflow-hidden border-2 border-slate-200 bg-white shadow-2xl rounded-[2rem]">
                        <div class="absolute right-8 top-8 z-50 flex gap-3 pointer-events-auto">
                            <button
                                onclick={() => {
                                    isFullscreen = true;
                                    setTimeout(() => {
                                        chainingRef?.resetView();
                                        tutorialState.startTour('admin_adaptive_rules_canvas', true, false);
                                    }, 300);
                                }}
                                class="flex h-12 w-12 items-center justify-center rounded-2xl border-2 border-b-4 border-slate-200 bg-white text-slate-400 transition-all hover:bg-slate-50 hover:text-amber-500"
                                title="Panduan Kanvas"
                            >
                                <CircleHelp size={20} />
                            </button>
                            <button
                                onclick={() => (isFullscreen = true)}
                                class="flex h-12 w-12 items-center justify-center rounded-2xl border-2 border-b-4 border-slate-200 bg-white text-slate-400 transition-all hover:bg-slate-50 hover:text-primary-500"
                            >
                                <Maximize2 size={20} />
                            </button>
                            <button
                                onclick={() => chainingRef?.resetView()}
                                class="flex h-12 w-12 items-center justify-center rounded-2xl border-2 border-b-4 border-slate-200 bg-white text-slate-400 transition-all hover:bg-slate-50 hover:text-primary-500"
                            >
                                <RefreshCw size={20} />
                            </button>
                        </div>

                        <div class="h-[750px] w-full bg-slate-50">
                            <ForwardChaining
                                bind:this={chainingRef}
                                {analyticsState}
                                onedit={openEditRule}
                                oneditaction={openEditAction}
                                {isFullscreen}
                            />
                        </div>
                    </div>
                </Section>
            {/if}
        {/if}

        {#if !isFullscreen}
            <!-- Recent Triggers & Distribution -->
            <div id="adaptive-engine-activities" class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                <Card title="Aktivitas Engine Terbaru">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
                            <Activity size={20} />
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight">Recent Triggers</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Eksekusi aturan terakhir</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        {#each analyticsState.recentTriggers as trigger}
                            <div class="flex items-start gap-4">
                                <UserAvatar name={trigger.user_name} size="sm" />
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-black text-slate-900 uppercase">{trigger.user_name}</span>
                                        <span class="text-[10px] font-bold text-slate-400">{trigger.created_at}</span>
                                    </div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="flex items-center gap-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">
                                            <GitBranch size={10} />
                                            <span>{trigger.rule_name}</span>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        {#each (trigger.action || '').split(', ') as actionId}
                                            <div class="flex items-center gap-1 text-[9px] font-black text-amber-600 border border-amber-200 px-2 py-0.5 rounded-full">
                                                <Target size={10} />
                                                <span>{actionId}</span>
                                            </div>
                                        {/each}
                                    </div>
                                </div>
                            </div>
                        {:else}
                            <div class="py-12 text-center">
                                <p class="text-xs font-bold text-slate-300 uppercase tracking-widest">Belum ada aktivitas terekam</p>
                            </div>
                        {/each}
                    </div>
                </Card>

                <Card title="Distribusi Status Adaptif">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                            <BrainCircuit size={20} />
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight">State Distribution</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kondisi mahasiswa saat ini</p>
                        </div>
                    </div>

                    <div class="space-y-5">
                        {#each analyticsState.adaptiveStateDistribution as item}
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-700 uppercase tracking-tight">{item.difficulty}</span>
                                    <span class="text-xs font-black text-slate-900">{item.count} MHS</span>
                                </div>
                                <div class="h-3 w-full bg-slate-100 rounded-full overflow-hidden">
                                    <div 
                                        class="h-full bg-emerald-500 rounded-full transition-all duration-1000" 
                                        style="width: {(item.count / analyticsState.maxStateCount) * 100}%"
                                    ></div>
                                </div>
                            </div>
                        {:else}
                             <div class="py-12 text-center">
                                <p class="text-xs font-bold text-slate-300 uppercase tracking-widest">Data distribusi belum tersedia</p>
                            </div>
                        {/each}
                    </div>
                </Card>
            </div>
        {/if}
    </div>

    <ActionEditorModal
        show={isActionModalOpen}
        action={editingAction}
        onclose={() => (isActionModalOpen = false)}
    />
</App>

<style>
    :global(body.fullscreen) {
        overflow: hidden;
    }
</style>
