<script lang="ts">
    import type { AdminAdaptiveRuleProps } from '@/types/states/admin';
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import ForwardChaining from './ForwardChaining.svelte';
    import RuleEditorModal from './RuleEditorModal.svelte';
    import ActionEditorModal from './ActionEditorModal.svelte';
    import { AdaptiveRuleState } from '@/states/Admin/AdaptiveRuleState.svelte';
    import { untrack } from 'svelte';
    import { RefreshCw, Maximize2, Minimize2, PlusCircle } from 'lucide-svelte';
    import Button from '@/components/ui/Button.svelte';

    let props: AdminAdaptiveRuleProps = $props();

    let isRuleModalOpen = $state(false);
    let isActionModalOpen = $state(false);
    let editingRule = $state<any>(null);
    let editingAction = $state<any>(null);

    function openEditRule(rule: any) {
        editingRule = rule;
        isRuleModalOpen = true;
    }

    function openEditAction(action: any) {
        editingAction = action;
        isActionModalOpen = true;
    }

    const analyticsState = untrack(() => new AdaptiveRuleState(props));

    $effect(() => {
        analyticsState.sync(props as any);
    });

    let mainContainer = $state<HTMLElement | null>(null);
    let isFullscreen = $state(false);
    let chainingRef = $state<any>(null);
</script>

<App title="Analytics Engine Adaptif - Admin">
    <div class="{!isFullscreen ? 'space-y-8' : ''}" bind:this={mainContainer}>
        {#if !isFullscreen}
            <PageHeader
                title="Analytics Engine Adaptif"
                subtitle="Orkestrasi alur logika dan visualisasi keputusan sistem adaptif."
            >
                {#snippet actions()}
                    <Button
                        variant="primary"
                        icon={PlusCircle}
                        onclick={() => openEditRule(null)}
                        class="shadow-primary-900/10 shadow-xl"
                    >
                        TAMBAH ATURAN
                    </Button>
                    <div
                        class="flex items-center gap-1 rounded-xl border border-slate-200 bg-white p-1 shadow-sm"
                    >
                        <button
                            onclick={() => chainingRef?.resetView()}
                            class="rounded-lg p-2 text-slate-500 transition-all hover:bg-slate-100 hover:text-slate-900 active:scale-95"
                            title="Atur Ulang Tampilan"
                        >
                            <RefreshCw size={18} />
                        </button>
                        <div class="mx-1 h-6 w-px bg-slate-200"></div>
                        <button
                            onclick={() => chainingRef?.toggleFullscreen()}
                            class="rounded-lg p-2 text-slate-500 transition-all hover:bg-slate-100 hover:text-slate-900 active:scale-95"
                            title={isFullscreen ? 'Keluar Layar Penuh' : 'Layar Penuh'}
                        >
                            {#if isFullscreen}
                                <Minimize2 size={18} />
                            {:else}
                                <Maximize2 size={18} />
                            {/if}
                        </button>
                    </div>
                {/snippet}
            </PageHeader>
        {/if}

        <div class="animate-in fade-in slide-in-from-bottom-4 duration-500">
            <ForwardChaining
                bind:this={chainingRef}
                {analyticsState}
                onedit={openEditRule}
                oneditaction={openEditAction}
                bind:isFullscreen
                fullscreenTarget={mainContainer}
            />
        </div>
        <!-- Modals inside mainContainer for Fullscreen Visibility -->
        <RuleEditorModal
            show={isRuleModalOpen}
            rule={editingRule}
            allFacts={props.allFacts}
            allActions={props.allActions}
            totalRules={props.totalRules}
            onclose={() => (isRuleModalOpen = false)}
        />

        <ActionEditorModal
            show={isActionModalOpen}
            action={editingAction}
            onclose={() => (isActionModalOpen = false)}
        />
    </div>
</App>
