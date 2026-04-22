<script lang="ts">
    import type { AdminAdaptiveAnalyticsProps } from '@/types/states/admin';
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
        RefreshCcw,
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

    const domainColors: Record<string, any> = {
        'Safety': { bg: 'bg-rose-50', border: 'border-rose-200', text: 'text-rose-700', dot: 'bg-rose-500' },
        'Project': { bg: 'bg-blue-50', border: 'border-blue-200', text: 'text-blue-700', dot: 'bg-blue-500' },
        'Achievement': { bg: 'bg-amber-50', border: 'border-amber-200', text: 'text-amber-700', dot: 'bg-amber-500' },
        'Recovery': { bg: 'bg-emerald-50', border: 'border-emerald-200', text: 'text-emerald-700', dot: 'bg-emerald-500' },
        'Progression': { bg: 'bg-purple-50', border: 'border-purple-200', text: 'text-purple-700', dot: 'bg-purple-500' },
    };

    const actionColors: Record<string, string> = {
        'INTERVENTION': 'destructive',
        'RECOVERY': 'warning',
        'PROMOTION': 'success',
        'SAFETY': 'primary',
        'CERTIFICATE': 'warning',
    };

    const getActionType = (action: string): string => {
        if (action.includes('CRISIS') || action.includes('INTERVENTION')) return 'INTERVENTION';
        if (action.includes('RECOVERY') || action.includes('STUDY_SYNTAX')) return 'RECOVERY';
        if (action.includes('INCREASE') || action.includes('PROMOTION') || action.includes('ACCELERATED')) return 'PROMOTION';
        if (action.includes('CERTIFICATE')) return 'CERTIFICATE';
        return 'SAFETY';
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
                            <div class="flex flex-col items-center gap-3 rounded-xl border-2 border-blue-200 bg-blue-50 p-4">
                                <span class="text-sm font-bold text-blue-700">Performance</span>
                                <div class="grid grid-cols-2 gap-2">
                                    {#each [['G01', 'Critical'], ['G02', 'Remedial'], ['G03', 'Standard'], ['G04', 'Mastery']] as [code, label]}
                                        <div class="flex flex-col items-center rounded bg-blue-100 p-2">
                                            <span class="text-xs font-bold text-blue-800">{code}</span>
                                            <span class="text-[9px] text-blue-500">{label}</span>
                                        </div>
                                    {/each}
                                </div>
                            </div>
                            <div class="flex flex-col items-center gap-3 rounded-xl border-2 border-emerald-200 bg-emerald-50 p-4">
                                <span class="text-sm font-bold text-emerald-700">Time</span>
                                <div class="flex flex-col items-center rounded bg-emerald-100 p-4">
                                    <span class="text-lg font-bold text-emerald-800">G05</span>
                                    <span class="text-[9px] text-emerald-500">&lt; 70% allocated</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-center gap-3 rounded-xl border-2 border-purple-200 bg-purple-50 p-4">
                                <span class="text-sm font-bold text-purple-700">Learning Style</span>
                                <div class="grid grid-cols-3 gap-2">
                                    {#each [['G06', 'Visual'], ['G07', 'Textual'], ['G22', 'Mixed']] as [code, label]}
                                        <div class="flex flex-col items-center rounded bg-purple-100 p-2">
                                            <span class="text-[10px] font-bold text-purple-800">{code}</span>
                                            <span class="text-[9px] text-purple-500">{label}</span>
                                        </div>
                                    {/each}
                                </div>
                            </div>
                            <div class="flex flex-col items-center gap-3 rounded-xl border-2 border-rose-200 bg-rose-50 p-4">
                                <span class="text-sm font-bold text-rose-700">Error Type</span>
                                <div class="grid grid-cols-2 gap-2">
                                    {#each [['G08', 'Syntax'], ['G09', 'Logic']] as [code, label]}
                                        <div class="flex flex-col items-center rounded bg-rose-100 p-2">
                                            <span class="text-[10px] font-bold text-rose-800">{code}</span>
                                            <span class="text-[9px] text-rose-500">{label}</span>
                                        </div>
                                    {/each}
                                </div>
                            </div>
                            <div class="flex flex-col items-center gap-3 rounded-xl border-2 border-amber-200 bg-amber-50 p-4">
                                <span class="text-sm font-bold text-amber-700">Difficulty</span>
                                <div class="grid grid-cols-3 gap-2">
                                    {#each [['G13', 'Beginner'], ['G14', 'Medium'], ['G15', 'Hard']] as [code, label]}
                                        <div class="flex flex-col items-center rounded bg-amber-100 p-2">
                                            <span class="text-[10px] font-bold text-amber-800">{code}</span>
                                            <span class="text-[9px] text-amber-500">{label}</span>
                                        </div>
                                    {/each}
                                </div>
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
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-1.5 h-6 rounded-full {domainColors[domain.domain]?.dot || 'bg-slate-400'}"></div>
                                <h3 class="text-sm font-black text-slate-900 tracking-tight uppercase">
                                    {domain.domain} 
                                    <span class="ml-2 text-[10px] font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded-full border border-slate-100">{domain.count} Rules</span>
                                </h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                {#each domain.rules as rule}
                                        <div class="{domainColors[domain.domain]?.bg} {domainColors[domain.domain]?.border} flex flex-col items-center gap-2 rounded-lg border-2 p-3 transition-all hover:shadow-md hover:scale-105">
                                            <div class="flex items-center gap-2">
                                                <span class="rounded bg-slate-800 px-2 py-0.5 text-[10px] font-bold text-white">{rule.id}</span>
                                                <span class="text-xs font-bold text-slate-800">{rule.name}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <Badge variant={actionColors[getActionType(rule.action)] as any} size="sm">
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
                        <div class="grid grid-cols-5 gap-4">
                            <div class="flex flex-col items-center gap-3 rounded-xl border-2 border-rose-200 bg-rose-50 p-4">
                                <AlertTriangle size={24} class="text-rose-500" />
                                <span class="text-sm font-bold text-rose-700">Crisis Intervention</span>
                                <div class="grid grid-cols-3 gap-2">
                                    {#each ['H01', 'H02', 'H17'] as code}
                                        <span class="rounded bg-rose-200 px-2 py-1 text-[10px] font-bold text-rose-800">{code}</span>
                                    {/each}
                                </div>
                            </div>
                            <div class="flex flex-col items-center gap-3 rounded-xl border-2 border-amber-200 bg-amber-50 p-4">
                                <RefreshCcw size={24} class="text-amber-500" />
                                <span class="text-sm font-bold text-amber-700">Recovery</span>
                                <div class="grid grid-cols-3 gap-2">
                                    {#each ['H03', 'H04', 'H23'] as code}
                                        <span class="rounded bg-amber-200 px-2 py-1 text-[10px] font-bold text-amber-800">{code}</span>
                                    {/each}
                                </div>
                            </div>
                            <div class="flex flex-col items-center gap-3 rounded-xl border-2 border-purple-200 bg-purple-50 p-4">
                                <TrendingUp size={24} class="text-purple-500" />
                                <span class="text-sm font-bold text-purple-700">Promotion</span>
                                <div class="grid grid-cols-3 gap-2">
                                    {#each ['H05', 'H06', 'H08'] as code}
                                        <span class="rounded bg-purple-200 px-2 py-1 text-[10px] font-bold text-purple-800">{code}</span>
                                    {/each}
                                </div>
                            </div>
                            <div class="flex flex-col items-center gap-3 rounded-xl border-2 border-blue-200 bg-blue-50 p-4">
                                <Target size={24} class="text-blue-500" />
                                <span class="text-sm font-bold text-blue-700">Safety Net</span>
                                <div class="grid grid-cols-3 gap-2">
                                    {#each ['H07', 'H14', 'H15'] as code}
                                        <span class="rounded bg-blue-200 px-2 py-1 text-[10px] font-bold text-blue-800">{code}</span>
                                    {/each}
                                </div>
                            </div>
                            <div class="flex flex-col items-center gap-3 rounded-xl border-2 border-amber-200 bg-amber-100 p-4">
                                <Crown size={24} class="text-amber-600" />
                                <span class="text-sm font-bold text-amber-700">Certificates</span>
                                <div class="grid grid-cols-3 gap-2">
                                    {#each ['H09', 'H10', 'H11'] as code}
                                        <span class="rounded bg-amber-200 px-2 py-1 text-[10px] font-bold text-amber-800">{code}</span>
                                    {/each}
                                </div>
                            </div>
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