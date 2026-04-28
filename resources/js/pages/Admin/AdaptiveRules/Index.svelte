<script lang="ts">
    import type { AdminAdaptiveRuleProps } from '@/types/states/admin';
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import ForwardChaining from './ForwardChaining.svelte';
    import RuleEditorModal from './RuleEditorModal.svelte';
    import ActionEditorModal from './ActionEditorModal.svelte';
    import { AdaptiveRuleState } from '@/states/Admin/AdaptiveRuleState.svelte';
    import { untrack } from 'svelte';

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
</script>

<App title="Analytics Engine Adaptif - Admin">
    <div class="bg-white {!isFullscreen ? 'space-y-8' : ''}" bind:this={mainContainer}>
        {#if !isFullscreen}
            <PageHeader
                title="Analytics Engine Adaptif"
                subtitle="Orkestrasi alur logika dan visualisasi keputusan sistem adaptif."
            />
        {/if}

        <div class="animate-in fade-in slide-in-from-bottom-4 duration-500">
            <ForwardChaining 
                {analyticsState} 
                onedit={openEditRule} 
                oneditaction={openEditAction}
                bind:isFullscreen={isFullscreen}
                fullscreenTarget={mainContainer} 
            />
        </div>
        <!-- Modals inside mainContainer for Fullscreen Visibility -->
        <RuleEditorModal
            show={isRuleModalOpen}
            rule={editingRule}
            allFacts={props.allFacts}
            allActions={props.allActions}
            onclose={() => (isRuleModalOpen = false)}
        />

        <ActionEditorModal
            show={isActionModalOpen}
            action={editingAction}
            onclose={() => (isActionModalOpen = false)}
        />
    </div>
</App>
