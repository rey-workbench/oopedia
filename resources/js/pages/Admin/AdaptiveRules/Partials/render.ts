import * as d3 from 'd3';
import { GRAPH_CONFIG } from './topology';

export interface RenderContext {
    svgRef: SVGSVGElement | null;
    mainGroup: d3.Selection<SVGGElement, unknown, null, undefined>;
    zoom: d3.ZoomBehavior<SVGSVGElement, unknown>;
    isInitialized: boolean;
    isFullscreen: boolean;
    zoomLevel: number;
    handleNodeClick: (d: any, event: MouseEvent) => void;
    handleNodeContextMenu: (d: any, event: MouseEvent) => void;
    resetD3Flow: () => void;
}

export function setupMarkers(defs: d3.Selection<SVGDefsElement, unknown, null, undefined>) {
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

export function generateBezierPath(d: any) {
    const isBackward = d.target.x < d.source.x;

    if (!isBackward) {
        // Forward link: Exit Right, Enter Left
        const sx = d.source.x + (d.source.type === 'gate' ? 30 : 120);
        const sy = d.source.y;
        const tx = d.target.x - (d.target.type === 'gate' ? 30 : 120);
        const ty = d.target.y;
        const dx = tx - sx;
        return `M${sx},${sy}C${sx + dx / 2},${sy} ${sx + dx / 2},${ty} ${tx},${ty}`;
    } else {
        // Backward link: Exit Left, Enter Right
        const sx = d.source.x - (d.source.type === 'gate' ? 30 : 120);
        const sy = d.source.y;
        const tx = d.target.x + (d.target.type === 'gate' ? 30 : 120);
        const ty = d.target.y;
        
        // Control points offset to create a nice loop/bow out effect
        const offset = Math.max(100, Math.abs(d.source.x - d.target.x) / 3);
        return `M${sx},${sy}C${sx - offset},${sy} ${tx + offset},${ty} ${tx},${ty}`;
    }
}

export function getLinkColor(type: string) {
    const colors: Record<string, string> = { deduction: '#10b981', action: '#10b981', requirement: '#cbd5e1' };
    return colors[type] || '#94a3b8';
}

export function getNodeFill(type: string) {
    if (type === 'virtual_fact') return '#f0fdf4';
    if (type === 'action') return '#d97706';
    if (type === 'input') return '#f8fafc';
    if (type === 'raw_fact') return '#f5f7ff';
    return '#ffffff';
}

export function getNodeStroke(type: string) {
    if (type === 'virtual_fact') return '#10b981';
    if (type === 'action') return '#92400e';
    if (type === 'input') return '#e2e8f0';
    if (type === 'raw_fact') return '#6366f1';
    return '#cbd5e1';
}

export function renderGraph(
    nodes: any[], 
    links: any[], 
    width: number, 
    height: number, 
    ctx: RenderContext,
    layers: any[]
) {
    const maxX = Math.max(...nodes.map((n) => n.x), 100);
    const minY = Math.min(...nodes.map((n) => n.y), 0);
    const maxY = Math.max(...nodes.map((n) => n.y), 100);

    const drag = d3.drag<SVGGElement, any>()
        .on('start', function() { d3.select(this).raise(); })
        .on('drag', function(event, d) {
            d.x = event.x;
            d.y = event.y;
            d3.select(this).attr('transform', `translate(${d.x},${d.y})`);
            ctx.mainGroup.selectAll<SVGPathElement, any>('.link').attr('d', generateBezierPath);
        });

    drawLayerBackgrounds(ctx.mainGroup, layers, height);
    drawLinks(ctx.mainGroup, links);
    drawNodes(ctx.mainGroup, nodes, drag, ctx);

    const initialScale = Math.min(GRAPH_CONFIG.initialScale, width / (maxX + 280));

    if (!ctx.isInitialized) {
        d3.select(ctx.svgRef!).call(
            ctx.zoom.transform,
            d3.zoomIdentity
                .translate(
                    width / 2 - (maxX / 2) * initialScale,
                    height / 2 - ((maxY + minY) / 2) * initialScale
                )
                .scale(initialScale)
        );
        return true; // isInitialized = true
    }
    return false;
}

function drawLinks(mainGroup: any, links: any[]) {
    mainGroup
        .selectAll('.link')
        .data(links)
        .enter()
        .append('path')
        .attr('class', 'link')
        .attr('d', generateBezierPath)
        .attr('fill', 'none')
        .attr('stroke', (d: any) => getLinkColor(d.type))
        .attr('stroke-width', 2)
        .attr('stroke-opacity', 0.4)
        .attr('marker-end', (d: any) => `url(#arrow-${d.type})`);
}

function drawLayerBackgrounds(mainGroup: any, layers: any[], height: number) {
    const layerGroup = mainGroup.append('g').attr('class', 'layer-backgrounds');
    layers.forEach((layer, i) => {
        const x = GRAPH_CONFIG.startX + i * GRAPH_CONFIG.colWidth - 250;
        layerGroup.append('rect')
            .attr('class', `layer-${layer.id}`)
            .attr('x', x)
            .attr('y', -height * 2)
            .attr('width', GRAPH_CONFIG.colWidth)
            .attr('height', height * 5)
            .attr('fill', layer.id === 'input' ? '#f8fafc' : (layer.id === 'condition' ? '#f5f7ff' : (layer.id === 'diagnosis' ? '#f0fdf4' : '#fffbeb')))
            .attr('opacity', 0.5);

        layerGroup.append('text')
            .attr('x', x + GRAPH_CONFIG.colWidth / 2)
            .attr('y', -height / 2 + 100)
            .attr('text-anchor', 'middle')
            .attr('class', 'text-[12px] font-black fill-slate-300 tracking-[0.2em]')
            .text(layer.label);
    });
}

function drawNodes(mainGroup: any, nodes: any[], drag: any, ctx: RenderContext) {
    const rectNodes = nodes.filter((n) => n.type !== 'gate');
    const gateNodes = nodes.filter((n) => n.type === 'gate');

    // RECT NODES (Facts, Inputs, Actions)
    const nodeSelection = mainGroup
        .selectAll('.rect-node')
        .data(rectNodes)
        .enter()
        .append('g')
        .attr('transform', (d: any) => `translate(${d.x},${d.y})`)
        .attr('class', 'rect-node cursor-grab active:cursor-grabbing select-none outline-none focus:outline-none')
        .attr('tabindex', 0)
        .call(drag)
        .on('click', (event: MouseEvent, d: any) => ctx.handleNodeClick(d, event))
        .on('contextmenu', (event: MouseEvent, d: any) => ctx.handleNodeContextMenu(d, event));

    // Focus ring for rect nodes
    nodeSelection.append('rect')
        .attr('class', 'focus-ring transition-all duration-300')
        .attr('width', 252)
        .attr('height', 56)
        .attr('x', -126)
        .attr('y', -28)
        .attr('rx', 14)
        .attr('fill', 'none')
        .attr('stroke', '#6366f1')
        .attr('stroke-width', 0)
        .attr('opacity', 0);

    nodeSelection.append('rect')
        .attr('width', 240)
        .attr('height', 44)
        .attr('x', -120)
        .attr('y', -22)
        .attr('rx', (d: any) => (d.type === 'action' ? 8 : 12))
        .attr('fill', (d: any) => getNodeFill(d.type))
        .attr('stroke', (d: any) => getNodeStroke(d.type))
        .attr('stroke-width', (d: any) => d.type === 'input' ? 1 : 2)
        .attr('class', 'node-body shadow-sm transition-all duration-300')
        .style('opacity', 0)
        .transition()
        .duration(500)
        .style('opacity', 1);

    nodeSelection.append('text')
        .attr('text-anchor', 'middle')
        .attr('dominant-baseline', 'middle')
        .attr('class', (d: any) => `text-[11px] font-bold pointer-events-none ${d.type === 'action' ? 'fill-white' : (d.type === 'input' ? 'fill-slate-500' : 'fill-slate-800')}`)
        .text((d: any) => d.data.name);

    // GATE NODES (Rules)
    const gateSelection = mainGroup
        .selectAll('.gate-node')
        .data(gateNodes)
        .enter()
        .append('g')
        .attr('transform', (d: any) => `translate(${d.x},${d.y})`)
        .attr('class', 'gate-node cursor-grab active:cursor-grabbing select-none outline-none focus:outline-none')
        .attr('tabindex', 0)
        .call(drag)
        .on('click', (event: MouseEvent, d: any) => ctx.handleNodeClick(d, event))
        .on('contextmenu', (event: MouseEvent, d: any) => ctx.handleNodeContextMenu(d, event));

    // Focus ring for gate nodes
    gateSelection.append('circle')
        .attr('class', 'focus-ring transition-all duration-300')
        .attr('r', 32)
        .attr('fill', 'none')
        .attr('stroke', '#6366f1')
        .attr('stroke-width', 0)
        .attr('opacity', 0);

    gateSelection.append('circle')
        .attr('r', 24)
        .attr('fill', '#0f172a')
        .attr('stroke', '#334155')
        .attr('stroke-width', 2)
        .attr('class', 'node-body')
        .style('opacity', 0)
        .transition()
        .duration(500)
        .style('opacity', 1);

    gateSelection.append('text')
        .attr('text-anchor', 'middle')
        .attr('dominant-baseline', 'middle')
        .attr('class', 'text-[10px] font-bold fill-white antialiased pointer-events-none')
        .text('AND');

    gateSelection.append('text')
        .attr('text-anchor', 'middle')
        .attr('y', 38)
        .attr('class', 'text-[9px] font-medium fill-slate-400 uppercase tracking-wider pointer-events-none')
        .text((d: any) => d.data.id);
}
