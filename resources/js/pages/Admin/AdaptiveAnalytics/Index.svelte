<script lang="ts">
    import type { AdminAdaptiveAnalyticsProps } from '@/types/states/admin';
    import type { AdaptiveFact, AdaptiveAction } from '@/types/models';
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Badge from '@/components/ui/Badge.svelte';
    import DecisionTree from './DecisionTree.svelte';
    import { untrack } from 'svelte';
    import { AdaptiveAnalyticsState } from '@/states/Admin/AdaptiveAnalyticsState.svelte';
    import {
        Brain,
        Play,
        GitBranch,
        TrendingUp,
        ArrowRight,
        Zap,
        AlertTriangle,
        CheckCircle2,
        Target,
        Crown,
        LayoutGrid,
        Network
    } from 'lucide-svelte';

    let props: AdminAdaptiveAnalyticsProps = $props();
    let currentView = $state<'tree' | 'linear'>('tree');

    const analyticsState = untrack(() => new AdaptiveAnalyticsState(props));

    const factsByCategory = $derived(props.allFacts.reduce((acc, fact: AdaptiveFact) => {
        const cat = fact.category.charAt(0).toUpperCase() + fact.category.slice(1);
        if (!acc[cat]) acc[cat] = [];
        acc[cat].push(fact);
        return acc;
    }, {} as Record<string, AdaptiveFact[]>));

    const actionsByVariant = $derived(props.allActions.reduce((acc, action: AdaptiveAction) => {
        const variant = action.variant ? (action.variant.charAt(0).toUpperCase() + action.variant.slice(1).replace('_', ' ')) : 'Other';
        if (!acc[variant]) acc[variant] = [];
        acc[variant].push(action);
        return acc;
    }, {} as Record<string, AdaptiveAction[]>));

    const categoryIcons: Record<string, any> = {
        'Performance': TrendingUp,
        'Time': Play,
        'Style': Brain,
        'Error': AlertTriangle,
        'Difficulty': Target,
        'Behaviour': Zap,
        'Progress': GitBranch
    };

    const variantIcons: Record<string, any> = {
        'Result': CheckCircle2,
        'Backtrack': AlertTriangle,
        'Acceleration': Zap,
        'Certificate': Crown,
        'Intervention': Brain
    };

</script>

<App title="Analytics Engine Adaptif - Admin">
    <div class="space-y-8">
        <PageHeader
            id="page-header"
            title="Analytics Engine Adaptif"
            subtitle="Visualisasi pohon keputusan dan data analitik sistem aturan adaptif."
        >
            <div class="flex items-center bg-slate-100 p-1 rounded-xl shadow-inner">
                <button 
                    onclick={() => currentView = 'tree'}
                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all {currentView === 'tree' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'}"
                >
                    <Network size={16} />
                    Decision Tree
                </button>
                <button 
                    onclick={() => currentView = 'linear'}
                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all {currentView === 'linear' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'}"
                >
                    <LayoutGrid size={16} />
                    Logic Flow
                </button>
            </div>
        </PageHeader>

        {#if currentView === 'tree'}
            <DecisionTree analyticsState={analyticsState} />
        {:else}
            <div class="overflow-x-auto pb-4">
                <div class="min-w-[1000px] space-y-10">
                    <div class="flex justify-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-900 text-white shadow-lg">
                                <Play size={24} fill="currentColor" />
                            </div>
                            <span class="text-xs font-bold uppercase tracking-widest text-slate-500">Quiz Attempt</span>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <div class="flex items-center gap-2 text-slate-400">
                            <ArrowRight size={20} />
                            <span class="text-xs">First Match Wins</span>
                            <ArrowRight size={20} />
                        </div>
                    </div>

                    <div>
                        <div class="mb-6 text-center">
                            <span class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-sm font-bold uppercase tracking-widest text-blue-700 ring-2 ring-blue-200">
                                <GitBranch size={16} />
                                Fact Gathering (G-Codes)
                            </span>
                        </div>
                        <div class="grid grid-cols-5 gap-4">
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 w-full">
                            {#each Object.entries(factsByCategory) as [category, facts]}
                                <div class="flex flex-col items-center gap-3 rounded-xl border-2 border-slate-200 bg-white p-4 shadow-sm">
                                    <div class="flex items-center gap-2 text-slate-700">
                                        {#if categoryIcons[category]}
                                            {@const Icon = categoryIcons[category]}
                                            <Icon size={14} />
                                        {/if}
                                        <span class="text-xs font-black uppercase tracking-tight">{category}</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 w-full">
                                        {#each facts as fact}
                                            <div class="flex flex-col items-center rounded bg-slate-50 border border-slate-100 p-2">
                                                <span class="text-[10px] font-bold text-slate-800">{fact.code}</span>
                                                <span class="text-[8px] text-slate-400 text-center leading-tight line-clamp-1">{fact.name}</span>
                                            </div>
                                        {/each}
                                    </div>
                                </div>
                            {/each}
                        </div>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <ArrowRight size={24} class="text-slate-300" />
                    </div>

                    <div>
                        <div class="mb-6 text-center">
                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-sm font-bold uppercase tracking-widest text-slate-700 ring-2 ring-slate-200">
                                <Brain size={16} />
                                Rule Evaluation (Priority Order)
                            </span>
                        </div>

                        {#each analyticsState.rulesByDomain as domain}
                            <div class="space-y-4 mb-8">
                                <h3 class="text-sm font-black text-slate-900 tracking-tight uppercase">
                                    {domain.domain} 
                                    <span class="ml-2 text-[10px] font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded-full border border-slate-100">{domain.count} Rules</span>
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    {#each domain.rules as rule}
                                        <div class="bg-white border-2 border-slate-100 flex flex-col items-center gap-2 rounded-lg p-3 transition-all hover:shadow-md hover:scale-105">
                                            <div class="flex items-center gap-2">
                                                <span class="rounded bg-slate-800 px-2 py-0.5 text-[10px] font-bold text-white">{rule.id}</span>
                                                <span class="text-xs font-bold text-slate-800">{rule.name}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <Badge variant="primary" size="sm">
                                                    {rule.action}
                                                </Badge>
                                            </div>
                                        </div>
                                    {/each}
                                </div>
                            </div>
                        {/each}
                    </div>

                    <div class="flex justify-center">
                        <ArrowRight size={24} class="text-slate-300" />
                    </div>

                    <div>
                        <div class="mb-6 text-center">
                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-4 py-2 text-sm font-bold uppercase tracking-widest text-white ring-2 ring-slate-700">
                                <Zap size={16} />
                                Action Output (H-Codes)
                            </span>
                        </div>
                        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                            {#each Object.entries(actionsByVariant) as [variant, actions]}
                                <div class="flex flex-col items-center gap-3 rounded-xl border-2 border-slate-200 bg-white p-4 shadow-sm">
                                    <div class="flex items-center gap-2 text-slate-700">
                                        {#if variantIcons[variant]}
                                            {@const Icon = variantIcons[variant]}
                                            <Icon size={14} />
                                        {/if}
                                        <span class="text-xs font-black uppercase tracking-tight">{variant}</span>
                                    </div>
                                    <div class="flex flex-wrap justify-center gap-2">
                                        {#each actions as action}
                                            <span class="rounded bg-slate-100 border border-slate-200 px-2 py-1 text-[10px] font-bold text-slate-600 truncate max-w-full" title={action.name}>
                                                {action.code}
                                            </span>
                                        {/each}
                                    </div>
                                </div>
                            {/each}
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <ArrowRight size={24} class="text-slate-300" />
                    </div>

                    <div class="flex justify-center">
                        <div class="flex h-20 w-48 items-center justify-center rounded-full bg-emerald-500 text-white shadow-lg ring-4 ring-emerald-200">
                            <CheckCircle2 size={32} />
                        </div>
                    </div>

                    <Card hover={false}>
                        <div class="flex flex-wrap items-center justify-between gap-6">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <div class="h-4 w-4 rounded-full bg-rose-500"></div>
                                    <span class="text-sm font-medium text-slate-600">Safety (Highest Priority)</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="h-4 w-4 rounded-full bg-blue-500"></div>
                                    <span class="text-sm font-medium text-slate-600">Project</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="h-4 w-4 rounded-full bg-amber-500"></div>
                                    <span class="text-sm font-medium text-slate-600">Achievement</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="h-4 w-4 rounded-full bg-emerald-500"></div>
                                    <span class="text-sm font-medium text-slate-600">Recovery</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="h-4 w-4 rounded-full bg-purple-500"></div>
                                    <span class="text-sm font-medium text-slate-600">Progression</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 text-sm text-slate-500">
                                <span class="flex items-center gap-2">
                                    <CheckCircle2 size={16} class="text-emerald-500" />
                                    First match wins
                                </span>
                                <span class="flex items-center gap-2">
                                    <AlertTriangle size={16} class="text-amber-500" />
                                    Lower priority number = evaluated first
                                </span>
                            </div>
                        </div>
                    </Card>
                </div>
            </div>
        {/if}
    </div>
</App>