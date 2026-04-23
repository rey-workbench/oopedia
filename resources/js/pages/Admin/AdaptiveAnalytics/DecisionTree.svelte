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
        GitBranch,
        Target,
        X
    } from 'lucide-svelte';

    interface Props {
        analyticsState: AdaptiveAnalyticsState;
    }

    let { analyticsState }: Props = $props();

    let svgRef = $state<SVGSVGElement | null>(null);
    let containerRef = $state<HTMLDivElement | null>(null);
    let isFullscreen = $state(false);
    let selectedNode = $state<any>(null);
    let zoomLevel = $state(0.8);

    const factData = $derived(analyticsState.allFacts);
    const actionData = $derived(analyticsState.allActions);
    const rules = $derived(analyticsState.rulesByDomain.flatMap(d => d.rules));

    let zoom: d3.ZoomBehavior<SVGSVGElement, unknown>;
    let mainGroup: d3.Selection<SVGGElement, unknown, null, undefined>;

    function initSchematic() {
        if (!svgRef || !containerRef || !factData || factData.length === 0) return;

        const width = containerRef.clientWidth;
        const height = isFullscreen ? window.innerHeight - 100 : 750;

        const svg = d3.select(svgRef)
            .attr("viewBox", `0 0 ${width} ${height}`)
            .attr("width", width)
            .attr("height", height);

        svg.selectAll("*").remove();

        svg.append("defs").append("marker")
            .attr("id", "arrowhead")
            .attr("viewBox", "0 -5 10 10")
            .attr("refX", 10)
            .attr("refY", 0)
            .attr("markerWidth", 5)
            .attr("markerHeight", 5)
            .attr("orient", "auto")
            .append("path")
            .attr("d", "M0,-5L10,0L0,5")
            .attr("fill", "#cbd5e1");

        zoom = d3.zoom<SVGSVGElement, unknown>()
            .scaleExtent([0.1, 4])
            .on("zoom", (event: d3.D3ZoomEvent<SVGSVGElement, unknown>) => {
                mainGroup.attr("transform", event.transform.toString());
                zoomLevel = event.transform.k;
            });

        svg.call(zoom);

        mainGroup = svg.append("g");

        // 1. Calculate Nodes and Depths (DAG Topo Sort)
        const nodesCalc = new Map();
        
        // Init Raw Facts (depth 0)
        factData.forEach(f => {
            if (f.category !== 'deduced') {
                nodesCalc.set(`raw_fact_${f.code}`, { type: 'raw_fact', depth: 0, data: f, id: `f_${f.code}` });
            }
        });

        let changed = true;
        let maxIter = 20; // safe circuit breaker
        while(changed && maxIter > 0) {
            changed = false;
            maxIter--;
            
            rules.forEach(r => {
                const requiredFacts = r.required_facts || [];
                let allResolved = true;
                let maxDepth = -1;
                
                for (const req of requiredFacts) {
                    let source = nodesCalc.get(`raw_fact_${req}`) || nodesCalc.get(`virtual_fact_${req}`);
                    if (!source) {
                        allResolved = false;
                        break;
                    }
                    maxDepth = Math.max(maxDepth, source.depth);
                }
                
                if (allResolved && !nodesCalc.has(`rule_${r.id}`)) {
                    const ruleDepth = maxDepth + 1;
                    nodesCalc.set(`rule_${r.id}`, { type: 'gate', depth: ruleDepth, data: r, id: `r_${r.id}` });
                    changed = true;
                    
                    if (r.deduced_facts) {
                        r.deduced_facts.forEach((df: string) => {
                            const vFact = factData.find(f => f.code === df);
                            if (vFact && !nodesCalc.has(`virtual_fact_${df}`)) {
                                nodesCalc.set(`virtual_fact_${df}`, { type: 'virtual_fact', depth: ruleDepth + 1, data: vFact, id: `vf_${df}` });
                            }
                        });
                    }
                    
                    const actionObj = actionData.find(a => a.id === r.action_id);
                    // Avoid action explicitly marked silent
                    if (actionObj && actionObj.code !== 'H00' && !nodesCalc.has(`action_${r.id}`)) {
                        nodesCalc.set(`action_${r.id}`, { type: 'action', depth: ruleDepth + 1, data: actionObj, id: `a_${r.id}`, sourceRule: r.id });
                    }
                }
            });
        }
        
        const nodes = Array.from(nodesCalc.values());
        
        // 2. Build Links
        const links: any[] = [];
        rules.forEach(r => {
            const gateNode = nodesCalc.get(`rule_${r.id}`);
            if (!gateNode) return;
            
            (r.required_facts || []).forEach(req => {
                const sourceNode = nodesCalc.get(`raw_fact_${req}`) || nodesCalc.get(`virtual_fact_${req}`);
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
            depthGroups[n.depth].push(n);
        });

        const depths = Object.keys(depthGroups).map(Number).sort((a, b) => a - b);
        
        // Pass 1: Initial sequential Y 
        depths.forEach(d => {
            depthGroups[d].forEach((n, i) => n.y = i * (itemHeight + verticalGap));
        });

        // Pass 2: Iterative relaxation (Barycenter heuristic)
        for (let iter = 0; iter < 10; iter++) {
            // Forward pass (left to right)
            depths.forEach(d => {
                if (d === 0) return;
                depthGroups[d].forEach((n: any) => {
                    const sources = links.filter(l => l.target === n).map(l => l.source);
                    if (sources.length > 0) {
                        n.y = sources.reduce((sum, s) => sum + s.y, 0) / sources.length;
                    }
                });
                
                depthGroups[d].sort((a, b) => a.y - b.y);
                
                // Pack to resolve overlaps
                if (depthGroups[d].length > 0) {
                    let currentY = depthGroups[d][0].y;
                    depthGroups[d].forEach((n: any, i: number) => {
                        if (i > 0) {
                            currentY = Math.max(currentY + itemHeight + verticalGap, n.y);
                        }
                        n.y = currentY;
                    });
                }
            });

            // Backward pass (right to left)
            [...depths].reverse().forEach(d => {
                if (d === depths[depths.length - 1]) return;
                depthGroups[d].forEach((n: any) => {
                    const targets = links.filter(l => l.source === n).map(l => l.target);
                    if (targets.length > 0) {
                        n.y = targets.reduce((sum, t) => sum + t.y, 0) / targets.length;
                    }
                });
                
                depthGroups[d].sort((a, b) => a.y - b.y);
                
                // Pack to resolve overlaps
                if (depthGroups[d].length > 0) {
                    let currentY = depthGroups[d][0].y;
                    depthGroups[d].forEach((n: any, i: number) => {
                        if (i > 0) {
                            currentY = Math.max(currentY + itemHeight + verticalGap, n.y);
                        }
                        n.y = currentY;
                    });
                }
            });
        }

        // 4. Center layers and apply X
        let minY = Infinity, maxY = -Infinity;
        let maxX = -Infinity;

        const targetCenterY = Math.max(700, height) / 2;

        depths.forEach(d => {
            const group = depthGroups[d];
            if (group.length === 0) return;
            const groupCenterY = (group[0].y + group[group.length - 1].y + itemHeight) / 2;
            const offset = targetCenterY - groupCenterY;
            
            group.forEach((n: any) => {
                n.x = startX + (n.depth * colWidth);
                n.y += offset;
                
                if (n.y < minY) minY = n.y;
                if (n.y > maxY) maxY = n.y;
                if (n.x > maxX) maxX = n.x;
            });
        });

        // 4. DRAWING

        mainGroup.selectAll(".link")
            .data(links)
            .enter()
            .append("path")
            .attr("class", "link")
            .attr("d", (d: any) => {
                const sx = d.source.x + (d.source.type === 'gate' ? 22 : 90);
                const sy = d.source.y;
                const tx = d.target.x - (d.target.type === 'gate' ? 22 : 90);
                const ty = d.target.y;
                return `M${sx},${sy}C${(sx+tx)/2},${sy} ${(sx+tx)/2},${ty} ${tx},${ty}`;
            })
            .attr("fill", "none")
            .attr("stroke", d => {
                if(d.type === 'deduction') return '#60a5fa'; // Blue
                if(d.type === 'action') return '#34d399'; // Emerald
                return '#cbd5e1'; // Slate (requirement)
            })
            .attr("stroke-width", 2.5)
            .attr("stroke-opacity", 0.6)
            .attr("marker-end", "url(#arrowhead)");

        // Render Rectangular Nodes (Facts, Virtual Facts, Actions)
        const rectNodes = nodes.filter(n => n.type !== 'gate');
        const rectG = mainGroup.selectAll(".rect-node")
            .data(rectNodes)
            .enter()
            .append("g")
            .attr("transform", d => `translate(${d.x},${d.y})`)
            .attr("class", "cursor-pointer outline-none select-none transition-transform hover:scale-[1.02]")
            .on("click", (_, d) => selectedNode = d.data);

        rectG.append("rect")
            .attr("width", 180)
            .attr("height", 36)
            .attr("x", -90)
            .attr("y", -18)
            .attr("fill", d => {
                if(d.type === 'virtual_fact') return '#eff6ff'; // pale blue
                if(d.type === 'action') return '#059669'; // deep emerald
                return '#ffffff';
            })
            .attr("stroke", d => {
                if(d.type === 'virtual_fact') return '#3b82f6';
                if(d.type === 'action') return '#047857';
                return '#e2e8f0';
            })
            .attr("stroke-width", 1.5)
            .attr("rx", d => d.type === 'action' ? 18 : 6); // More rounded for action terminals

        rectG.append("text")
            .attr("text-anchor", "middle")
            .attr("dominant-baseline", "middle")
            .attr("class", d => `text-[10px] font-bold ${d.type === 'action' ? 'fill-white' : (d.type === 'virtual_fact' ? 'fill-blue-700' : 'fill-slate-700')}`)
            .text(d => d.data.name);

        // Render Gate Nodes (AND)
        const gateNodes = nodes.filter(n => n.type === 'gate');
        const gateG = mainGroup.selectAll(".gate-node")
            .data(gateNodes)
            .enter()
            .append("g")
            .attr("transform", d => `translate(${d.x},${d.y})`)
            .attr("class", "cursor-pointer select-none transition-transform hover:scale-[1.05]")
            .on("click", (_, d) => selectedNode = d.data);

        gateG.append("circle")
            .attr("r", 20)
            .attr("fill", "#f8fafc")
            .attr("stroke", "#64748b")
            .attr("stroke-width", 2);

        gateG.append("text")
            .attr("text-anchor", "middle")
            .attr("dominant-baseline", "middle")
            .attr("class", "text-[9px] font-black fill-slate-800 tracking-tighter")
            .text("AND");

        gateG.append("text")
            .attr("text-anchor", "middle")
            .attr("y", 32)
            .attr("class", "text-[8px] font-black fill-slate-400 uppercase tracking-widest")
            .text(d => d.data.id); 

        // Center on start, fit graph width
        const totalWidth = maxX + 240;
        const totalHeight = Math.max(height, maxY - minY + 200);
        const initialScale = Math.min(0.85, width / totalWidth);
        const yOffset = (height - totalHeight * initialScale) / 2;
        
        // Center view on the graph content
        svg.call(zoom.transform, d3.zoomIdentity.translate(80, Math.max(50, -minY * initialScale + yOffset)).scale(initialScale));
    }

    function resetView() {
        if (!svgRef || !zoom) return;
        d3.select(svgRef).transition().duration(750)
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

<div class="relative w-full rounded-3xl bg-white border-2 border-slate-100 shadow-xl overflow-hidden {isFullscreen ? 'fixed inset-0 z-50 p-6' : 'h-[750px]'}" bind:this={containerRef}>
    <!-- Header -->
    <div class="absolute top-6 left-6 right-6 z-10 flex items-center justify-between pointer-events-none">
        <div class="flex items-center gap-4 bg-white/80 backdrop-blur-xl p-3 px-5 rounded-2xl border border-slate-200 shadow-lg pointer-events-auto">
            <div class="p-2.5 bg-slate-900 rounded-xl text-white shadow-inner">
                <Brain size={22} />
            </div>
            <div>
                <h3 class="text-lg font-black text-slate-900 tracking-tight leading-none mb-1">Inference Engine</h3>
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest text-shadow-sm">Forward Chaining Schematic Diagram</p>
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
        <div class="absolute inset-0 pointer-events-none opacity-[0.03]" style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 24px 24px;"></div>
        
        <svg bind:this={svgRef} class="w-full h-full block touch-none group"></svg>
        
        <!-- Legend Bottom Right -->
        <div class="absolute bottom-6 right-6 flex flex-col gap-2 pointer-events-none">
            <div class="flex items-center gap-3 bg-white/90 backdrop-blur border border-slate-200 p-2 px-4 rounded-xl shadow-sm pointer-events-auto transition-transform">
                <div class="w-3 h-3 rounded-sm border-2 border-slate-200 bg-white"></div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Observed Facts</span>
            </div>
            <div class="flex items-center gap-3 bg-white/90 backdrop-blur border border-slate-200 p-2 px-4 rounded-xl shadow-sm pointer-events-auto transition-transform">
                <div class="w-3 h-3 rounded-full border-2 border-slate-600 bg-white"></div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Logic Gate (Rule)</span>
            </div>
            <div class="flex items-center gap-3 bg-white/90 backdrop-blur border border-slate-200 p-2 px-4 rounded-xl shadow-sm pointer-events-auto transition-transform">
                <div class="w-3 h-3 rounded-sm bg-emerald-600"></div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Adaptive Decisions</span>
            </div>
        </div>

        <!-- Node Details -->
        {#if selectedNode}
            <div class="absolute bottom-6 left-6 w-[320px] bg-white border-2 border-slate-100 rounded-3xl shadow-2xl overflow-hidden animate-in zoom-in-95 fade-in duration-300">
                <div class="p-5">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            {#if selectedNode.type === 'gate'}
                                <div class="p-3 rounded-2xl bg-slate-900 text-white shadow-lg">
                                    <Brain size={20} />
                                </div>
                            {:else if selectedNode.type === 'fact'}
                                <div class="p-3 rounded-2xl bg-blue-50 text-blue-600 shadow-sm border border-blue-100">
                                    <GitBranch size={20} />
                                </div>
                            {:else}
                                <div class="p-3 rounded-2xl bg-emerald-50 text-emerald-600 shadow-sm border border-emerald-100">
                                    <Target size={20} />
                                </div>
                            {/if}
                            <div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{selectedNode.type}</span>
                                <h4 class="text-sm font-bold text-slate-900 line-clamp-1">{selectedNode.name}</h4>
                            </div>
                        </div>
                        <button onclick={() => selectedNode = null} class="text-slate-400 hover:text-slate-600 p-1">
                            <X size={16} />
                        </button>
                    </div>

                    <div class="space-y-4">
                        {#if selectedNode.type === 'gate'}
                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Code</p>
                                    <p class="text-xs font-black text-slate-900 font-mono">{selectedNode.id}</p>
                                </div>
                                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Priority</p>
                                    <p class="text-xs font-black text-slate-900">P{selectedNode.priority}</p>
                                </div>
                            </div>
                        {:else}
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-xs text-slate-600 leading-relaxed italic">
                                {selectedNode.description || 'No additional description available.'}
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        {/if}

        <!-- Zoom Indicator -->
        <div class="absolute top-6 left-1/2 -translate-x-1/2 px-4 py-1.5 bg-slate-900/10 backdrop-blur-md rounded-full border border-white/20 text-[10px] font-bold text-slate-600 tracking-widest uppercase">
            Scale {Math.round(zoomLevel * 100)}%
        </div>
    </div>
</div>

<style>
    :global(.link) {
        transition: stroke-opacity 0.4s ease, stroke-width 0.4s ease;
    }
</style>
