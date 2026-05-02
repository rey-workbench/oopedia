import { BaseState } from '@/states/BaseState.svelte';
import type { Material } from '@/types';
import type { MaterialWithStats } from '@/types';

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

/**
 * Single Material View State
 */
export class MaterialShowState extends BaseState {
    material = $state<Material>({} as Material);
    from_adaptive = $state(false);

    constructor(material: Material, from_adaptive: boolean) {
        super();
        this.hydrate({ material, from_adaptive });
    }
}

/**
 * In Progress Materials State
 */
export class InProgressState extends BaseState {
    materials_with_stats = $state<MaterialWithStats[]>([]);

    constructor(materials_with_stats: MaterialWithStats[]) {
        super();
        this.hydrate({ materials_with_stats });
    }

    public calculateProgressPercentage(correctCount: number, totalQuestions: number): number {
        if (totalQuestions === 0) return 0;
        return Math.round((correctCount / totalQuestions) * 100);
    }
}

/**
 * Completed Materials State
 */
export class CompletedState extends BaseState {
    materials_with_stats = $state<MaterialWithStats[]>([]);

    constructor(materials_with_stats: MaterialWithStats[]) {
        super();
        this.hydrate({ materials_with_stats });
    }
}
