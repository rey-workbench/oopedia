<script lang="ts">
    /// <reference types="d3" />
    import { onMount, untrack } from 'svelte';
    import { fade, fly } from 'svelte/transition';
    import * as d3 from 'd3';
    import type { AdaptiveRuleState } from '@/states/Admin/AdaptiveRuleState.svelte';
    import Button from '@/components/ui/Button.svelte';
    import {
        Brain,
        Maximize2,
        Minimize2,
        RefreshCw,
        GitBranch,
        Target,
        X,
        Pencil,
        PlusCircle,
        Zap,
        Trophy,
        Info,
    } from 'lucide-svelte';

    interface Props {
        analyticsState: AdaptiveRuleState;
        onedit?: (rule: any) => void;
        oneditaction?: (action: any) => void;
        isFullscreen?: boolean;
        fullscreenTarget?: HTMLElement | null;
    }

    let { 
        analyticsState, 
        onedit, 
        oneditaction, 
        isFullscreen = $bindable(false),
        fullscreenTarget = null 
    }: Props = $props();

    let svgRef = $state<SVGSVGElement | null>(null);
    let containerRef = $state<HTMLDivElement | null>(null);
    let selectedNode = $state<any>(null);
    let zoomLevel = $state(0.8);
    let resetD3Flow = () => {};

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
                const resolvedSources = prerequisites.map(
                    (p) =>
                        nodeRegistry.get(`raw_fact_${p}`) || nodeRegistry.get(`virtual_fact_${p}`)
                );

                const isResolved = resolvedSources.every((s) => !!s);
                if (!isResolved) return;

                const maxPrereqDepth = Math.max(...resolvedSources.map((s) => s.depth));
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
        const action = actionData.find(
            (a) => a.id === rule.action_id || a.code === rule.action_code
        );
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
                const source =
                    nodeRegistry.get(`raw_fact_${code}`) ||
                    nodeRegistry.get(`virtual_fact_${code}`);
                if (source) links.push({ source, target: gateNode, type: 'requirement' });
            });

            // Link Gate -> Deduced Facts
            (rule.deduced_facts || []).forEach((code: string) => {
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

        const depths = Object.keys(depthGroups)
            .map(Number)
            .sort((a, b) => a - b);

        // Pass 1: Initial Layout
        depths.forEach((d) => {
            depthGroups[d]!.forEach(
                (n, i) => (n.y = i * (GRAPH_CONFIG.itemHeight + GRAPH_CONFIG.verticalGap))
            );
        });

        // Pass 2: Iterative Barycenter Optimization
        for (let i = 0; i < GRAPH_CONFIG.barycenterIterations; i++) {
            optimizeLayoutLayer(depths, depthGroups, links, true);
            optimizeLayoutLayer([...depths].reverse(), depthGroups, links, false);
        }

        centerAndProject(depths, depthGroups, containerHeight);
    }

    function optimizeLayoutLayer(
        depths: number[],
        groups: Record<number, any[]>,
        links: any[],
        forward: boolean
    ) {
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
                currentY = Math.max(
                    currentY + GRAPH_CONFIG.itemHeight + GRAPH_CONFIG.verticalGap,
                    node.y
                );
            }
            node.y = currentY;
        });
    }

    function centerAndProject(depths: number[], groups: Record<number, any[]>, height: number) {
        const centerRefY = height / 2;
        let maxX = 0;
        let minY = Infinity,
            maxY = -Infinity;

        depths.forEach((d) => {
            const group = groups[d]!;
            if (group.length === 0) return;
            const groupCenterY =
                (group[0].y + group[group.length - 1].y + GRAPH_CONFIG.itemHeight) / 2;
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
        const maxX = Math.max(...nodes.map((n) => n.x), 100);
        const minY = Math.min(...nodes.map((n) => n.y), 0);
        const maxY = Math.max(...nodes.map((n) => n.y), 100);

        drawLinks(links);
        drawNodes(nodes);

        const initialScale = Math.min(GRAPH_CONFIG.initialScale, width / (maxX + 280));

        if (!isInitialized) {
            d3.select(svgRef!).call(
                zoom.transform,
                d3.zoomIdentity
                    .translate(
                        width / 2 - (maxX / 2) * initialScale,
                        height / 2 - ((maxY + minY) / 2) * initialScale
                    )
                    .scale(initialScale)
            );
            isInitialized = true;
        }
    }

    function updateLinkPaths() {
        mainGroup.selectAll('.link').attr('d', generateBezierPath);
    }

    function dragstarted(this: any) {
        d3.select(this).raise();
    }

    function dragged(this: any, event: any, d: any) {
        d.x = event.x;
        d.y = event.y;
        d3.select(this).attr('transform', `translate(${d.x},${d.y})`);
        updateLinkPaths();
    }

    function dragended(this: any) {
        // Optional: save positions or snap to grid
    }

    const drag = d3.drag()
        .on('start', dragstarted)
        .on('drag', dragged)
        .on('end', dragended);

    function drawLinks(links: any[]) {
        mainGroup
            .selectAll('.link')
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
        return `M${sx},${sy}C${sx + dx / 2},${sy} ${sx + dx / 2},${ty} ${tx},${ty}`;
    }

    function getLinkColor(type: string) {
        const colors = { deduction: '#3b82f6', action: '#10b981', requirement: '#94a3b8' };
        return colors[type as keyof typeof colors] || '#94a3b8';
    }

    function drawNodes(nodes: any[]) {
        const rectNodes = nodes.filter((n) => n.type !== 'gate');
        const gateNodes = nodes.filter((n) => n.type === 'gate');

        const nodeSelection = mainGroup
            .selectAll('.rect-node')
            .data(rectNodes)
            .enter()
            .append('g')
            .attr('transform', (d) => `translate(${d.x},${d.y})`)
            .attr('class', 'rect-node cursor-grab active:cursor-grabbing select-none')
            .call(drag as any)
            .on('click', (event, d) => handleNodeClick(d, event))
            .on('contextmenu', (event, d) => handleNodeContextMenu(d, event));

        nodeSelection
            .append('rect')
            .attr('width', 200)
            .attr('height', 40)
            .attr('x', -100)
            .attr('y', -20)
            .attr('rx', (d) => (d.type === 'action' ? 20 : 12))
            .attr('fill', (d) => getNodeFill(d.type))
            .attr('stroke', (d) => getNodeStroke(d.type))
            .attr('stroke-width', 2)
            .attr('class', 'shadow-sm transition-all duration-300')
            .style('opacity', 0)
            .transition()
            .duration(500)
            .style('opacity', 1);

        nodeSelection
            .append('text')
            .attr('text-anchor', 'middle')
            .attr('dominant-baseline', 'middle')
            .attr(
                'class',
                (d) =>
                    `text-[11px] font-bold ${d.type === 'action' ? 'fill-white' : 'fill-slate-800'}`
            )
            .text((d) => d.data.name);

        const gateSelection = mainGroup
            .selectAll('.gate-node')
            .data(gateNodes)
            .enter()
            .append('g')
            .attr('transform', (d) => `translate(${d.x},${d.y})`)
            .attr('class', 'gate-node cursor-grab active:cursor-grabbing select-none')
            .call(drag as any)
            .on('click', (event, d) => handleNodeClick(d, event))
            .on('contextmenu', (event, d) => handleNodeContextMenu(d, event));

        gateSelection
            .append('circle')
            .attr('r', 24)
            .attr('fill', '#0f172a')
            .attr('stroke', '#334155')
            .attr('stroke-width', 2)
            .style('opacity', 0)
            .transition()
            .duration(500)
            .style('opacity', 1);
        gateSelection
            .append('text')
            .attr('text-anchor', 'middle')
            .attr('dominant-baseline', 'middle')
            .attr('class', 'text-[10px] font-bold fill-white antialiased')
            .text('AND');
        gateSelection
            .append('text')
            .attr('text-anchor', 'middle')
            .attr('y', 38)
            .attr('class', 'text-[9px] font-medium fill-slate-400 uppercase tracking-wider')
            .text((d) => d.data.code);
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

    function handleNodeContextMenu(d: any, event: MouseEvent) {
        event.preventDefault();
        if (d.type === 'gate' && onedit) {
            onedit(d.data);
        } else if (d.type === 'action' && oneditaction) {
            oneditaction(d.data);
        }
        event.stopPropagation();
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
            .attr('opacity', (n: any) => (activeNodes.has(n) ? 1 : 0.15));
        mainGroup
            .selectAll('.link')
            .attr('stroke-opacity', (l: any) => (activeLinks.has(l) ? 1 : 0.05))
            .attr('stroke-width', (l: any) => (activeLinks.has(l) ? 4 : 1.5))
            .classed('flow-active', (l: any) => activeLinks.has(l));
    }

    resetD3Flow = () => {
        mainGroup
            .selectAll('.link')
            .classed('flow-active', false)
            .attr('stroke-opacity', 0.4)
            .attr('stroke-width', 2);
        mainGroup.selectAll('.rect-node, .gate-node').attr('opacity', 1);
    };

    function resetView() {
        if (!svgRef || !zoom) return;
        d3.select(svgRef)
            .transition()
            .duration(750)
            .call(zoom.transform, d3.zoomIdentity.translate(80, 50).scale(0.8));
    }

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
        if (analyticsState.rulesByDomain.length > 0 || analyticsState.allFacts.length > 0) {
            untrack(() => initSchematic());
        }
    });

    async function toggleFullscreen() {
        const target = fullscreenTarget || containerRef;
        if (!target) return;
        if (!document.fullscreenElement) await target.requestFullscreen();
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
    <!-- Header -->
    {#if !isFullscreen}
        <div
            class="pointer-events-none absolute top-6 right-6 left-6 z-10 flex items-center justify-end"
            transition:fade
        >
            <div class="pointer-events-auto flex items-center gap-2">
                {#if onedit}
                    <Button
                        variant="primary"
                        size="sm"
                        icon={PlusCircle}
                        onclick={() => onedit(null)}
                        class="mr-2 shadow-xl shadow-primary-900/10"
                    >
                        TAMBAH ATURAN
                    </Button>
                {/if}
                <div
                    class="flex items-center gap-1 rounded-xl border border-slate-200 bg-white/80 p-1.5 shadow-lg backdrop-blur-xl"
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
    {/if}

    <!-- Tree Canvas -->
    <div class="relative h-full w-full bg-slate-50/50">
        <div
            class="pointer-events-none absolute inset-0 opacity-[0.03]"
            style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 24px 24px;"
        ></div>

        <svg 
            bind:this={svgRef} 
            class="group block h-full w-full touch-none"
            oncontextmenu={(e) => e.preventDefault()}
            onclick={() => (selectedNode = null)}
            role="application"
            aria-label="Workflow Canvas"
        ></svg>

        <!-- Palette Sidebar (n8n Style) -->
        <div class="pointer-events-none absolute left-8 top-1/2 -translate-y-1/2 z-10 flex flex-col gap-6">
            <div class="pointer-events-auto flex flex-col gap-4">
                <div 
                    class="group relative flex flex-col items-center gap-2 p-4 rounded-3xl bg-white/90 border border-slate-200 shadow-xl cursor-grab active:cursor-grabbing hover:border-primary-400 transition-all hover:shadow-primary-900/10 backdrop-blur-xl focus:outline-none focus:ring-2 focus:ring-primary-500"
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
                >
                    <div class="p-3 rounded-2xl bg-primary-50 text-primary-600 group-hover:scale-110 transition-transform shadow-sm">
                        <Zap size={28} />
                    </div>
                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-tighter">New Rule</span>
                    
                    <!-- Tooltip -->
                    <div class="absolute left-full ml-4 px-3 py-1.5 bg-slate-900 text-white text-[10px] font-bold rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                        Tarik ke kanvas untuk buat Aturan
                    </div>
                </div>

                <div 
                    class="group relative flex flex-col items-center gap-2 p-4 rounded-3xl bg-white/90 border border-slate-200 shadow-xl cursor-grab active:cursor-grabbing hover:border-emerald-400 transition-all hover:shadow-emerald-900/10 backdrop-blur-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
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
                >
                    <div class="p-3 rounded-2xl bg-emerald-50 text-emerald-600 group-hover:scale-110 transition-transform shadow-sm">
                        <Target size={28} />
                    </div>
                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-tighter">New Action</span>
                    
                    <!-- Tooltip -->
                    <div class="absolute left-full ml-4 px-3 py-1.5 bg-slate-900 text-white text-[10px] font-bold rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
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
        {#if selectedNode}
            <!-- svelte-ignore a11y_click_events_have_key_events -->
            <!-- svelte-ignore a11y_no_static_element_interactions -->
            <div 
                class="absolute inset-0 z-20 bg-slate-900/10 backdrop-blur-[2px]"
                onclick={() => (selectedNode = null)}
                transition:fade={{ duration: 200 }}
            ></div>

            <div 
                class="absolute inset-y-0 right-0 z-150 flex w-full max-w-sm flex-col border-l border-slate-200 bg-white shadow-2xl"
                transition:fly={{ x: 400, duration: 300 }}
            >
                <!-- Drawer Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="rounded-xl p-2 {selectedNode.type === 'gate' ? 'bg-primary-50 text-primary-600' : 'bg-emerald-50 text-emerald-600'}">
                            {#if selectedNode.type === 'gate'}
                                <GitBranch size={20} />
                            {:else if selectedNode.type === 'action'}
                                <Target size={20} />
                            {:else}
                                <Brain size={20} />
                            {/if}
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800">
                                {selectedNode.type === 'gate' ? 'Detail Aturan' : selectedNode.type === 'action' ? 'Detail Aksi' : 'Detail Fakta'}
                            </h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Node Configuration</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        {#if selectedNode.type === 'gate' && onedit}
                            <button
                                onclick={() => onedit(selectedNode.data)}
                                class="rounded-xl p-2 text-primary-600 transition-colors hover:bg-primary-50"
                                title="Ubah Aturan"
                            >
                                <Pencil size={18} />
                            </button>
                        {/if}
                        <button
                            onclick={() => {
                                selectedNode = null;
                                resetD3Flow();
                            }}
                            class="rounded-xl p-2 text-slate-400 transition-colors hover:bg-slate-50 hover:text-slate-600"
                        >
                            <X size={20} />
                        </button>
                    </div>
                </div>

                <!-- Drawer Body -->
                <div class="custom-scrollbar flex-1 overflow-y-auto p-6 space-y-6">
                    {#if selectedNode.type === 'gate'}
                        <section class="space-y-4">
                            <div class="flex items-center gap-2 text-[10px] font-black text-blue-500 uppercase tracking-widest">
                                <div class="h-px flex-1 bg-blue-100"></div>
                                <span>Logic Flow</span>
                                <div class="h-px w-4 bg-blue-100"></div>
                            </div>
                            
                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 font-mono text-[11px] leading-relaxed">
                                <div class="mb-3">
                                    <span class="font-bold text-blue-600">IF (Triggers)</span>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        {#each selectedNode.data.required_facts || [] as req}
                                            <span class="rounded border border-slate-200 bg-white px-2 py-1 text-slate-700 shadow-sm">
                                                {factData.find((f) => f.code === req)?.name || req}
                                            </span>
                                        {/each}
                                    </div>
                                </div>

                                {#if selectedNode.data.deduced_facts?.length > 0}
                                    <div class="mb-3">
                                        <span class="font-bold text-purple-600">THEN (Deduce)</span>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            {#each selectedNode.data.deduced_facts as ded}
                                                <span class="rounded border border-purple-100 bg-purple-50 px-2 py-1 text-purple-700 shadow-sm">
                                                    {factData.find((f) => f.code === ded)?.name || ded}
                                                </span>
                                            {/each}
                                        </div>
                                    </div>
                                {/if}

                                {#if selectedNode.data.action_id || selectedNode.data.action}
                                    {#if actionData.find(a => a.id === selectedNode.data.action_id || a.code === selectedNode.data.action)}
                                        {@const action = actionData.find(a => a.id === selectedNode.data.action_id || a.code === selectedNode.data.action)}
                                        <div>
                                            <span class="font-bold text-emerald-600">THEN (Action)</span>
                                            <div class="mt-2 rounded-xl border border-emerald-100 bg-emerald-50/50 p-3">
                                                <p class="text-xs font-bold text-emerald-900">{action?.name || selectedNode.data.action}</p>
                                                <p class="mt-1 text-[10px] text-emerald-700/80 leading-relaxed">{action?.description || ''}</p>
                                            </div>
                                        </div>
                                    {/if}
                                {/if}
                            </div>
                        </section>

                        <section class="space-y-4">
                            <div class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                <div class="h-px flex-1 bg-slate-100"></div>
                                <span>Metadata</span>
                                <div class="h-px w-4 bg-slate-100"></div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-2xl border border-slate-100 bg-white p-3 shadow-sm">
                                    <p class="mb-1 text-[9px] font-bold text-slate-400 uppercase">Kode</p>
                                    <p class="font-mono text-xs font-black text-slate-900">{selectedNode.data.code}</p>
                                </div>
                                <div class="rounded-2xl border border-slate-100 bg-white p-3 shadow-sm">
                                    <p class="mb-1 text-[9px] font-bold text-slate-400 uppercase">Prioritas</p>
                                    <p class="text-xs font-black text-slate-900">Level {selectedNode.data.priority}</p>
                                </div>
                            </div>

                            {#if selectedNode.data.description}
                                <div class="rounded-2xl border border-blue-50 bg-blue-50/30 p-4">
                                    <p class="text-[11px] leading-relaxed text-slate-600 italic">
                                        "{selectedNode.data.description}"
                                    </p>
                                </div>
                            {/if}
                        </section>
                    {:else}
                        <div class="rounded-3xl border border-slate-100 bg-slate-50 p-6 text-center">
                            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm">
                                <Brain size={32} class="text-slate-400" />
                            </div>
                            <h4 class="mb-2 text-sm font-black text-slate-800">{selectedNode.data.name}</h4>
                            <p class="text-xs leading-relaxed text-slate-500 italic">
                                {selectedNode.data.description || 'Tidak ada deskripsi tambahan untuk node ini.'}
                            </p>
                            {#if selectedNode.data.variant}
                                <div class="mt-4 flex justify-center">
                                    <span class="rounded-full bg-slate-200 px-3 py-1 text-[10px] font-black text-slate-700 uppercase tracking-widest">
                                        {selectedNode.data.variant}
                                    </span>
                                </div>
                            {/if}
                        </div>
                    {/if}

                    {#if selectedNode.type === 'gate' || selectedNode.type === 'action'}
                         <!-- Preview Section -->
                         <section class="space-y-4">
                            <div class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                <div class="h-px flex-1 bg-slate-100"></div>
                                <span>Preview Tampilan</span>
                                <div class="h-px w-4 bg-slate-100"></div>
                            </div>
                            <div class="overflow-hidden rounded-2xl border border-slate-100 bg-slate-50 p-4 shadow-inner">
                                {#if (selectedNode.type === 'gate' && actionData.find(a => a.id === selectedNode.data.action_id || a.code === selectedNode.data.action)?.code) || selectedNode.type === 'action'}
                                    {@const actionCode = selectedNode.type === 'gate' 
                                        ? actionData.find(a => a.id === selectedNode.data.action_id || a.code === selectedNode.data.action)?.code 
                                        : selectedNode.data.code}
                                    
                                    {#if actionCode === 'INCREASE_DIFF' || actionCode === 'H01'}
                                        <div class="flex items-center gap-3 rounded-xl bg-blue-600 p-3 text-white shadow-lg">
                                            <div class="rounded-full bg-white/20 p-1.5"><Target size={14} /></div>
                                            <div class="leading-tight">
                                                <p class="text-xs font-bold">Level Up!</p>
                                                <p class="text-[10px] opacity-80">Tantangan baru tersedia untukmu.</p>
                                            </div>
                                        </div>
                                    {:else if actionCode === 'CERTIFICATION' || actionCode === 'H06'}
                                        <div class="flex items-center gap-3 rounded-xl bg-amber-500 p-3 text-white shadow-lg">
                                            <div class="rounded-full bg-white/20 p-1.5"><Trophy size={14} /></div>
                                            <div class="leading-tight">
                                                <p class="text-xs font-bold">Selamat!</p>
                                                <p class="text-[10px] opacity-80">Kamu meraih sertifikat baru.</p>
                                            </div>
                                        </div>
                                    {:else}
                                        <div class="flex items-center gap-3 rounded-xl bg-slate-800 p-3 text-white shadow-lg">
                                            <div class="rounded-full bg-white/20 p-1.5"><Info size={14} /></div>
                                            <div class="leading-tight">
                                                <p class="text-xs font-bold">Feedback Sistem</p>
                                                <p class="text-[10px] opacity-80">Pertahankan ritme belajarmu.</p>
                                            </div>
                                        </div>
                                    {/if}
                                {/if}
                            </div>
                        </section>
                    {/if}
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
