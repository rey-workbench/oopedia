<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import Button from '@/components/ui/Button.svelte';
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
        Plus,
        Pencil,
        Trash2,
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
        Network,
    } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';

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

    function handleDelete(ruleId: number) {
        if (confirm('Yakin ingin menghapus aturan ini? Seluruh riwayat eksekusi terkait aturan ini akan hilang.')) {
            router.delete(`${ROUTES.ADMIN.ADAPTIVE_ANALYTICS}/${ruleId}`);
        }
    }

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
                    <div class="flex items-center bg-slate-100 p-1 rounded-2xl shadow-inner mr-4">
                        <button 
                            onclick={() => currentView = 'tree'}
                            class="flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {currentView === 'tree' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-400 hover:text-slate-600'}"
                        >
                            <Network size={14} />
                            Arsitektur
                        </button>
                        <button 
                            onclick={() => currentView = 'linear'}
                            class="flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {currentView === 'linear' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-400 hover:text-slate-600'}"
                        >
                            <LayoutGrid size={14} />
                            Alur Logika
                        </button>
                    </div>

                    <Button 
                        variant="primary" 
                        size="md" 
                        icon={Plus} 
                        href={`${ROUTES.ADMIN.ADAPTIVE_ANALYTICS}/create`}
                        class="shadow-xl shadow-primary-900/10"
                    >
                        TAMBAH ATURAN
                    </Button>
                </div>
            {/snippet}
        </PageHeader>

        {#if currentView === 'tree'}
            <DecisionTree analyticsState={analyticsState} />
        {:else}
            <div class="overflow-x-auto pb-4">
                <div class="min-w-[1000px] space-y-10">
                    <!-- Workflow Visualization -->
                    <div class="flex justify-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-900 text-white shadow-lg">
                                <Play size={24} fill="currentColor" />
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Mulai Sesi Kuis</span>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <ArrowRight size={20} class="text-slate-200" />
                    </div>

                    <!-- Facts Section -->
                    <div class="space-y-6">
                        <div class="flex items-center justify-center gap-4">
                            <div class="h-px w-20 bg-linear-to-r from-transparent to-blue-200"></div>
                            <span class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-blue-700 ring-2 ring-blue-100">
                                <GitBranch size={14} />
                                1. Pengamatan Fakta Pedagogis
                            </span>
                            <div class="h-px w-20 bg-linear-to-l from-transparent to-blue-200"></div>
                        </div>

                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 w-full">
                            {#each Object.entries(factsByCategory) as [category, facts]}
                                <div class="flex flex-col items-center gap-4 rounded-3xl border border-slate-100 bg-white p-6 shadow-sm transition-all hover:shadow-md">
                                    <div class="flex items-center gap-2 text-slate-400">
                                        {#if categoryIcons[category]}
                                            {@const Icon = categoryIcons[category]}
                                            <Icon size={12} />
                                        {/if}
                                        <span class="text-[10px] font-black uppercase tracking-widest">{category}</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 w-full">
                                        {#each facts as fact}
                                            <div class="flex flex-col items-center rounded-xl bg-slate-50 border border-slate-100/50 p-2.5" title={fact.description}>
                                                <span class="text-[10px] font-black text-slate-800 tracking-tighter">{fact.code}</span>
                                                <span class="text-[8px] font-bold text-slate-400 text-center leading-tight line-clamp-1 uppercase">{fact.name}</span>
                                            </div>
                                        {/each}
                                    </div>
                                </div>
                            {/each}
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <ArrowRight size={20} class="text-slate-200" />
                    </div>

                    <!-- Rules Section -->
                    <div class="space-y-8">
                        <div class="flex items-center justify-center gap-4">
                            <div class="h-px w-20 bg-linear-to-r from-transparent to-slate-200"></div>
                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl shadow-slate-900/20 ring-4 ring-slate-100">
                                <Brain size={14} />
                                2. Evaluasi Aturan (Forward Chaining)
                            </span>
                            <div class="h-px w-20 bg-linear-to-l from-transparent to-slate-200"></div>
                        </div>

                        {#each analyticsState.rulesByDomain as domain}
                            <div class="space-y-4">
                                <div class="flex items-center justify-between border-b-2 border-slate-50 pb-2">
                                    <h3 class="text-[11px] font-black text-slate-900 tracking-widest uppercase flex items-center gap-3">
                                        <div class="h-2 w-2 rounded-full bg-primary-500 shadow-sm shadow-primary-500"></div>
                                        {domain.domain} 
                                    </h3>
                                    <span class="text-[9px] font-black text-slate-400 bg-slate-50 px-3 py-1 rounded-full border border-slate-100 tracking-widest">{domain.count} ATURAN TERDAFTAR</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                                    {#each domain.rules as rule}
                                        <div class="group relative bg-white border border-slate-100 flex flex-col gap-4 rounded-[2rem] p-5 transition-all duration-300 hover:border-primary-200 hover:shadow-2xl hover:shadow-primary-500/10 hover:-translate-y-1.5">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="flex flex-col gap-1.5">
                                                    <span class="w-fit rounded-lg bg-slate-100 px-2 py-0.5 text-[9px] font-black text-slate-500 uppercase tracking-widest">{rule.id}</span>
                                                    <span class="text-xs font-bold text-slate-800 leading-snug">{rule.name}</span>
                                                </div>
                                                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-all transform translate-y-1 group-hover:translate-y-0">
                                                    <a 
                                                        href={`${ROUTES.ADMIN.ADAPTIVE_ANALYTICS}/${rule.real_id}/edit`}
                                                        class="p-2 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors"
                                                    >
                                                        <Pencil size={12} />
                                                    </a>
                                                    <button 
                                                        onclick={() => handleDelete(rule.real_id)}
                                                        class="p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-colors"
                                                    >
                                                        <Trash2 size={12} />
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center justify-between mt-auto pt-3 border-t border-slate-50">
                                                <div class="flex items-center gap-2">
                                                    <Zap size={10} class="text-primary-500" />
                                                    <span class="text-[10px] font-black text-primary-600 uppercase tracking-tighter">{rule.action}</span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest">Priority</span>
                                                    <Badge variant="primary" size="sm" class="font-black">
                                                        {rule.priority}
                                                    </Badge>
                                                </div>
                                            </div>
                                        </div>
                                    {/each}
                                </div>
                            </div>
                        {/each}
                    </div>

                    <div class="flex justify-center pt-6">
                        <ArrowRight size={20} class="text-slate-200" />
                    </div>

                    <!-- Actions Section -->
                    <div class="space-y-6">
                        <div class="flex items-center justify-center gap-4">
                            <div class="h-px w-20 bg-linear-to-r from-transparent to-slate-900"></div>
                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl shadow-slate-900/20">
                                <Zap size={14} />
                                3. Eksekusi Aksi Adaptif
                            </span>
                            <div class="h-px w-20 bg-linear-to-l from-transparent to-slate-900"></div>
                        </div>

                        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                            {#each Object.entries(actionsByVariant) as [variant, actions]}
                                <div class="flex flex-col items-center gap-4 rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
                                    <div class="flex items-center gap-2 text-slate-400">
                                        {#if variantIcons[variant]}
                                            {@const Icon = variantIcons[variant]}
                                            <Icon size={12} />
                                        {/if}
                                        <span class="text-[10px] font-black uppercase tracking-widest">{variant}</span>
                                    </div>
                                    <div class="flex flex-wrap justify-center gap-2">
                                        {#each actions as action}
                                            <span class="rounded-xl bg-slate-50 border border-slate-100/50 px-3 py-1.5 text-[10px] font-black text-slate-600 tracking-tight shadow-sm hover:scale-105 transition-transform" title={action.name}>
                                                {action.code}
                                            </span>
                                        {/each}
                                    </div>
                                </div>
                            {/each}
                        </div>
                    </div>

                    <div class="flex justify-center pt-8">
                        <ArrowRight size={20} class="text-slate-200" />
                    </div>

                    <div class="flex justify-center">
                        <div class="flex h-20 w-48 items-center justify-center rounded-3xl bg-emerald-500 text-white shadow-2xl shadow-emerald-500/30 ring-8 ring-emerald-50">
                            <CheckCircle2 size={32} />
                        </div>
                    </div>

                    <Card hover={false} className="border-none shadow-none bg-slate-50/50 rounded-[3rem] p-8">
                        <div class="flex flex-wrap items-center justify-between gap-10">
                            <div class="flex flex-wrap items-center gap-8">
                                <div class="flex items-center gap-3">
                                    <div class="h-3 w-3 rounded-full bg-rose-500 shadow-sm shadow-rose-500/50"></div>
                                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Safety (Critical)</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="h-3 w-3 rounded-full bg-blue-500 shadow-sm shadow-blue-500/50"></div>
                                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Project</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="h-3 w-3 rounded-full bg-amber-500 shadow-sm shadow-amber-500/50"></div>
                                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Achievement</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="h-3 w-3 rounded-full bg-emerald-500 shadow-sm shadow-emerald-500/50"></div>
                                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Recovery</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="h-3 w-3 rounded-full bg-purple-500 shadow-sm shadow-purple-500/50"></div>
                                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Progression</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <span class="flex items-center gap-2 text-[10px] font-black text-emerald-600 uppercase tracking-widest bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100">
                                    <CheckCircle2 size={12} />
                                    Sequential Matching
                                </span>
                                <span class="flex items-center gap-2 text-[10px] font-black text-amber-600 uppercase tracking-widest bg-amber-50 px-3 py-1 rounded-full border border-amber-100">
                                    <AlertTriangle size={12} />
                                    Smallest Index Priority
                                </span>
                            </div>
                        </div>
                    </Card>
                </div>
            </div>
        {/if}
    </div>
</App>