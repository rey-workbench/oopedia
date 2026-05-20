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
        CircleHelp,
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
        analyticsState.rules_by_diagnosis.flatMap((d: any) =>
            d.rules.map((r: any) => ({
                ...r,
                diagnosis_group: d.diagnosis_name,
            }))
        )
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

<style>
    :global(body.fullscreen) {
        overflow: hidden;
    }
</style>

<App title="Strategi Adaptif - Admin">
    <div class={!isFullscreen ? 'space-y-12' : ''}>
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
                        class="shadow-primary-500/20 shadow-xl"
                    >
                        BUAT ATURAN BARU
                    </Button>
                {/snippet}
            </PageHeader>

            <!-- Stats Overview -->
            <div id="adaptive-rules-stats" class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <StatCard
                    title="Total Aturan"
                    value={analyticsState.total_rules}
                    icon={BrainCircuit as any}
                    variant="primary"
                    footer="Logika aktif"
                />
                <StatCard
                    title="Fakta & Gejala"
                    value={analyticsState.total_facts}
                    icon={Cpu as any}
                    variant="success"
                    footer="Variabel kondisi"
                />
                <StatCard
                    title="Intervensi"
                    value={analyticsState.total_actions}
                    icon={Settings2 as any}
                    variant="warning"
                    footer="Aksi pedagogis"
                />
            </div>

            <!-- Tabs Navigation -->
            <div id="adaptive-rules-tabs" class="mt-8 mb-4 flex gap-4 border-b-2 border-slate-100">
                <button
                    class="px-4 pb-3 text-xs font-black tracking-widest uppercase transition-all {activeTab ===
                    'table'
                        ? 'border-primary-500 text-primary-600 border-b-4'
                        : 'text-slate-400 hover:text-slate-600'}"
                    onclick={() => (activeTab = 'table')}
                >
                    Daftar Aturan
                </button>
                <button
                    class="px-4 pb-3 text-xs font-black tracking-widest uppercase transition-all {activeTab ===
                    'canvas'
                        ? 'border-primary-500 text-primary-600 border-b-4'
                        : 'text-slate-400 hover:text-slate-600'}"
                    onclick={() => (activeTab = 'canvas')}
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
                <DataTable items={flattenedRules} {columns} hideSearch={false} itemsPerPage={10}>
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
                                <span
                                    class="text-xs font-black tracking-tight text-slate-900 uppercase"
                                    >{rule.name}</span
                                >
                                <span
                                    class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                    >{rule.id}</span
                                >
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col gap-1.5">
                                <Badge variant="secondary" size="xs" class="w-fit"
                                    >{rule.diagnosis_group}</Badge
                                >
                                <div class="flex flex-wrap gap-1">
                                    {#each rule.required_fact_ids as factId}
                                        <span
                                            class="rounded border border-slate-200 bg-slate-100 px-1.5 py-0.5 text-[9px] font-bold text-slate-500"
                                            >{factId}</span
                                        >
                                    {/each}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-wrap gap-2">
                                {#each rule.actions as action}
                                    {@const actionObj = analyticsState.all_actions.find(
                                        (a) =>
                                            a.id ===
                                            (typeof action === 'string' ? action : action.id)
                                    )}
                                    <div
                                        class="flex items-center gap-1.5 rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1"
                                    >
                                        <div class="h-1 w-1 rounded-full bg-emerald-500"></div>
                                        <span
                                            class="text-[10px] font-black text-emerald-700 uppercase"
                                            >{actionObj?.name ||
                                                (typeof action === 'string'
                                                    ? action
                                                    : action.id)}</span
                                        >
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
                                    class="hover:text-primary-500 text-slate-400"
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
                <div class="fixed inset-0 z-200 h-screen w-screen bg-slate-50">
                    <div class="absolute top-8 right-8 z-50 flex gap-3">
                        <button
                            onclick={() => {
                                isFullscreen = true;
                                setTimeout(() => {
                                    chainingRef?.resetView();
                                    tutorialState.startTour(
                                        'admin_adaptive_rules_canvas',
                                        true,
                                        false
                                    );
                                }, 300);
                            }}
                            class="flex h-12 w-12 items-center justify-center rounded-2xl border-2 border-b-4 border-slate-200 bg-white text-slate-400 transition-all hover:bg-slate-50 hover:text-amber-500"
                            title="Panduan Kanvas"
                        >
                            <CircleHelp size={20} />
                        </button>
                        <button
                            onclick={() => (isFullscreen = false)}
                            class="hover:text-primary-500 flex h-12 w-12 items-center justify-center rounded-2xl border-2 border-b-4 border-slate-200 bg-white text-slate-400 transition-all hover:bg-slate-50"
                        >
                            <Minimize2 size={20} />
                        </button>
                        <button
                            onclick={() => chainingRef?.resetView()}
                            class="hover:text-primary-500 flex h-12 w-12 items-center justify-center rounded-2xl border-2 border-b-4 border-slate-200 bg-white text-slate-400 transition-all hover:bg-slate-50"
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
                    <div
                        class="relative overflow-hidden rounded-[2rem] border-2 border-slate-200 bg-white shadow-2xl"
                    >
                        <div class="pointer-events-auto absolute top-8 right-8 z-50 flex gap-3">
                            <button
                                onclick={() => {
                                    isFullscreen = true;
                                    setTimeout(() => {
                                        chainingRef?.resetView();
                                        tutorialState.startTour(
                                            'admin_adaptive_rules_canvas',
                                            true,
                                            false
                                        );
                                    }, 300);
                                }}
                                class="flex h-12 w-12 items-center justify-center rounded-2xl border-2 border-b-4 border-slate-200 bg-white text-slate-400 transition-all hover:bg-slate-50 hover:text-amber-500"
                                title="Panduan Kanvas"
                            >
                                <CircleHelp size={20} />
                            </button>
                            <button
                                onclick={() => (isFullscreen = true)}
                                class="hover:text-primary-500 flex h-12 w-12 items-center justify-center rounded-2xl border-2 border-b-4 border-slate-200 bg-white text-slate-400 transition-all hover:bg-slate-50"
                            >
                                <Maximize2 size={20} />
                            </button>
                            <button
                                onclick={() => chainingRef?.resetView()}
                                class="hover:text-primary-500 flex h-12 w-12 items-center justify-center rounded-2xl border-2 border-b-4 border-slate-200 bg-white text-slate-400 transition-all hover:bg-slate-50"
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
                    <div class="mb-6 flex items-center gap-3">
                        <div class="bg-primary-50 text-primary-600 rounded-xl p-2">
                            <Activity size={20} />
                        </div>
                        <div>
                            <h3 class="text-sm font-black tracking-tight text-slate-800 uppercase">
                                Recent Triggers
                            </h3>
                            <p
                                class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                            >
                                Eksekusi aturan terakhir
                            </p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        {#each analyticsState.recent_triggers as trigger}
                            <div class="flex items-start gap-4">
                                <UserAvatar name={trigger.user_name} size="sm" />
                                <div class="min-w-0 flex-1">
                                    <div class="mb-1 flex items-center justify-between">
                                        <span class="text-xs font-black text-slate-900 uppercase"
                                            >{trigger.user_name}</span
                                        >
                                        <span class="text-[10px] font-bold text-slate-400"
                                            >{trigger.created_at}</span
                                        >
                                    </div>
                                    <div class="mb-2 flex items-center gap-2">
                                        <div
                                            class="flex items-center gap-1 rounded bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-600"
                                        >
                                            <GitBranch size={10} />
                                            <span>{trigger.rule_name}</span>
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center gap-1 rounded-full border border-amber-200 px-2 py-0.5 text-[9px] font-black text-amber-600"
                                    >
                                        <Target size={10} />
                                        <span>{trigger.action_name}</span>
                                    </div>
                                </div>
                            </div>
                        {:else}
                            <div class="py-12 text-center">
                                <p
                                    class="text-xs font-bold text-slate-300 uppercase tracking-widest"
                                >
                                    Belum ada aktivitas terekam
                                </p>
                            </div>
                        {/each}
                    </div>
                </Card>

                <Card title="Distribusi Status Adaptif">
                    <div class="mb-6 flex items-center gap-3">
                        <div class="rounded-xl bg-emerald-50 p-2 text-emerald-600">
                            <BrainCircuit size={20} />
                        </div>
                        <div>
                            <h3 class="text-sm font-black tracking-tight text-slate-800 uppercase">
                                State Distribution
                            </h3>
                            <p
                                class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                            >
                                Kondisi mahasiswa saat ini
                            </p>
                        </div>
                    </div>

                    <div class="space-y-5">
                        {#each analyticsState.adaptive_state_distribution as item}
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-xs font-bold tracking-tight text-slate-700 uppercase"
                                        >{item.difficulty}</span
                                    >
                                    <span class="text-xs font-black text-slate-900"
                                        >{item.count} MHS</span
                                    >
                                </div>
                                <div class="h-3 w-full overflow-hidden rounded-full bg-slate-100">
                                    <div
                                        class="h-full rounded-full bg-emerald-500 transition-all duration-1000"
                                        style="width: {(item.count / analyticsState.maxStateCount) *
                                            100}%"
                                    ></div>
                                </div>
                            </div>
                        {:else}
                            <div class="py-12 text-center">
                                <p
                                    class="text-xs font-bold text-slate-300 uppercase tracking-widest"
                                >
                                    Data distribusi belum tersedia
                                </p>
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
