import { BaseState } from '@/states/BaseState.svelte';
import type { Material, MaterialWithStats } from '@/types';

/**
 * Material Catalog State
 */
export class MaterialCatalogState extends BaseState {
    materials = $state<Material[]>([]);

    constructor(materials: Material[]) {
        super();
        this.hydrate({ materials });
    }
}

export class MaterialShowState extends BaseState {
    material = $state<Material>({} as Material);
    fromAdaptive = $state(false);

    constructor(material: Material, fromAdaptive: boolean) {
        super();
        this.hydrate({ material, fromAdaptive });
    }
}


/**
 * In Progress Materials State
 */
export class InProgressState extends BaseState {
    materialsWithStats = $state<MaterialWithStats[]>([]);

    constructor(materialsWithStats: MaterialWithStats[]) {
        super();
        this.hydrate({ materialsWithStats });
    }

    calculateProgress(correct: number, total: number) {
        return total > 0 ? Math.round((correct / total) * 100) : 0;
    }
}

/**
 * Completed Materials State
 */
export class CompletedState extends BaseState {
    materialsWithStats = $state<MaterialWithStats[]>([]);

    constructor(materialsWithStats: MaterialWithStats[]) {
        super();
        this.hydrate({ materialsWithStats });
    }
}
