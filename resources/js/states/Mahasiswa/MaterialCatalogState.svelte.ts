import { BaseState } from "@/states/BaseState.svelte";

export class MaterialCatalogState extends BaseState {
    materials = $state<any[]>([]);
    isGuest = $state(false);

    constructor(materials: any, isGuest: any) {
        super();
        this.materials = materials;
        this.isGuest = isGuest;
    }
}
