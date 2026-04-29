
export const GRAPH_CONFIG = {
    colWidth: 550,
    itemHeight: 50,
    verticalGap: 48,
    startX: 100,
    maxTopologyIterations: 30,
    barycenterIterations: 12,
    initialScale: 0.65,
};

export function resolveGraphTopology(rules: any[], factData: any[], actionData: any[]) {
    const nodeRegistry = new Map<string, any>();

    // Phase 1: Initialize Virtual Inputs
    const inputs = [
        { id: 'IN_ACC', name: 'Jawaban (B/S)' },
        { id: 'IN_SPD', name: 'Kecepatan Respons' },
        { id: 'IN_HLP', name: 'Penggunaan Bantuan' },
        { id: 'IN_LVL', name: 'Level Saat Ini' },
        { id: 'IN_STR', name: 'Streak & Sesi' },
    ];

    inputs.forEach((input) => {
        nodeRegistry.set(`input_${input.id}`, {
            type: 'input',
            depth: 0,
            data: input,
            id: input.id,
        });
    });

    // Phase 1.5: Initialize Raw Facts (KONDISI)
    factData
        .filter((f) => !f.id.startsWith('V'))
        .forEach((f) => {
            nodeRegistry.set(`raw_fact_${f.id}`, {
                type: 'raw_fact',
                depth: 1,
                data: f,
                id: `f_${f.id}`,
            });
        });

    // Phase 2: Iteratively resolve
    let hasTopologyChanged = true;
    let iterations = GRAPH_CONFIG.maxTopologyIterations;

    while (hasTopologyChanged && iterations-- > 0) {
        hasTopologyChanged = false;

        rules.forEach((rule) => {
            const prerequisites = rule.required_fact_ids || [];
            const resolvedSources = prerequisites.map(
                (id: string) =>
                    nodeRegistry.get(`raw_fact_${id}`) || nodeRegistry.get(`virtual_fact_${id}`)
            );

            if (!prerequisites.every((_: any, i: number) => !!resolvedSources[i])) return;

            const maxPrereqDepth = resolvedSources.length > 0
                ? Math.max(...resolvedSources.map((s: any) => s.depth))
                : 1;

            const ruleDepth = maxPrereqDepth + 0.5;

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

            // Register Deduced Facts (DIAGNOSIS)
            if (rule.deduced_fact_ids && rule.deduced_fact_ids.length > 0) {
                rule.deduced_fact_ids.forEach((id: string) => {
                    if (registerVirtualFact(nodeRegistry, id, 2, factData)) {
                        hasTopologyChanged = true;
                    }
                });
            }

            // Register Resulting Actions (REKOMENDASI)
            const actionIds = rule.actions ? rule.actions.map((a: any) => a.id) : [];
            actionIds.forEach((id: string) => {
                if (registerAction(nodeRegistry, id, 3, actionData)) {
                    hasTopologyChanged = true;
                }
            });
        });
    }

    return nodeRegistry;
}

function registerVirtualFact(registry: Map<string, any>, id: string, depth: number, factData: any[]) {
    const fact = factData.find((f) => f.id === id);
    if (!fact) return false;

    const factId = `virtual_fact_${id}`;
    const existing = registry.get(factId);

    if (!existing) {
        registry.set(factId, {
            type: 'virtual_fact',
            depth: depth,
            data: fact,
            id: `vf_${id}`,
        });
        return true;
    }

    if (existing.depth < depth) {
        existing.depth = depth;
        return true;
    }

    return false;
}

function registerAction(registry: Map<string, any>, id: string, depth: number, actionData: any[]) {
    const action = actionData.find((a) => a.id === id);
    if (!action) return false;

    const actionId = `action_${id}`;
    const existing = registry.get(actionId);

    if (!existing) {
        registry.set(actionId, {
            type: 'action',
            depth: depth,
            data: action,
            id: `a_${id}`,
        });
        return true;
    }

    if (existing.depth < depth) {
        existing.depth = depth;
        return true;
    }

    return false;
}

export function buildGraphLinks(nodeRegistry: Map<string, any>, rules: any[]) {
    const links: any[] = [];

    rules.forEach((rule) => {
        const gateNode = nodeRegistry.get(`rule_${rule.id}`);
        if (!gateNode) return;

        (rule.required_fact_ids || []).forEach((id: string) => {
            const source =
                nodeRegistry.get(`raw_fact_${id}`) ||
                nodeRegistry.get(`virtual_fact_${id}`);
            if (source) links.push({ source, target: gateNode, type: 'requirement' });
        });

        const deducedFactIds = rule.deduced_fact_ids || [];
        deducedFactIds.forEach((id: string) => {
            const target = nodeRegistry.get(`virtual_fact_${id}`);
            if (target) links.push({ source: gateNode, target, type: 'deduction' });
        });

        const actionIds = rule.actions ? rule.actions.map((a: any) => a.id) : [];
        actionIds.forEach((id: string) => {
            const actionNode = nodeRegistry.get(`action_${id}`);
            if (!actionNode) return;

            if (deducedFactIds.length > 0) {
                deducedFactIds.forEach((factId: string) => {
                    const factNode = nodeRegistry.get(`virtual_fact_${factId}`);
                    if (factNode) links.push({ source: factNode, target: actionNode, type: 'action' });
                });
            } else {
                links.push({ source: gateNode, target: actionNode, type: 'action' });
            }
        });
    });

    // Link Inputs -> Kondisi
    const conditions = Array.from(nodeRegistry.values()).filter(n => n.type === 'raw_fact');
    conditions.forEach(cond => {
        const id = cond.data.id;
        let inputNodeId = null;
        if (id.startsWith('G0')) inputNodeId = 'input_IN_ACC';
        else if (id.startsWith('G05') || id.startsWith('G06') || id.startsWith('G07')) inputNodeId = 'input_IN_SPD'; 
        else if (id.startsWith('G08') || id.startsWith('G09') || id.startsWith('G20')) inputNodeId = 'input_IN_HLP';
        else if (id.startsWith('G11') || id.startsWith('G12') || id.startsWith('G13')) inputNodeId = 'input_IN_SPD';
        else if (id.startsWith('G14') || id.startsWith('G15') || id.startsWith('G16')) inputNodeId = 'input_IN_STR';
        else if (id === 'G19') inputNodeId = 'input_IN_LVL';
        
        if (inputNodeId) {
            const inputNode = nodeRegistry.get(inputNodeId);
            if (inputNode) links.push({ source: inputNode, target: cond, type: 'requirement' });
        }
    });

    return links;
}

export function calculateSpatialCoordinates(nodes: any[], links: any[], containerHeight: number) {
    const depthGroups: Record<number, any[]> = {};
    nodes.forEach((n) => {
        if (!depthGroups[n.depth]) depthGroups[n.depth] = [];
        depthGroups[n.depth]!.push(n);
    });

    const depths = Object.keys(depthGroups)
        .map(Number)
        .sort((a, b) => a - b);

    depths.forEach((d) => {
        depthGroups[d]!.forEach(
            (n, i) => (n.y = i * (GRAPH_CONFIG.itemHeight + GRAPH_CONFIG.verticalGap))
        );
    });

    for (let i = 0; i < GRAPH_CONFIG.barycenterIterations; i++) {
        optimizeLayoutLayer(depths, depthGroups, links, true);
        optimizeLayoutLayer([...depths].reverse(), depthGroups, links, false);
    }

    return centerAndProject(depths, depthGroups, containerHeight);
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
    if (group.length <= 1) return;
    const minGap = GRAPH_CONFIG.itemHeight + GRAPH_CONFIG.verticalGap;
    for (let i = 1; i < group.length; i++) {
        if (group[i].y < group[i-1].y + minGap) {
            group[i].y = group[i-1].y + minGap;
        }
    }
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
