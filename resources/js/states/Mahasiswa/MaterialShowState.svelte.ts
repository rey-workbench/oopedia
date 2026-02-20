export class MaterialShowState {
    material = $state<any>({});
    subMaterials = $state([]);
    fromAdaptive = $state(false);

    constructor(material: any, fromAdaptive: any) {
        this.material = material;
        // Handling subMaterials properly if it comes as an array or object
        this.subMaterials = material.subMaterials || material.sub_materials || [];
        this.fromAdaptive = fromAdaptive;
    }
}
