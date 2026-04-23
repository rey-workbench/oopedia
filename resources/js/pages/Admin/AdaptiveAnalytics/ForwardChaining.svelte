<script lang="ts">
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

    let zoom: d3.ZoomBehavior<SVGSVGElement, unknown>;
    let mainGroup: d3.Selection<SVGGElement, unknown, null, undefined>;

    function initSchematic() {
        if (!svgRef || !containerRef || !factData || factData.length === 0) return;

        const width = containerRef.clientWidth;
        const height = isFullscreen ? window.innerHeight - 100 : 750;

        const svg = d3
            .select(svgRef)
            .attr('viewBox', `0 0 ${width} ${height}`)
            .attr('width', width)
            .attr('height', height);

        svg.selectAll('*').remove();

        const defs = svg.append('defs');

        const createMarker = (id: string, color: string) => {
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
        };

        createMarker('arrow-requirement', '#cbd5e1'); // Slate
        createMarker('arrow-deduction', '#60a5fa'); // Blue
        createMarker('arrow-action', '#34d399'); // Emerald

        zoom = d3
            .zoom<SVGSVGElement, unknown>()
            .scaleExtent([0.1, 4])
            .on('zoom', (event: d3.D3ZoomEvent<SVGSVGElement, unknown>) => {
                mainGroup.attr('transform', event.transform.toString());
                zoomLevel = event.transform.k;
            });

        svg.call(zoom);
        svg.on('click', () => {
            selectedNode = null;
            resetD3Flow();
        });

        mainGroup = svg.append('g');

        // 1. Calculate Nodes and Depths (DAG Topo Sort)
        const nodesCalc = new Map();

        // Init Raw Facts (depth 0)
        factData.forEach((f) => {
            if (f.category !== 'deduced') {
                nodesCalc.set(`raw_fact_${f.code}`, {
                    type: 'raw_fact',
                    depth: 0,
                    data: f,
                    id: `f_${f.code}`,
                });
            }
        });

        let changed = true;
        let maxIter = 20; // safe circuit breaker
        while (changed && maxIter > 0) {
            changed = false;
            maxIter--;

            rules.forEach((r) => {
                const requiredFacts = r.required_facts || [];
                let allResolved = true;
                let maxDepth = -1;

                for (const req of requiredFacts) {
                    let source =
                        nodesCalc.get(`raw_fact_${req}`) || nodesCalc.get(`virtual_fact_${req}`);
                    if (!source) {
                        allResolved = false;
                        break;
                    }
                    maxDepth = Math.max(maxDepth, source.depth);
                }

                if (allResolved && !nodesCalc.has(`rule_${r.id}`)) {
                    const ruleDepth = maxDepth + 1;
                    nodesCalc.set(`rule_${r.id}`, {
                        type: 'gate',
                        depth: ruleDepth,
                        data: r,
                        id: `r_${r.id}`,
                    });
                    changed = true;

                    if (r.deduced_facts) {
                        r.deduced_facts.forEach((df: string) => {
                            const vFact = factData.find((f) => f.code === df);
                            if (vFact && !nodesCalc.has(`virtual_fact_${df}`)) {
                                nodesCalc.set(`virtual_fact_${df}`, {
                                    type: 'virtual_fact',
                                    depth: ruleDepth + 1,
                                    data: vFact,
                                    id: `vf_${df}`,
                                });
                            }
                        });
                    }

                    const actionObj = actionData.find((a) => a.id === r.action_id);
                    // Avoid action explicitly marked silent
                    if (actionObj && actionObj.code !== 'H00' && !nodesCalc.has(`action_${r.id}`)) {
                        nodesCalc.set(`action_${r.id}`, {
                            type: 'action',
                            depth: ruleDepth + 1,
                            data: actionObj,
                            id: `a_${r.id}`,
                            sourceRule: r.id,
                        });
                    }
                }
            });
        }

        const nodes = Array.from(nodesCalc.values());

        // 2. Build Links
        const links: any[] = [];
        rules.forEach((r) => {
            const gateNode = nodesCalc.get(`rule_${r.id}`);
            if (!gateNode) return;

            (r.required_facts || []).forEach((req) => {
                const sourceNode =
                    nodesCalc.get(`raw_fact_${req}`) || nodesCalc.get(`virtual_fact_${req}`);
                if (sourceNode) {
                    links.push({ source: sourceNode, target: gateNode, type: 'requirement' });
                }
            });

            if (r.deduced_facts) {
                r.deduced_facts.forEach((df: string) => {
                    const targetNode = nodesCalc.get(`virtual_fact_${df}`);
                    if (targetNode) {
                        links.push({ source: gateNode, target: targetNode, type: 'deduction' });
                    }
                });
            }

            if (nodesCalc.has(`action_${r.id}`)) {
                const targetNode = nodesCalc.get(`action_${r.id}`);
                links.push({ source: gateNode, target: targetNode, type: 'action' });
            }
        });

        // 3. Assign Spatial Coordinates via Custom DAG Layering
        const colWidth = 300;
        const itemHeight = 45;
        const verticalGap = 16;
        const startX = 140;

        const depthGroups: Record<number, any[]> = {};
        nodes.forEach((n: any) => {
            if (!depthGroups[n.depth]) depthGroups[n.depth] = [];
            depthGroups[n.depth]!.push(n);
        });

        const depths = Object.keys(depthGroups)
            .map(Number)
            .sort((a, b) => a - b);

        // Pass 1: Initial sequential Y
        depths.forEach((d) => {
            depthGroups[d]!.forEach((n, i) => (n.y = i * (itemHeight + verticalGap)));
        });

        // Pass 2: Iterative relaxation (Barycenter heuristic)
        for (let iter = 0; iter < 10; iter++) {
            // Forward pass (left to right)
            depths.forEach((d) => {
                if (d === 0) return;
                depthGroups[d]!.forEach((n: any) => {
                    const sources = links.filter((l) => l.target === n).map((l) => l.source);
                    if (sources.length > 0) {
                        n.y = sources.reduce((sum, s) => sum + s.y, 0) / sources.length;
                    }
                });

                depthGroups[d]!.sort((a, b) => a.y - b.y);

                // Pack to resolve overlaps
                if (depthGroups[d]!.length > 0) {
                    let currentY = depthGroups[d]![0].y;
                    depthGroups[d]!.forEach((n: any, i: number) => {
                        if (i > 0) {
                            currentY = Math.max(currentY + itemHeight + verticalGap, n.y);
                        }
                        n.y = currentY;
                    });
                }
            });

            // Backward pass (right to left)
            [...depths].reverse().forEach((d) => {
                if (d === depths[depths.length - 1]) return;
                depthGroups[d]!.forEach((n: any) => {
                    const targets = links.filter((l) => l.source === n).map((l) => l.target);
                    if (targets.length > 0) {
                        n.y = targets.reduce((sum, t) => sum + t.y, 0) / targets.length;
                    }
                });

                depthGroups[d]!.sort((a, b) => a.y - b.y);

                // Pack to resolve overlaps
                if (depthGroups[d]!.length > 0) {
                    let currentY = depthGroups[d]![0].y;
                    depthGroups[d]!.forEach((n: any, i: number) => {
                        if (i > 0) {
                            currentY = Math.max(currentY + itemHeight + verticalGap, n.y);
                        }
                        n.y = currentY;
                    });
                }
            });
        }

        // 4. Center layers and apply X
        let minY = Infinity,
            maxY = -Infinity;
        let maxX = -Infinity;

        const targetCenterY = Math.max(700, height) / 2;

        depths.forEach((d) => {
            const group = depthGroups[d] ?? [];
            if (group.length === 0) return;
            const groupCenterY = (group[0].y + group[group.length - 1].y + itemHeight) / 2;
            const offset = targetCenterY - groupCenterY;

            group.forEach((n: any) => {
                n.x = startX + n.depth * colWidth;
                n.y += offset;

                if (n.y < minY) minY = n.y;
                if (n.y > maxY) maxY = n.y;
                if (n.x > maxX) maxX = n.x;
            });
        });

        // 4. DRAWING

        resetD3Flow = () => {
            mainGroup
                .selectAll('.link')
                .classed('flow-active', false)
                .attr('stroke-opacity', 0.6)
                .attr('stroke-width', 2.5);
            mainGroup
                .selectAll('.rect-node, .gate-node')
                .attr('opacity', 1)
                .classed('opacity-100', true);
        };

        function highlightFlow(clickedNode: any) {
            resetD3Flow();

            const activeLinks = new Set();
            const activeNodes = new Set([clickedNode]);

            // Traverse downstream (outcomes)
            function traverseDownstream(node: any) {
                links
                    .filter((l) => l.source === node)
                    .forEach((l) => {
                        activeLinks.add(l);
                        activeNodes.add(l.target);
                        traverseDownstream(l.target);
                    });
            }

            // Traverse upstream (prerequisites)
            function traverseUpstream(node: any) {
                links
                    .filter((l) => l.target === node)
                    .forEach((l) => {
                        activeLinks.add(l);
                        activeNodes.add(l.source);
                        traverseUpstream(l.source);
                    });
            }

            traverseDownstream(clickedNode);
            traverseUpstream(clickedNode);

            mainGroup
                .selectAll('.rect-node, .gate-node')
                .attr('opacity', (n: any) => (activeNodes.has(n) ? 1 : 0.2));

            mainGroup
                .selectAll('.link')
                .attr('stroke-opacity', (l: any) => (activeLinks.has(l) ? 1 : 0.1))
                .attr('stroke-width', (l: any) => (activeLinks.has(l) ? 3.5 : 1.5))
                .classed('flow-active', (l: any) => activeLinks.has(l));
        }

        mainGroup
            .selectAll('.link')
            .data(links)
            .enter()
            .append('path')
            .attr('class', 'link')
            .attr('d', (d: any) => {
                const sx = d.source.x + (d.source.type === 'gate' ? 22 : 90);
                const sy = d.source.y;
                const tx = d.target.x - (d.target.type === 'gate' ? 22 : 90);
                const ty = d.target.y;
                return `M${sx},${sy}C${(sx + tx) / 2},${sy} ${(sx + tx) / 2},${ty} ${tx},${ty}`;
            })
            .attr('fill', 'none')
            .attr('stroke', (d) => {
                if (d.type === 'deduction') return '#60a5fa'; // Blue
                if (d.type === 'action') return '#34d399'; // Emerald
                return '#cbd5e1'; // Slate (requirement)
            })
            .attr('stroke-width', 2.5)
            .attr('stroke-opacity', 0.6)
            .attr('marker-end', (d) => {
                if (d.type === 'deduction') return 'url(#arrow-deduction)';
                if (d.type === 'action') return 'url(#arrow-action)';
                return 'url(#arrow-requirement)';
            });

        // Render Rectangular Nodes (Facts, Virtual Facts, Actions)
        const rectNodes = nodes.filter((n) => n.type !== 'gate');
        const rectG = mainGroup
            .selectAll('.rect-node')
            .data(rectNodes)
            .enter()
            .append('g')
            .attr('transform', (d) => `translate(${d.x},${d.y})`)
            .attr(
                'class',
                'rect-node cursor-pointer outline-none select-none transition-opacity duration-300'
            )
            .on('click', (event, d) => {
                selectedNode = d;
                highlightFlow(d);
                event.stopPropagation();
            });

        rectG
            .append('rect')
            .attr('width', 180)
            .attr('height', 36)
            .attr('x', -90)
            .attr('y', -18)
            .attr('fill', (d) => {
                if (d.type === 'virtual_fact') return '#eff6ff'; // pale blue
                if (d.type === 'action') return '#059669'; // deep emerald
                return '#ffffff';
            })
            .attr('stroke', (d) => {
                if (d.type === 'virtual_fact') return '#3b82f6';
                if (d.type === 'action') return '#047857';
                return '#e2e8f0';
            })
            .attr('stroke-width', 1.5)
            .attr('rx', (d) => (d.type === 'action' ? 18 : 6)); // More rounded for action terminals

        rectG
            .append('text')
            .attr('text-anchor', 'middle')
            .attr('dominant-baseline', 'middle')
            .attr(
                'class',
                (d) =>
                    `text-[10px] font-bold ${d.type === 'action' ? 'fill-white' : d.type === 'virtual_fact' ? 'fill-blue-700' : 'fill-slate-700'}`
            )
            .text((d) => d.data.name);

        // Render Gate Nodes (AND)
        const gateNodes = nodes.filter((n) => n.type === 'gate');
        const gateG = mainGroup
            .selectAll('.gate-node')
            .data(gateNodes)
            .enter()
            .append('g')
            .attr('transform', (d) => `translate(${d.x},${d.y})`)
            .attr('class', 'gate-node cursor-pointer select-none transition-opacity duration-300')
            .on('click', (event, d) => {
                selectedNode = d;
                highlightFlow(d);
                event.stopPropagation();
            });

        gateG
            .append('circle')
            .attr('r', 20)
            .attr('fill', '#f8fafc')
            .attr('stroke', '#64748b')
            .attr('stroke-width', 2);

        gateG
            .append('text')
            .attr('text-anchor', 'middle')
            .attr('dominant-baseline', 'middle')
            .attr('class', 'text-[9px] font-black fill-slate-800 tracking-tighter')
            .text('AND');

        gateG
            .append('text')
            .attr('text-anchor', 'middle')
            .attr('y', 32)
            .attr('class', 'text-[8px] font-black fill-slate-400 uppercase tracking-widest')
            .text((d) => d.data.id);

        // Center on start, fit graph width
        const totalWidth = maxX + 240;
        const totalHeight = Math.max(height, maxY - minY + 200);
        const initialScale = Math.min(0.85, width / totalWidth);
        const yOffset = (height - totalHeight * initialScale) / 2;

        // Center view on the graph content
        svg.call(
            zoom.transform,
            d3.zoomIdentity
                .translate(80, Math.max(50, -minY * initialScale + yOffset))
                .scale(initialScale)
        );
    }

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
        return () => window.removeEventListener('resize', initSchematic);
    });

    $effect(() => {
        if (analyticsState.rulesByDomain.length > 0 || analyticsState.allFacts.length > 0) {
            untrack(() => initSchematic());
        }
    });

    function toggleFullscreen() {
        isFullscreen = !isFullscreen;
        setTimeout(initSchematic, 100);
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
        animation: flow 0.5s linear infinite;
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

<!-- svelte-ignore element_invalid_self_closing_tag -->
<reference types="d3"></reference>

<div
    class="relative w-full overflow-hidden bg-white {isFullscreen
        ? 'fixed inset-0 z-50'
        : 'h-[750px] rounded-3xl border-2 border-slate-100 shadow-xl'}"
    bind:this={containerRef}
>
    <!-- Header -->
    <div
        class="pointer-events-none absolute top-6 right-6 left-6 z-10 flex items-center justify-between"
    >
        <div
            class="pointer-events-auto flex items-center gap-4 rounded-2xl border border-slate-200 bg-white/80 p-3 px-5 shadow-lg backdrop-blur-xl"
        >
            <div class="rounded-xl bg-slate-900 p-2.5 text-white shadow-inner">
                <Brain size={22} />
            </div>
            <div>
                <h3 class="mb-1 text-lg leading-none font-black tracking-tight text-slate-900">
                    Mesin Inferensi
                </h3>
                <div class="flex items-center gap-2">
                    <div class="h-1.5 w-1.5 animate-pulse rounded-full bg-blue-500"></div>
                    <p
                        class="text-[10px] font-bold tracking-widest text-slate-500 uppercase text-shadow-sm"
                    >
                        Diagram Skematik Forward Chaining
                    </p>
                </div>
            </div>
        </div>

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
                                    <span class="mx-1 font-bold text-purple-600">DEDUKSI</span>
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
                                {:else}
                                    <span
                                        class="mx-1 mt-2 inline-block rounded border border-emerald-200 bg-emerald-50 px-1.5 py-0.5 text-emerald-800 shadow-sm"
                                    >
                                        AKSI: {actionData.find(
                                            (a) =>
                                                a.id === selectedNode.data.action_id ||
                                                a.code === selectedNode.data.action
                                        )?.name || selectedNode.data.action}
                                    </span>
                                {/if}
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-3">
                                    <p class="mb-1 text-[9px] font-bold text-slate-400 uppercase">
                                        Kode Aturan
                                    </p>
                                    <p class="font-mono text-xs font-black text-slate-900">
                                        {selectedNode.data.id}
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
