<script lang="ts">
    import App from '@/layouts/App.svelte';
    import { useForm, router } from '@inertiajs/svelte';
    import Button from '@/components/ui/Button.svelte';
    import { Save, ArrowLeft } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import type { AdaptiveFact, AdaptiveAction } from '@/types/models';
    import Toggle from '@/components/ui/Toggle.svelte';
    import LibraryDrawer from '@/components/rulebase/LibraryDrawer.svelte';
    import { AdaptiveRuleEditorState } from '@/states/Admin/AdaptiveRuleEditorState.svelte';
    import { untrack } from 'svelte';
    import RuleMetadataCard from '@/components/rulebase/RuleMetadataCard.svelte';
    import RuleConditionSection from '@/components/rulebase/RuleConditionSection.svelte';
    import RuleDeductionSection from '@/components/rulebase/RuleDeductionSection.svelte';
    import RuleActionSection from '@/components/rulebase/RuleActionSection.svelte';
    import RuleFeedbackSection from '@/components/rulebase/RuleFeedbackSection.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    let {
        rule,
        allFacts = [],
        allActions = []
    } = $props<{
        rule: any;
        allFacts: AdaptiveFact[];
        allActions: AdaptiveAction[];
    }>();

    const state = untrack(() => new AdaptiveRuleEditorState({ allFacts, allActions, isEdit: true }));

    let form = useForm(untrack(() => ({
        id: rule.id,
        name: rule.name,
        recommendation: rule.recommendation,
        priority: rule.priority,
        actions: rule.actions || [],
        required_fact_ids: rule.required_fact_ids || [],
        deduced_fact_ids: rule.deduced_fact_ids || [],
        facts: state.parseInitialFacts(rule.required_fact_ids || []),
        deduced_facts: state.parseInitialDeductions(rule.deduced_fact_ids || []),
        is_active: rule.is_active,
    })));

    function handleSubmit(e: Event) {
        e.preventDefault();
        form.required_fact_ids = form.facts.map(f => f.id);
        form.deduced_fact_ids = form.deduced_facts.map(f => f.id);

        form.put(ROUTES.ADMIN.ADAPTIVE_RULES.UPDATE(rule.id), {
            onSuccess: () => router.visit(ROUTES.ADMIN.ADAPTIVE_RULES.INDEX),
        });
    }
</script>

<App title="Edit Logic Rule - Analytics Engine">
    <div class="space-y-8">
        <PageHeader
            title="Edit Aturan"
            subtitle="Modifikasi logika inferensi yang sudah ada dalam sistem."
        >
            {#snippet actions()}
                <Button href={ROUTES.ADMIN.ADAPTIVE_RULES.INDEX} variant="ghost" icon={ArrowLeft}>
                    KEMBALI KE DAFTAR
                </Button>
            {/snippet}
        </PageHeader>

        <div id="adaptive-rule-editor-container" class="flex h-[800px] w-full overflow-hidden border-2 border-slate-200 rounded-[2rem] bg-white shadow-2xl">
            <div id="adaptive-rule-library">
                <LibraryDrawer 
                    {allFacts} 
                    {allActions} 
                    CONDITION_KEYS={state.CONDITION_KEYS} 
                    handleDragStart={(e, id, type) => state.handleDragStart(e, id, type)} 
                />
            </div>

        <div class="flex-1 flex flex-col overflow-hidden bg-slate-50">
            <!-- MAIN FORM CANVAS -->
            <div class="flex-1 overflow-y-auto p-8 custom-scrollbar bg-slate-50/50">
                <form id="rule-form" onsubmit={handleSubmit} class="mx-auto max-w-5xl space-y-6 pb-20">
                    
                    <RuleMetadataCard {form} />

                    <RuleConditionSection {form} {state} />

                    <RuleDeductionSection {form} {state} />

                    <RuleActionSection {form} {state} {allActions} />

                    <RuleFeedbackSection {form} />
                </form>
            </div>

            <!-- FOOTER ACTIONS -->
            <div class="flex items-center justify-between border-t-2 border-slate-200 bg-white px-8 py-5 z-10 shadow-2xl">
                <div class="flex items-center gap-6">
                    <Toggle bind:checked={form.is_active} label="Active Status" />
                    <div class="h-8 w-px bg-slate-100"></div>
                    <div class="flex flex-col">
                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em] mb-0.5">Rule ID</span>
                        <span class="text-xs font-black text-amber-500 font-mono tracking-tight">{form.id}</span>
                    </div>
                </div>
                <div class="flex gap-3">
                    <Button variant="secondary" size="sm" href={ROUTES.ADMIN.ADAPTIVE_RULES.INDEX}>
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
</App>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>
