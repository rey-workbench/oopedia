import { router } from "@inertiajs/svelte";
import { BaseState } from "@/states/BaseState.svelte";
import { ROUTES } from "@/utils/route";
import type { MaterialWithProgress } from "@/types";

/**
 * Material Catalog State
 */
export class MaterialCatalogState extends BaseState {
    materials = $state<MaterialWithProgress[]>([]);

    constructor(materials: MaterialWithProgress[]) {
        super();
        this.materials = materials;
    }
}

/**
 * Material Show Detail State
 */
export class MaterialShowState extends BaseState {
    material = $state<any>({});
    subMaterials = $state([]);
    fromAdaptive = $state(false);

    constructor(material: any, fromAdaptive: any) {
        super();
        this.material = material;
        this.subMaterials = material.sub_materials || material.subMaterials || [];
        this.fromAdaptive = fromAdaptive;
    }
}

/**
 * SubMaterial Detail & Navigation State
 */
export class SubMaterialState extends BaseState {
    material = $state<any>({});
    subMaterial = $state<any>({});

    currentIndex = $derived(
        Array.isArray(this.material?.sub_materials)
            ? this.material.sub_materials.findIndex((sm: any) => sm.id === this.subMaterial.id)
            : -1
    );

    otherSubMaterials = $derived(
        Array.isArray(this.material?.sub_materials)
            ? this.material.sub_materials.filter((sm: any) => sm.id !== this.subMaterial.id)
            : []
    );

    constructor(material: any, subMaterial: any) {
        super();
        this.material = material;
        this.subMaterial = subMaterial;
    }

    goToNext() {
        if (this.currentIndex < this.material.sub_materials.length - 1) {
            const nextId = this.material.sub_materials[this.currentIndex + 1].id;
            router.visit(ROUTES.MAHASISWA.SUBMATERIALS.SHOW(this.material.id, nextId));
        } else {
            router.visit(ROUTES.MAHASISWA.MATERIALS.SHOW(this.material.id));
        }
    }

    goToPrev() {
        if (this.currentIndex > 0) {
            const prevId = this.material.sub_materials[this.currentIndex - 1].id;
            router.visit(ROUTES.MAHASISWA.SUBMATERIALS.SHOW(this.material.id, prevId));
        }
    }
}

/**
 * In Progress Materials State
 */
export class InProgressState extends BaseState {
    materialsWithStats = $state<any[]>([]);

    constructor(materialsWithStats: any) {
        super();
        this.materialsWithStats = materialsWithStats;
    }

    calculateProgress(correct: number, total: number) {
        return total > 0 ? Math.round((correct / total) * 100) : 0;
    }
}

/**
 * Completed Materials State
 */
export class CompletedState extends BaseState {
    materials = $state<any[]>([]);

    constructor(materials: any) {
        super();
        this.materials = materials;
    }
}
