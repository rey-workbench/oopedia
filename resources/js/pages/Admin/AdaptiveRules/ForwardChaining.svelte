<script lang="ts">
    /// <reference types="d3" />
    import { onMount, untrack } from 'svelte';
    import * as d3 from 'd3';
    import type { AdaptiveRuleState } from '@/states/Admin/AdaptiveRuleState.svelte';
    import NodePreviewPanel from './NodePreviewPanel.svelte';
    import {
        resolveGraphTopology,
        buildGraphLinks,
        calculateSpatialCoordinates,
    } from './topology';
    import {
        Zap,
        Target,
    } from 'lucide-svelte';
    interface Props {
        analyticsState: AdaptiveRuleState;
        onedit?: ((rule: any) => void) | undefined;
        oneditaction?: ((action: any) => void) | undefined;
        isFullscreen?: boolean;
        fullscreenTarget?: HTMLElement | null;
    }

    let {
        analyticsState,
        onedit,
        oneditaction,
        isFullscreen = $bindable(false),
        fullscreenTarget = null,
    }: Props = $props();

    let svgRef = $state<SVGSVGElement | null>(null);
    let containerRef = $state<HTMLDivElement | null>(null);
    let selectedNode = $state<any>(null);
    let zoomLevel = $state(0.8);
    let resetD3Flow = $state(() => {});

    onMount(() => {
        const handleFullscreenChange = () => {
            isFullscreen = !!document.fullscreenElement;
        };

        document.addEventListener('fullscreenchange', handleFullscreenChange);
        return () => {
            document.removeEventListener('fullscreenchange', handleFullscreenChange);
        };
    });

    const factData = $derived(analyticsState.allFacts);
    const actionData = $derived(analyticsState.allActions);
    const rules = $derived(analyticsState.rulesByDiagnosis.flatMap((d) => d.rules));

    // ─── CONFIGURATION & CONSTANTS ──────────────────────────────────────────

    const LAYERS = [
        { id: 'input', label: 'INPUT', color: 'bg-slate-100/50' },
        { id: 'condition', label: 'KONDISI', color: 'bg-indigo-50/30' },
        { id: 'diagnosis', label: 'DIAGNOSIS', color: 'bg-emerald-50/30' },
        { id: 'recommendation', label: 'REKOMENDASI', color: 'bg-amber-50/30' },
    ];

    let isInitialized = false;
    let zoom: d3.ZoomBehavior<SVGSVGElement, unknown>;
    let mainGroup: d3.Selection<SVGGElement, unknown, null, undefined>;

    /**
     * Main orchestration function for the visualization.
     */
    function initSchematic() {
        if (!svgRef || !containerRef || !factData || factData.length === 0) return;

        const dimensions = prepareCanvas();
        const nodeRegistry = resolveGraphTopology(rules, factData, actionData);
        const links = buildGraphLinks(nodeRegistry, rules);
        const nodes = Array.from(nodeRegistry.values());

        calculateSpatialCoordinates(nodes, links, dimensions.height);
        renderGraphLocal(nodes, links, dimensions.width, dimensions.height);
    }

    /**
     * Prepares the SVG canvas, markers, and zoom behavior.
     */
    function prepareCanvas() {
        const width = containerRef!.clientWidth;
        const height = isFullscreen ? window.innerHeight - 100 : 750;

        const svg = d3
            .select(svgRef!)
            .attr('viewBox', `0 0 ${width} ${height}`)
            .attr('width', width)
            .attr('height', height);

        svg.selectAll('*').remove();
        setupMarkers(svg.append('defs'));

        zoom = d3
            .zoom<SVGSVGElement, unknown>()
            .scaleExtent([0.1, 4])
            .on('zoom', (event) => {
                mainGroup.attr('transform', event.transform.toString());
                zoomLevel = event.transform.k;
            });

        svg.call(zoom);
        svg.on('click', () => {
            selectedNode = null;
            resetD3Flow();
        });

        mainGroup = svg.append('g');
        return { width, height, svg };
    }


    import { 
        setupMarkers, 
        renderGraph,
        type RenderContext
    } from './render';

    function handleNodeClick(d: any, event: MouseEvent) {
        event.stopPropagation();
        highlightFlow(d);
    }

    function handleNodeContextMenu(d: any, event: MouseEvent) {
        event.preventDefault();
        event.stopPropagation();
        selectedNode = d;
        highlightFlow(d);
    }

    function renderGraphLocal(nodes: any[], links: any[], width: number, height: number) {
        const ctx: RenderContext = {
            svgRef,
            mainGroup,
            zoom,
            isInitialized,
            isFullscreen,
            zoomLevel,
            handleNodeClick,
            handleNodeContextMenu,
            resetD3Flow
        };

        const initialized = renderGraph(nodes, links, width, height, ctx, LAYERS);
        if (initialized) isInitialized = true;
    }

    function highlightFlow(clickedNode: any) {
        resetD3Flow();
        const activeLinks = new Set();
        const activeNodes = new Set([clickedNode]);

        const traverse = (node: any, direction: 'source' | 'target') => {
            const relKey = direction === 'source' ? 'target' : 'source';
            mainGroup.selectAll('.link').each(function (l: any) {
                if (l[direction] === node) {
                    activeLinks.add(l);
                    activeNodes.add(l[relKey]);
                    traverse(l[relKey], direction);
                }
            });
        };

        traverse(clickedNode, 'source');
        traverse(clickedNode, 'target');

        mainGroup
            .selectAll('.rect-node, .gate-node')
            .attr('opacity', (n: any) => (activeNodes.has(n) ? 1 : 0.15))
            .each(function (n: any) {
                const node = d3.select(this);
                const isSelected = n === clickedNode;
                node.selectAll('.focus-ring')
                    .attr('stroke-width', isSelected ? 3 : 0)
                    .attr('opacity', isSelected ? 1 : 0);
            });

        mainGroup
            .selectAll('.link')
            .attr('stroke-opacity', (l: any) => (activeLinks.has(l) ? 1 : 0.05))
            .attr('stroke-width', (l: any) => (activeLinks.has(l) ? 4 : 1.5))
            .classed('flow-active', (l: any) => activeLinks.has(l));
    }

    resetD3Flow = () => {
        if (!mainGroup) return;
        mainGroup
            .selectAll('.link')
            .classed('flow-active', false)
            .attr('stroke-opacity', 0.4)
            .attr('stroke-width', 2);
        
        mainGroup.selectAll('.rect-node, .gate-node')
            .attr('opacity', 1)
            .selectAll('.focus-ring')
            .attr('stroke-width', 0)
            .attr('opacity', 0);
    };

    export const resetView = () => {
        if (!svgRef || !zoom) return;
        d3.select(svgRef)
            .transition()
            .duration(750)
            .call(zoom.transform, d3.zoomIdentity.translate(80, 50).scale(0.8));
    };

    onMount(() => {
        initSchematic();
        window.addEventListener('resize', initSchematic);
        const handleFullscreen = () => {
            isFullscreen = !!document.fullscreenElement;
            setTimeout(initSchematic, 100);
        };
        document.addEventListener('fullscreenchange', handleFullscreen);
        return () => {
            window.removeEventListener('resize', initSchematic);
            document.removeEventListener('fullscreenchange', handleFullscreen);
        };
    });

    $effect(() => {
        if (analyticsState.rulesByDiagnosis.length > 0 || analyticsState.allFacts.length > 0) {
            untrack(() => initSchematic());
        }
    });

    export const toggleFullscreen = async () => {
        const target = fullscreenTarget || containerRef;
        if (!target) return;
        if (!document.fullscreenElement) await target.requestFullscreen();
        else await document.exitFullscreen();
    };
</script>

<style>
    :global(.link) {
        transition:
            stroke-opacity 0.4s ease,
            stroke-width 0.4s ease;
    }
    :global(.flow-active) {
        stroke-dasharray: 8;
        animation: flow 1.5s linear infinite;
    }
    @keyframes flow {
        from {
            stroke-dashoffset: 16;
        }
        to {
            stroke-dashoffset: 0;
        }
    }
</style>

<div
    class="relative w-full overflow-hidden bg-white {isFullscreen
        ? 'fixed inset-0 z-100 h-screen w-screen rounded-none'
        : 'h-[750px] rounded-3xl border-2 border-slate-100 shadow-xl'}"
    bind:this={containerRef}
    role="region"
    aria-label="Strategy Visualization Canvas"
    ondragover={(e) => {
        e.preventDefault();
        if (e.dataTransfer) e.dataTransfer.dropEffect = 'copy';
    }}
    ondrop={(e) => {
        e.preventDefault();
        const type = e.dataTransfer?.getData('type');
        if (type === 'rule' && onedit) onedit(null);
        if (type === 'action' && oneditaction) oneditaction(null);
    }}
>
    <!-- The canvas header has been moved to Index.svelte -->

    <!-- Tree Canvas -->
    <div class="relative h-full w-full bg-slate-50/50">
        <div
            class="pointer-events-none absolute inset-0 opacity-[0.03]"
            style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 24px 24px;"
        ></div>

        <button
            type="button"
            class="group absolute inset-0 h-full w-full touch-none outline-none"
            onclick={() => (selectedNode = null)}
            onkeydown={(e) => {
                if (e.key === 'Escape') selectedNode = null;
            }}
            aria-label="Workflow Canvas"
            ondragover={(e) => {
                e.preventDefault();
                e.dataTransfer!.dropEffect = 'copy';
            }}
            ondrop={(e) => {
                e.preventDefault();
                const type = e.dataTransfer?.getData('type');
                if (type === 'rule' && onedit) onedit(null);
                if (type === 'action' && oneditaction) oneditaction(null);
            }}
        >
            <svg
                bind:this={svgRef}
                class="block h-full w-full"
                role="presentation"
                oncontextmenu={(e) => e.preventDefault()}
            ></svg>
        </button>

        <!-- Palette Sidebar (n8n Style) -->
        <div
            class="pointer-events-none absolute top-1/2 left-8 z-10 flex -translate-y-1/2 flex-col gap-6"
        >
            <div class="pointer-events-auto flex flex-col gap-4">
                <div
                    class="group hover:border-primary-400 hover:shadow-primary-900/10 focus:ring-primary-500 relative flex cursor-grab flex-col items-center gap-2 rounded-3xl border border-slate-200 bg-white/90 p-4 shadow-xl backdrop-blur-xl transition-all focus:ring-2 focus:outline-none active:cursor-grabbing"
                    draggable="true"
                    role="button"
                    tabindex="0"
                    aria-label="Add New Rule"
                    ondragstart={(e) => {
                        e.dataTransfer?.setData('type', 'rule');
                        e.dataTransfer!.effectAllowed = 'copy';
                    }}
                    onkeydown={(e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            if (onedit) onedit(null);
                        }
                    }}
                    onclick={() => {
                        if (onedit) onedit(null);
                    }}
                >
                    <div
                        class="bg-primary-50 text-primary-600 rounded-2xl p-3 shadow-sm transition-transform group-hover:scale-110"
                    >
                        <Zap size={28} />
                    </div>
                    <span class="text-[9px] font-black tracking-tighter text-slate-500 uppercase"
                        >New Rule</span
                    >

                    <!-- Tooltip -->
                    <div
                        class="pointer-events-none absolute left-full ml-4 rounded-lg bg-slate-900 px-3 py-1.5 text-[10px] font-bold whitespace-nowrap text-white opacity-0 transition-opacity group-hover:opacity-100"
                    >
                        Tarik ke kanvas untuk buat Aturan
                    </div>
                </div>

                <div
                    class="group relative flex cursor-grab flex-col items-center gap-2 rounded-3xl border border-slate-200 bg-white/90 p-4 shadow-xl backdrop-blur-xl transition-all hover:border-emerald-400 hover:shadow-emerald-900/10 focus:ring-2 focus:ring-emerald-500 focus:outline-none active:cursor-grabbing"
                    draggable="true"
                    role="button"
                    tabindex="0"
                    aria-label="Add New Action"
                    ondragstart={(e) => {
                        e.dataTransfer?.setData('type', 'action');
                        e.dataTransfer!.effectAllowed = 'copy';
                    }}
                    onkeydown={(e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            if (oneditaction) oneditaction(null);
                        }
                    }}
                    onclick={() => {
                        if (oneditaction) oneditaction(null);
                    }}
                >
                    <div
                        class="rounded-2xl bg-emerald-50 p-3 text-emerald-600 shadow-sm transition-transform group-hover:scale-110"
                    >
                        <Target size={28} />
                    </div>
                    <span class="text-[9px] font-black tracking-tighter text-slate-500 uppercase"
                        >New Action</span
                    >

                    <!-- Tooltip -->
                    <div
                        class="pointer-events-none absolute left-full ml-4 rounded-lg bg-slate-900 px-3 py-1.5 text-[10px] font-bold whitespace-nowrap text-white opacity-0 transition-opacity group-hover:opacity-100"
                    >
                        Tarik ke kanvas untuk buat Aksi
                    </div>
                </div>
            </div>
        </div>

        <!-- Legend Bottom Right -->
        <div class="pointer-events-none absolute right-6 bottom-6 flex flex-col gap-2">
            <div
                class="pointer-events-auto flex items-center gap-3 rounded-xl border border-slate-200 bg-white/90 p-2 px-4 shadow-sm backdrop-blur transition-transform"
            >
                <div class="h-3 w-3 rounded-sm border-2 border-slate-200 bg-white"></div>
                <span class="text-[10px] font-bold tracking-wider text-slate-500 uppercase"
                    >Fakta Teramati</span
                >
            </div>
            <div
                class="pointer-events-auto flex items-center gap-3 rounded-xl border border-slate-200 bg-white/90 p-2 px-4 shadow-sm backdrop-blur transition-transform"
            >
                <div class="h-3 w-3 rounded-sm border-2 border-blue-400 bg-blue-50"></div>
                <span class="text-[10px] font-bold tracking-wider text-slate-500 uppercase"
                    >Fakta Virtual</span
                >
            </div>
            <div
                class="pointer-events-auto flex items-center gap-3 rounded-xl border border-slate-200 bg-white/90 p-2 px-4 shadow-sm backdrop-blur transition-transform"
            >
                <div class="h-3 w-3 rounded-full border-2 border-slate-600 bg-white"></div>
                <span class="text-[10px] font-bold tracking-wider text-slate-500 uppercase"
                    >Gerbang Logika (Aturan)</span
                >
            </div>
            <div
                class="pointer-events-auto flex items-center gap-3 rounded-xl border border-slate-200 bg-white/90 p-2 px-4 shadow-sm backdrop-blur transition-transform"
            >
                <div class="h-3 w-3 rounded-sm bg-emerald-600"></div>
                <span class="text-[10px] font-bold tracking-wider text-slate-500 uppercase"
                    >Keputusan Adaptif</span
                >
            </div>
        </div>

        <!-- n8n-style Node Detail Panel -->
        <NodePreviewPanel 
            {selectedNode} 
            {factData}
            {actionData}
            onclose={() => (selectedNode = null)} 
            {onedit} 
            {oneditaction} 
            {resetD3Flow}
        />

        <!-- Zoom Indicator -->
        <div
            class="absolute top-6 left-1/2 -translate-x-1/2 rounded-full border border-white/20 bg-slate-900/10 px-4 py-1.5 text-[10px] font-bold tracking-widest text-slate-600 uppercase backdrop-blur-md"
        >
            Skala {Math.round(zoomLevel * 100)}%
        </div>
    </div>
</div>
