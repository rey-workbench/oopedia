<!-- svelte-ignore element_invalid_self_closing_tag -->
<reference types="d3" />
<script lang="ts">
    import { onMount, untrack } from 'svelte';
    import * as d3 from 'd3';
    import type { AdaptiveAnalyticsState } from '@/states/Admin/AdaptiveAnalyticsState.svelte';
    import { 
        Brain, 
        Maximize2, 
        Minimize2,
        RefreshCw,
        Info,
        Zap
    } from 'lucide-svelte';
    import Badge from '@/components/ui/Badge.svelte';

    interface Props {
        analyticsState: AdaptiveAnalyticsState;
    }

    let { analyticsState }: Props = $props();

    let svgRef = $state<SVGSVGElement | null>(null);
    let containerRef = $state<HTMLDivElement | null>(null);
    let isFullscreen = $state(false);
    let selectedNode = $state<any>(null);
    let zoomLevel = $state(1);

    // Palette inspired by the reference image
    const depthConfig: Record<number, { bg: string, text: string }> = {
        0: { bg: '#ffffff', text: '#020617' }, // Root
        1: { bg: '#ffffff', text: '#020617' }, // Decisions
        2: { bg: '#ffffff', text: '#020617' }, // Sub-decisions
        3: { bg: '#ffffff', text: '#020617' }, // Rules
        4: { bg: '#f8fafc', text: '#64748b' }, // Terminal
    };

    const getDepthStyle = (d: any) => {
        const config = depthConfig[d.depth] || depthConfig[4];
        return config;
    };

    // Use the actual recursive tree from state
    const treeDataObject = $derived(analyticsState.decisionTree);

    let zoom: d3.ZoomBehavior<SVGSVGElement, unknown>;
    let mainGroup: d3.Selection<SVGGElement, unknown, null, undefined>;

    function initTree() {
        if (!svgRef || !containerRef || !treeDataObject) return;

        const width = containerRef.clientWidth;
        const height = isFullscreen ? window.innerHeight - 100 : 750;

        const svg = d3.select(svgRef)
            .attr("viewBox", `0 0 ${width} ${height}`)
            .attr("width", width)
            .attr("height", height);

        svg.selectAll("*").remove();

        // Arrow marker definition
        svg.append("defs").append("marker")
            .attr("id", "arrowhead")
            .attr("viewBox", "0 -5 10 10")
            .attr("refX", 10)
            .attr("refY", 0)
            .attr("markerWidth", 6)
            .attr("markerHeight", 6)
            .attr("orient", "auto")
            .append("path")
            .attr("d", "M0,-5L10,0L0,5")
            .attr("fill", "#94a3b8");

        // Setup Zoom
        zoom = d3.zoom<SVGSVGElement, unknown>()
            .scaleExtent([0.2, 4])
            .on("zoom", (event: d3.D3ZoomEvent<SVGSVGElement, unknown>) => {
                mainGroup.attr("transform", event.transform.toString());
                zoomLevel = event.transform.k;
            });

        svg.call(zoom);

        mainGroup = svg.append("g");

        const margin = { top: 80, right: 100, bottom: 80, left: 100 };
        
        const nodeWidth = 140;
        const nodeHeight = 50;

        const treeLayout = d3.tree<any>()
            .nodeSize([nodeWidth + 60, nodeHeight + 80]); // Adjust spacing to be more compact like schematic

        const rootNode = d3.hierarchy<any>(treeDataObject);
        treeLayout(rootNode);

        // Links with Arrows
        mainGroup.selectAll(".link")
            .data(rootNode.links())
            .enter()
            .append("path")
            .attr("class", "link")
            .attr("fill", "none")
            .attr("stroke", "#94a3b8")
            .attr("stroke-width", 1.5)
            .attr("marker-end", "url(#arrowhead)")
            .attr("d", (d: any) => {
                const sourceX = d.source.x + width / 2;
                const sourceY = d.source.y + margin.top + nodeHeight / 2;
                const targetX = d.target.x + width / 2;
                const targetY = d.target.y + margin.top - nodeHeight / 2;
                
                return `M${sourceX},${sourceY}L${targetX},${targetY}`;
            });

        // Nodes
        const nodes = mainGroup.selectAll(".node")
            .data(rootNode.descendants(), (d: any) => d.data.id || d.data.name)
            .enter()
            .append("g")
            .attr("class", "node cursor-pointer transition-all")
            .attr("transform", (d: any) => `translate(${(d.x || 0) + width / 2},${(d.y || 0) + margin.top})`)
            .on("click", (event: any, d: any) => {
                if (event.defaultPrevented) return;
                selectedNode = d.data;
            });

        // Background Rect (Schematic Style)
        nodes.append("rect")
            .attr("width", nodeWidth)
            .attr("height", nodeHeight)
            .attr("x", -nodeWidth / 2)
            .attr("y", -nodeHeight / 2)
            .attr("fill", "white")
            .attr("stroke", "#000000") // Sharp black borders
            .attr("stroke-width", (d: any) => d.data.is_terminal ? 2.5 : 0.8) // Thick for actions
            .attr("rx", 2)
            .attr("ry", 2)
            .attr("class", d => d.data.is_terminal ? "shadow-sm" : "");

        // Label Wrapper
        const labelArea = nodes.append("g")
            .attr("class", "select-none");

        // Label Text
        labelArea.append("text")
            .attr("text-anchor", "middle")
            .attr("dominant-baseline", "middle")
            .attr("class", (d: any) => d.data.is_terminal ? "text-[10px] font-black fill-slate-900" : "text-[11px] font-bold fill-slate-700")
            .selectAll("tspan")
            .data((d: any) => {
                const name = d.data.name;
                const actionCode = d.data.action_code;
                
                if (d.data.is_terminal && actionCode) {
                    return ["«Action»", actionCode];
                }
                
                // Truncate long names but keep G-codes visible
                return name.length > 22 ? [name.slice(0, 20) + "..."] : [name];
            })
            .enter()
            .append("tspan")
            .attr("x", 0)
            .attr("dy", (_, i: number) => i === 0 ? 0 : "1.2em")
            .text((d: any) => d);

        // Initial Transform - Center the root
        svg.call(zoom.transform, d3.zoomIdentity.translate(0, 100).scale(0.8));
    }

    function resetView() {
        if (!svgRef || !zoom) return;
        d3.select(svgRef).transition().duration(750)
            .call(zoom.transform, d3.zoomIdentity.translate(0, 0).scale(0.8));
    }

    onMount(() => {
        initTree();
        window.addEventListener('resize', initTree);
        return () => window.removeEventListener('resize', initTree);
    });

    $effect(() => {
        // Redraw when domain data changes
        if (analyticsState.rulesByDomain.length > 0) {
            untrack(() => initTree());
        }
    });

    function toggleFullscreen() {
        isFullscreen = !isFullscreen;
        setTimeout(initTree, 100);
    }
</script>

<div class="relative w-full rounded-3xl bg-white border-2 border-slate-100 shadow-xl overflow-hidden {isFullscreen ? 'fixed inset-0 z-50 p-6' : 'h-[850px]'}" bind:this={containerRef}>
    <!-- Header -->
    <div class="absolute top-6 left-6 right-6 z-10 flex items-center justify-between pointer-events-none">
        <div class="flex items-center gap-4 bg-white/80 backdrop-blur-xl p-3 px-5 rounded-2xl border border-slate-200 shadow-lg pointer-events-auto">
            <div class="p-2.5 bg-slate-900 rounded-xl text-white shadow-inner">
                <Brain size={22} />
            </div>
            <div>
                <h3 class="text-lg font-black text-slate-900 tracking-tight leading-none mb-1">Decision Matrix</h3>
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Rule-Based Forward Chaining Engine</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 pointer-events-auto">
            <div class="flex items-center gap-1 bg-white/80 backdrop-blur-xl p-1.5 rounded-xl border border-slate-200 shadow-lg mr-2">
                <button 
                    onclick={resetView}
                    class="p-2 hover:bg-slate-100 rounded-lg text-slate-500 transition-all hover:text-slate-900 active:scale-95"
                    title="Reset View"
                >
                    <RefreshCw size={18} />
                </button>
                <div class="w-px h-6 bg-slate-200 mx-1"></div>
                <button 
                    onclick={toggleFullscreen}
                    class="p-2 hover:bg-slate-100 rounded-lg text-slate-500 transition-all hover:text-slate-900 active:scale-95"
                    title={isFullscreen ? 'Exit Fullscreen' : 'Fullscreen'}
                >
                    {#if isFullscreen}
                        <Minimize2 size={18} />
                    {:else}
                        <Maximize2 size={18} />
                    {/if}
                </button>
            </div>
        </div>
    </div>

    <!-- Tree Canvas -->
    <div class="w-full h-full bg-slate-50/50 relative">
        <!-- Grid Pattern Overlay -->
        <div class="absolute inset-0 pointer-events-none opacity-[0.03]" style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 24px 24px;"></div>
        
        <svg bind:this={svgRef} class="w-full h-full block touch-none group"></svg>
        
        <!-- Legend Bottom Right -->
        <div class="absolute bottom-6 right-6 flex flex-col gap-2 pointer-events-none">
            {#each Object.entries(depthConfig) as [depth, config]}
                <div class="flex items-center gap-3 bg-white/90 backdrop-blur border border-slate-200 p-2 px-4 rounded-xl shadow-sm animate-in fade-in slide-in-from-right-4 pointer-events-auto hover:translate-x-[-4px] transition-transform">
                    <div class="w-3 h-3 rounded-full" style="background: {config.bg}"></div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        {depth === '0' ? 'Root' : (depth === '4' ? 'Leaf' : `Level ${depth}`)}
                    </span>
                </div>
            {/each}
        </div>

        <!-- Node Details Sidebar-style Panel -->
        {#if selectedNode}
            <div class="absolute bottom-6 left-6 w-[320px] bg-white border-2 border-slate-100 rounded-3xl shadow-2xl overflow-hidden animate-in zoom-in-95 fade-in duration-300">
                <div class="p-5">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            {#if selectedNode.type === 'rule'}
                                <div class="p-3 rounded-2xl bg-slate-900 text-white shadow-lg">
                                    <Zap size={20} />
                                </div>
                            {:else}
                                <div class="p-3 rounded-2xl bg-blue-50 text-blue-600 shadow-sm border border-blue-100">
                                    <Brain size={20} />
                                </div>
                            {/if}
                            <div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{selectedNode.type}</span>
                                <h4 class="text-sm font-bold text-slate-900 line-clamp-1">{selectedNode.name}</h4>
                            </div>
                        </div>
                        <button onclick={() => selectedNode = null} class="text-slate-400 hover:text-slate-600 p-1">
                            <Minimize2 size={16} />
                        </button>
                    </div>

                    <div class="space-y-4">
                        {#if selectedNode.type === 'rule'}
                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">ID Code</p>
                                    <p class="text-xs font-black text-slate-900 font-mono tracking-tighter">{selectedNode.id}</p>
                                </div>
                                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Priority</p>
                                    <p class="text-xs font-black text-slate-900 leading-none">P{selectedNode.priority}</p>
                                </div>
                            </div>
                            
                            <div class="p-4 bg-emerald-50/50 border border-emerald-100 rounded-2xl">
                                <p class="text-[9px] font-bold text-emerald-600 uppercase mb-2 flex items-center gap-1">
                                    <Zap size={10} /> Triggered Action
                                </p>
                                <div class="flex items-center gap-2">
                                    <Badge variant="primary" class="bg-emerald-500 text-white border-0 shadow-sm">{selectedNode.action}</Badge>
                                </div>
                            </div>
                        {:else}
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-xs text-slate-600 leading-relaxed italic">
                                Domain level grouping for strategic rule partitioning. Rules within this domain are evaluated sequentially.
                            </div>
                        {/if}
                        
                        <div class="flex items-center gap-2 p-3 px-4 bg-slate-100/50 rounded-2xl text-[10px] font-medium text-slate-500">
                            <Info size={14} />
                            <span>First rule that matches its conditions will fire.</span>
                        </div>
                    </div>
                </div>
            </div>
        {/if}

        <!-- Zoom Indicator -->
        <div class="absolute top-6 left-1/2 -translate-x-1/2 px-4 py-1.5 bg-slate-900/10 backdrop-blur-md rounded-full border border-white/20 text-[10px] font-bold text-slate-600 tracking-widest uppercase">
            Zoom {Math.round(zoomLevel * 100)}%
        </div>
    </div>
</div>

<style>
    :global(.link) {
        transition: stroke-opacity 0.4s ease, stroke-width 0.4s ease;
    }
    
    :global(.node) {
        pointer-events: all;
    }

    :global(.node:hover circle) {
        filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
    }
</style>
