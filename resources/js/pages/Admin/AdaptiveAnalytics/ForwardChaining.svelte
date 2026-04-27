<script lang="ts">
    /// <reference types="d3" />
    import { onMount, untrack } from 'svelte';
    import * as d3 from 'd3';
    import type { AdaptiveAnalyticsState } from '@/states/Admin/AdaptiveAnalyticsState.svelte';
    import {
        Brain,
        Maximize2,
        Minimize2,
        RefreshCw,
        GitBranch,
        Target,
        X,
        Pencil,
    } from 'lucide-svelte';

    interface Props {
        analyticsState: AdaptiveAnalyticsState;
        onedit?: (rule: any) => void;
    }

    let { analyticsState, onedit }: Props = $props();

    let svgRef = $state<SVGSVGElement | null>(null);
    let containerRef = $state<HTMLDivElement | null>(null);
    let isFullscreen = $state(false);
    let selectedNode = $state<any>(null);
    let zoomLevel = $state(0.8);
    let resetD3Flow = () => {};

    const factData = $derived(analyticsState.allFacts);
    const actionData = $derived(analyticsState.allActions);
    const rules = $derived(analyticsState.rulesByDomain.flatMap((d) => d.rules));

    // ─── CONFIGURATION & CONSTANTS ──────────────────────────────────────────
    const GRAPH_CONFIG = {
        colWidth: 350,
        itemHeight: 50,
        verticalGap: 24,
        startX: 180,
        maxTopologyIterations: 30,
        barycenterIterations: 12,
        initialScale: 0.7,
    };

    let isInitialized = false;
    let zoom: d3.ZoomBehavior<SVGSVGElement, unknown>;
    let mainGroup: d3.Selection<SVGGElement, unknown, null, undefined>;

    /**
     * Main orchestration function for the visualization.
     */
    function initSchematic() {
        if (!svgRef || !containerRef || !factData || factData.length === 0) return;

        const dimensions = prepareCanvas();
        const nodeRegistry = resolveGraphTopology();
        const links = buildGraphLinks(nodeRegistry);
        const nodes = Array.from(nodeRegistry.values());

        calculateSpatialCoordinates(nodes, links, dimensions.height);
        renderGraph(nodes, links, dimensions.width, dimensions.height);
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

        zoom = d3.zoom<SVGSVGElement, unknown>()
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

    function setupMarkers(defs: d3.Selection<SVGDefsElement, unknown, null, undefined>) {
        const markers = [
            { id: 'arrow-requirement', color: '#cbd5e1' },
            { id: 'arrow-deduction', color: '#60a5fa' },
            { id: 'arrow-action', color: '#34d399' },
        ];

        markers.forEach(({ id, color }) => {
            defs.append('marker')
                .attr('id', id)
                .attr('viewBox', '0 -5 10 10')
                .attr('refX', 10)
                .attr('refY', 0)
                .attr('markerWidth', 5)
                .attr('markerHeight', 5)
                .attr('orient', 'auto')
                .append('path')
                .attr('d', 'M0,-5L10,0L0,5')
                .attr('fill', color);
        });
    }

    /**
     * Resolves nodes and their hierarchical depths using an iterative DAG topo-sort.
     */
    function resolveGraphTopology() {
        const nodeRegistry = new Map<string, any>();

        // Phase 1: Initialize Raw Facts
        factData
            .filter((f) => !f.code.startsWith('V'))
            .forEach((f) => {
                nodeRegistry.set(`raw_fact_${f.code}`, {
                    type: 'raw_fact',
                    depth: 0,
                    data: f,
                    id: `f_${f.code}`,
                });
            });

        // Phase 2: Iteratively resolve rules, virtual facts, and actions
        let hasTopologyChanged = true;
        let iterations = GRAPH_CONFIG.maxTopologyIterations;

        while (hasTopologyChanged && iterations-- > 0) {
            hasTopologyChanged = false;

            rules.forEach((rule) => {
                const prerequisites = rule.required_facts || [];
                const resolvedSources = prerequisites.map(p => 
                    nodeRegistry.get(`raw_fact_${p}`) || nodeRegistry.get(`virtual_fact_${p}`)
                );

                const isResolved = resolvedSources.every(s => !!s);
                if (!isResolved) return;

                const maxPrereqDepth = Math.max(...resolvedSources.map(s => s.depth));
                const ruleDepth = maxPrereqDepth + 1;

                // Register Rule Gate
                const ruleNodeId = `rule_${rule.id}`;
                if (!nodeRegistry.has(ruleNodeId)) {
                    nodeRegistry.set(ruleNodeId, {
                        type: 'gate',
                        depth: ruleDepth,
                        data: rule,
                        id: `r_${rule.id}`,
                    });
                    hasTopologyChanged = true;
                }

                // Register Deduced Facts
                if (rule.deduced_facts) {
                    rule.deduced_facts.forEach((code: string) => {
                        if (registerVirtualFact(nodeRegistry, code, ruleDepth + 1)) {
                            hasTopologyChanged = true;
                        }
                    });
                }

                // Register Resulting Actions
                if (registerAction(nodeRegistry, rule, ruleDepth + 1)) {
                    hasTopologyChanged = true;
                }
            });
        }

        return nodeRegistry;
    }

    function registerVirtualFact(registry: Map<string, any>, code: string, depth: number) {
        const fact = factData.find((f) => f.code === code);
        if (!fact) return false;

        const factId = `virtual_fact_${code}`;
        const existing = registry.get(factId);

        if (!existing) {
            registry.set(factId, {
                type: 'virtual_fact',
                depth: depth,
                data: fact,
                id: `vf_${code}`,
            });
            return true;
        }

        if (existing.depth < depth) {
            existing.depth = depth;
            return true;
        }

        return false;
    }

    function registerAction(registry: Map<string, any>, rule: any, depth: number) {
        const action = actionData.find((a) => a.id === rule.action_id || a.code === rule.action_code);
        if (!action || action.code === 'H00') return false;

        const actionId = `action_${rule.id}`;
        if (!registry.has(actionId)) {
            registry.set(actionId, {
                type: 'action',
                depth: depth,
                data: action,
                id: `a_${rule.id}`,
                sourceRule: rule.id,
            });
            return true;
        }
        return false;
    }

    /**
     * Builds relational links between resolved nodes.
     */
    function buildGraphLinks(nodeRegistry: Map<string, any>) {
        const links: any[] = [];

        rules.forEach((rule) => {
            const gateNode = nodeRegistry.get(`rule_${rule.id}`);
            if (!gateNode) return;

            // Link Prerequisites -> Gate
            (rule.required_facts || []).forEach((code) => {
                const source = nodeRegistry.get(`raw_fact_${code}`) || nodeRegistry.get(`virtual_fact_${code}`);
                if (source) links.push({ source, target: gateNode, type: 'requirement' });
            });

            // Link Gate -> Deduced Facts
            (rule.deduced_facts || []).forEach((code) => {
                const target = nodeRegistry.get(`virtual_fact_${code}`);
                if (target) links.push({ source: gateNode, target, type: 'deduction' });
            });

            // Link Gate -> Final Action
            const actionNode = nodeRegistry.get(`action_${rule.id}`);
            if (actionNode) links.push({ source: gateNode, target: actionNode, type: 'action' });
        });

        return links;
    }

    /**
     * Orchestrates the spatial layout using a barycenter-based relaxation.
     */
    function calculateSpatialCoordinates(nodes: any[], links: any[], containerHeight: number) {
        const depthGroups: Record<number, any[]> = {};
        nodes.forEach((n) => {
            if (!depthGroups[n.depth]) depthGroups[n.depth] = [];
            depthGroups[n.depth]!.push(n);
        });

        const depths = Object.keys(depthGroups).map(Number).sort((a, b) => a - b);

        // Pass 1: Initial Layout
        depths.forEach((d) => {
            depthGroups[d]!.forEach((n, i) => (n.y = i * (GRAPH_CONFIG.itemHeight + GRAPH_CONFIG.verticalGap)));
        });

        // Pass 2: Iterative Barycenter Optimization
        for (let i = 0; i < GRAPH_CONFIG.barycenterIterations; i++) {
            optimizeLayoutLayer(depths, depthGroups, links, true);
            optimizeLayoutLayer([...depths].reverse(), depthGroups, links, false);
        }

        centerAndProject(depths, depthGroups, containerHeight);
    }

    function optimizeLayoutLayer(depths: number[], groups: Record<number, any[]>, links: any[], forward: boolean) {
        depths.forEach((d) => {
            const group = groups[d]!;
            if (d === (forward ? depths[0] : depths[depths.length - 1])) return;

            group.forEach((node) => {
                const relatives = links
                    .filter((l) => (forward ? l.target === node : l.source === node))
                    .map((l) => (forward ? l.source : l.target));

                if (relatives.length > 0) {
                    node.y = relatives.reduce((sum, r) => sum + r.y, 0) / relatives.length;
                }
            });

            group.sort((a, b) => a.y - b.y);
            resolveLayerOverlaps(group);
        });
    }

    function resolveLayerOverlaps(group: any[]) {
        if (group.length === 0) return;
        let currentY = group[0].y;
        group.forEach((node, i) => {
            if (i > 0) {
                currentY = Math.max(currentY + GRAPH_CONFIG.itemHeight + GRAPH_CONFIG.verticalGap, node.y);
            }
            node.y = currentY;
        });
    }

    function centerAndProject(depths: number[], groups: Record<number, any[]>, height: number) {
        const centerRefY = height / 2;
        let maxX = 0;
        let minY = Infinity, maxY = -Infinity;

        depths.forEach((d) => {
            const group = groups[d]!;
            if (group.length === 0) return;
            const groupCenterY = (group[0].y + group[group.length - 1].y + GRAPH_CONFIG.itemHeight) / 2;
            const offset = centerRefY - groupCenterY;

            group.forEach((node) => {
                node.x = GRAPH_CONFIG.startX + node.depth * GRAPH_CONFIG.colWidth;
                node.y += offset;
                if (node.x > maxX) maxX = node.x;
                minY = Math.min(minY, node.y);
                maxY = Math.max(maxY, node.y);
            });
        });

        return { maxX, minY, maxY };
    }

    /**
     * D3 Rendering Implementation
     */
    function renderGraph(nodes: any[], links: any[], width: number, height: number) {
        // Compute bounding box for zoom-to-fit
        const maxX = Math.max(...nodes.map(n => n.x), 100);
        const minY = Math.min(...nodes.map(n => n.y), 0);
        const maxY = Math.max(...nodes.map(n => n.y), 100);

        drawLinks(links);
        drawNodes(nodes);

        const initialScale = Math.min(GRAPH_CONFIG.initialScale, width / (maxX + 280));
        
        if (!isInitialized) {
            d3.select(svgRef!).call(
                zoom.transform,
                d3.zoomIdentity
                    .translate(width/2 - (maxX/2)*initialScale, height/2 - ((maxY+minY)/2)*initialScale)
                    .scale(initialScale)
            );
            isInitialized = true;
        }
    }

    function drawLinks(links: any[]) {
        mainGroup.selectAll('.link')
            .data(links)
            .enter()
            .append('path')
            .attr('class', 'link')
            .attr('d', generateBezierPath)
            .attr('fill', 'none')
            .attr('stroke', (d) => getLinkColor(d.type))
            .attr('stroke-width', 2)
            .attr('stroke-opacity', 0.4)
            .attr('marker-end', (d) => `url(#arrow-${d.type})`);
    }

    function generateBezierPath(d: any) {
        const sx = d.source.x + (d.source.type === 'gate' ? 25 : 100);
        const sy = d.source.y;
        const tx = d.target.x - (d.target.type === 'gate' ? 25 : 100);
        const ty = d.target.y;
        const dx = tx - sx;
        return `M${sx},${sy}C${sx + dx/2},${sy} ${sx + dx/2},${ty} ${tx},${ty}`;
    }

    function getLinkColor(type: string) {
        const colors = { deduction: '#3b82f6', action: '#10b981', requirement: '#94a3b8' };
        return colors[type as keyof typeof colors] || '#94a3b8';
    }

    function drawNodes(nodes: any[]) {
        const rectNodes = nodes.filter((n) => n.type !== 'gate');
        const gateNodes = nodes.filter((n) => n.type === 'gate');

        const nodeSelection = mainGroup.selectAll('.rect-node')
            .data(rectNodes)
            .enter()
            .append('g')
            .attr('transform', (d) => `translate(${d.x},${d.y})`)
            .attr('class', 'rect-node cursor-pointer select-none')
            .on('click', (event, d) => handleNodeClick(d, event));

        nodeSelection.append('rect')
            .attr('width', 200).attr('height', 40).attr('x', -100).attr('y', -20).attr('rx', (d) => (d.type === 'action' ? 20 : 12))
            .attr('fill', (d) => getNodeFill(d.type))
            .attr('stroke', (d) => getNodeStroke(d.type))
            .attr('stroke-width', 2).attr('class', 'shadow-sm');

        nodeSelection.append('text').attr('text-anchor', 'middle').attr('dominant-baseline', 'middle')
            .attr('class', (d) => `text-[11px] font-bold ${d.type === 'action' ? 'fill-white' : 'fill-slate-800'}`)
            .text((d) => d.data.name);

        const gateSelection = mainGroup.selectAll('.gate-node')
            .data(gateNodes).enter().append('g').attr('transform', (d) => `translate(${d.x},${d.y})`)
            .attr('class', 'gate-node cursor-pointer select-none')
            .on('click', (event, d) => handleNodeClick(d, event));

        gateSelection.append('circle').attr('r', 24).attr('fill', '#0f172a').attr('stroke', '#334155').attr('stroke-width', 2);
        gateSelection.append('text').attr('text-anchor', 'middle').attr('dominant-baseline', 'middle').attr('class', 'text-[10px] font-bold fill-white antialiased').text('AND');
        gateSelection.append('text').attr('text-anchor', 'middle').attr('y', 38).attr('class', 'text-[9px] font-medium fill-slate-400 uppercase tracking-wider').text((d) => d.data.rule_code);
    }

    function getNodeFill(type: string) {
        if (type === 'virtual_fact') return '#f0f7ff';
        if (type === 'action') return '#065f46';
        return '#ffffff';
    }

    function getNodeStroke(type: string) {
        if (type === 'virtual_fact') return '#3b82f6';
        if (type === 'action') return '#047857';
        return '#cbd5e1';
    }

    function handleNodeClick(d: any, event: MouseEvent) {
        selectedNode = d;
        highlightFlow(d);
        event.stopPropagation();
    }

    function highlightFlow(clickedNode: any) {
        resetD3Flow();
        const activeLinks = new Set();
        const activeNodes = new Set([clickedNode]);

        const traverse = (node: any, direction: 'source' | 'target') => {
            const relKey = direction === 'source' ? 'target' : 'source';
            mainGroup.selectAll('.link').each(function(l: any) {
                if (l[direction] === node) {
                    activeLinks.add(l);
                    activeNodes.add(l[relKey]);
                    traverse(l[relKey], direction);
                }
            });
        };

        traverse(clickedNode, 'source');
        traverse(clickedNode, 'target');

        mainGroup.selectAll('.rect-node, .gate-node').attr('opacity', (n: any) => (activeNodes.has(n) ? 1 : 0.15));
        mainGroup.selectAll('.link').attr('stroke-opacity', (l: any) => (activeLinks.has(l) ? 1 : 0.05))
            .attr('stroke-width', (l: any) => (activeLinks.has(l) ? 4 : 1.5))
            .classed('flow-active', (l: any) => activeLinks.has(l));
    }

    resetD3Flow = () => {
        mainGroup.selectAll('.link').classed('flow-active', false).attr('stroke-opacity', 0.4).attr('stroke-width', 2);
        mainGroup.selectAll('.rect-node, .gate-node').attr('opacity', 1);
    };

    function resetView() {
        if (!svgRef || !zoom) return;
        d3.select(svgRef).transition().duration(750).call(zoom.transform, d3.zoomIdentity.translate(80, 50).scale(0.8));
    }

    onMount(() => {
        initSchematic();
        window.addEventListener('resize', initSchematic);
        const handleFullscreen = () => { isFullscreen = !!document.fullscreenElement; setTimeout(initSchematic, 100); };
        document.addEventListener('fullscreenchange', handleFullscreen);
        return () => {
            window.removeEventListener('resize', initSchematic);
            document.removeEventListener('fullscreenchange', handleFullscreen);
        };
    });

    $effect(() => {
        if (analyticsState.rulesByDomain.length > 0 || analyticsState.allFacts.length > 0) {
            untrack(() => initSchematic());
        }
    });

    async function toggleFullscreen() {
        if (!containerRef) return;
        if (!document.fullscreenElement) await containerRef.requestFullscreen();
        else await document.exitFullscreen();
    }
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
        ? 'h-full rounded-none'
        : 'h-[750px] rounded-3xl border-2 border-slate-100 shadow-xl'}"
    bind:this={containerRef}
>
    <!-- Header -->
    <div
        class="pointer-events-none absolute top-6 right-6 left-6 z-10 flex items-center justify-end"
    >
        <div class="pointer-events-auto flex items-center gap-2">
            <div
                class="mr-2 flex items-center gap-1 rounded-xl border border-slate-200 bg-white/80 p-1.5 shadow-lg backdrop-blur-xl"
            >
                <button
                    onclick={resetView}
                    class="rounded-lg p-2 text-slate-500 transition-all hover:bg-slate-100 hover:text-slate-900 active:scale-95"
                    title="Atur Ulang Tampilan"
                >
                    <RefreshCw size={18} />
                </button>
                <div class="mx-1 h-6 w-px bg-slate-200"></div>
                <button
                    onclick={toggleFullscreen}
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
        </div>
    </div>

    <!-- Tree Canvas -->
    <div class="relative h-full w-full bg-slate-50/50">
        <div
            class="pointer-events-none absolute inset-0 opacity-[0.03]"
            style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 24px 24px;"
        ></div>

        <svg bind:this={svgRef} class="group block h-full w-full touch-none"></svg>

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

        <!-- Node Details -->       
        {#if selectedNode}
            <div
                class="animate-in zoom-in-95 fade-in absolute bottom-6 left-6 w-[320px] overflow-hidden rounded-3xl border-2 border-slate-100 bg-white shadow-2xl duration-300"
            >
                <div class="p-5">
                    <div class="mb-4 flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            {#if selectedNode.type === 'gate'}
                                <div class="rounded-2xl bg-slate-900 p-3 text-white shadow-lg">
                                    <Brain size={20} />
                                </div>
                            {:else if selectedNode.type === 'raw_fact' || selectedNode.type === 'virtual_fact'}
                                <div
                                    class="rounded-2xl border border-blue-100 bg-blue-50 p-3 text-blue-600 shadow-sm"
                                >
                                    <GitBranch size={20} />
                                </div>
                            {:else}
                                <div
                                    class="rounded-2xl border border-emerald-100 bg-emerald-50 p-3 text-emerald-600 shadow-sm"
                                >
                                    <Target size={20} />
                                </div>
                            {/if}
                            <div class="flex flex-col gap-1">
                                <span
                                    class="text-[10px] font-black tracking-widest text-slate-400 uppercase"
                                    >{selectedNode.type === 'raw_fact'
                                        ? 'FAKTA MENTAH'
                                        : selectedNode.type === 'virtual_fact'
                                          ? 'FAKTA VIRTUAL'
                                          : selectedNode.type === 'gate'
                                            ? 'GERBANG'
                                            : selectedNode.type === 'action'
                                              ? 'AKSI'
                                              : selectedNode.type.replace('_', ' ')}</span
                                >
                                <h4 class="line-clamp-1 text-sm font-bold text-slate-900">
                                    {selectedNode.data.name}
                                </h4>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            {#if selectedNode.type === 'gate' && onedit}
                                <button
                                    onclick={() => onedit(selectedNode.data)}
                                    class="hover:text-primary-600 hover:bg-primary-50 rounded-xl p-1.5 text-slate-400 transition-colors"
                                    title="Ubah Aturan"
                                >
                                    <Pencil size={14} />
                                </button>
                            {/if}
                            <button
                                onclick={() => {
                                    selectedNode = null;
                                    resetD3Flow();
                                }}
                                class="rounded-xl p-1.5 text-slate-400 transition-colors hover:bg-slate-50 hover:text-slate-600"
                            >
                                <X size={16} />
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        {#if selectedNode.type === 'gate'}
                            <div
                                class="rounded-2xl border border-slate-100 bg-slate-50 p-4 font-mono text-[11px] leading-relaxed"
                            >
                                <span class="font-bold text-blue-600">JIKA</span>
                                {#each selectedNode.data.required_facts || [] as req, i}
                                    <span
                                        class="mx-1 rounded border border-slate-200 bg-white px-1.5 py-0.5 text-slate-700 shadow-sm"
                                    >
                                        {factData.find((f) => f.code === req)?.name || req}
                                    </span>
                                    {#if i < (selectedNode.data.required_facts?.length || 0) - 1}
                                        <span class="text-[10px] font-bold text-slate-400">DAN</span
                                        >
                                    {/if}
                                {/each}

                                <br />
                                <span class="mt-2 inline-block font-bold text-emerald-600"
                                    >MAKA</span
                                >

                                {#if selectedNode.data.deduced_facts && selectedNode.data.deduced_facts.length > 0}
                                    <div class="mt-2">
                                        <span class="font-bold text-purple-600">DEDUKSI</span>
                                        {#each selectedNode.data.deduced_facts as ded, i}
                                            <span
                                                class="mx-1 rounded border border-purple-100 bg-purple-50 px-1.5 py-0.5 text-purple-700 shadow-sm"
                                            >
                                                {factData.find((f) => f.code === ded)?.name || ded}
                                            </span>
                                            {#if i < selectedNode.data.deduced_facts.length - 1}
                                                <span class="text-[10px] font-bold text-slate-400"
                                                    >DAN</span
                                                >
                                            {/if}
                                        {/each}
                                    </div>
                                {/if}

                                {#if selectedNode.data.action_id}
                                    <div class="mt-2">
                                        <span class="font-bold text-emerald-600">AKSI</span>
                                        <span
                                            class="mx-1 rounded border border-emerald-200 bg-emerald-50 px-1.5 py-0.5 text-emerald-800 shadow-sm"
                                        >
                                            {actionData.find(
                                                (a) =>
                                                    a.id === selectedNode.data.action_id ||
                                                    a.code === selectedNode.data.action
                                            )?.name || selectedNode.data.action}
                                        </span>
                                    </div>
                                {/if}
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-3">
                                    <p class="mb-1 text-[9px] font-bold text-slate-400 uppercase">
                                        Kode Aturan
                                    </p>
                                    <p class="font-mono text-xs font-black text-slate-900">
                                        {selectedNode.data.rule_code}
                                    </p>
                                </div>
                                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-3">
                                    <p class="mb-1 text-[9px] font-bold text-slate-400 uppercase">
                                        Prioritas
                                    </p>
                                    <p class="text-xs font-black text-slate-900">
                                        Level {selectedNode.data.priority}
                                    </p>
                                </div>
                            </div>
                        {:else}
                            <div
                                class="rounded-2xl border border-slate-100 bg-slate-50 p-4 text-xs leading-relaxed text-slate-600 italic"
                            >
                                {selectedNode.data.description || 'Tidak ada deskripsi tambahan.'}
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        {/if}

        <!-- Zoom Indicator -->
        <div
            class="absolute top-6 left-1/2 -translate-x-1/2 rounded-full border border-white/20 bg-slate-900/10 px-4 py-1.5 text-[10px] font-bold tracking-widest text-slate-600 uppercase backdrop-blur-md"
        >
            Skala {Math.round(zoomLevel * 100)}%
        </div>
    </div>
</div>
