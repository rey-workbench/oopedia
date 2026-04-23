<script lang="ts">
    import Button from '@/components/ui/Button.svelte';
    import type { AdminAdaptiveAnalyticsProps } from '@/types/states/admin';
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import ForwardChaining from './ForwardChaining.svelte';
    import RuleEditorModal from './RuleEditorModal.svelte';
    import { untrack } from 'svelte';
    import { AdaptiveAnalyticsState } from '@/states/Admin/AdaptiveAnalyticsState.svelte';
    import { Plus } from 'lucide-svelte';

    let props: AdminAdaptiveAnalyticsProps = $props();

    let isModalOpen = $state(false);
    let editingRule = $state<any>(null);

    function openCreate() {
        editingRule = null;
        isModalOpen = true;
    }

    function openEdit(rule: any) {
        editingRule = rule;
        isModalOpen = true;
    }

    const analyticsState = untrack(() => new AdaptiveAnalyticsState(props));
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
                    <Button
                        variant="primary"
                        size="md"
                        icon={Plus}
                        onclick={openCreate}
                        class="shadow-primary-900/10 shadow-xl"
                    >
                        TAMBAH ATURAN
                    </Button>
                </div>
            {/snippet}
        </PageHeader>

        <ForwardChaining {analyticsState} onedit={openEdit} />
    </div>

    <!-- Rule Editor Modal -->
    <RuleEditorModal
        show={isModalOpen}
        rule={editingRule}
        allFacts={props.allFacts}
        allActions={props.allActions}
        onclose={() => (isModalOpen = false)}
    />
</App>
