import { router } from "@inertiajs/svelte";
import { BaseState } from "@/states/BaseState.svelte";
import { ROUTES } from "@/utils/route";
import type { Material, SubMaterial, MaterialWithStats } from "@/types";

/**
 * Material Catalog State
 */
export class MaterialCatalogState extends BaseState {
    materials = $state<Material[]>([]);

    constructor(materials: Material[]) {
        super();
        this.materials = materials;
    }
}

/**
 * Material Show Detail State
 */
export class MaterialShowState extends BaseState {
    material = $state<Material>({} as Material);
    subMaterials = $state<SubMaterial[]>([]);
    fromAdaptive = $state(false);

    constructor(material: Material, fromAdaptive: boolean) {
        super();
        this.material = material;
        this.subMaterials = material.sub_materials ?? [];
        this.fromAdaptive = fromAdaptive;
    }
}

/**
 * SubMaterial Detail & Navigation State
 */
export class SubMaterialState extends BaseState {
    material = $state<Material>({} as Material);
    subMaterial = $state<SubMaterial>({} as SubMaterial);

    currentIndex = $derived(
        Array.isArray(this.material?.sub_materials)
            ? this.material.sub_materials.findIndex((sm: SubMaterial) => sm.id === this.subMaterial.id)
            : -1
    );

    otherSubMaterials = $derived(
        Array.isArray(this.material?.sub_materials)
            ? this.material.sub_materials.filter((sm: SubMaterial) => sm.id !== this.subMaterial.id)
            : []
    );

    constructor(material: Material, subMaterial: SubMaterial) {
        super();
        this.material = material;
        this.subMaterial = subMaterial;
    }

    goToNext() {
        if (this.material.sub_materials && this.currentIndex < this.material.sub_materials.length - 1) {
            const nextSubMaterial = this.material.sub_materials[this.currentIndex + 1];
            if (nextSubMaterial) {
                router.visit(ROUTES.MAHASISWA.SUBMATERIALS.SHOW(this.material.id, nextSubMaterial.id));
            }
        } else {
            router.visit(ROUTES.MAHASISWA.MATERIALS.SHOW(this.material.id));
        }
    }

    goToPrevious() {
        if (this.material.sub_materials && this.currentIndex > 0) {
            const prevSubMaterial = this.material.sub_materials[this.currentIndex - 1];
            if (prevSubMaterial) {
                router.visit(ROUTES.MAHASISWA.SUBMATERIALS.SHOW(this.material.id, prevSubMaterial.id));
            }
        }
    }
}

/**
 * In Progress Materials State
 */
export class InProgressState extends BaseState {
    materialsWithStats = $state<MaterialWithStats[]>([]);

    constructor(materialsWithStats: MaterialWithStats[]) {
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
    materialsWithStats = $state<MaterialWithStats[]>([]);

    constructor(materialsWithStats: MaterialWithStats[]) {
        super();
        this.materialsWithStats = materialsWithStats;
    }
}
