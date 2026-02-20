import { router } from "@inertiajs/svelte";

export class SubMaterialState {
    material = $state<any>({});
    subMaterial = $state<any>({});

    // Derived state for navigation
    currentIndex = $derived(
        Array.isArray(this.material?.sub_materials)
            ? this.material.sub_materials.findIndex((sm: any) => sm.id === this.subMaterial.id)
            : -1
    );

    constructor(material: any, subMaterial: any) {
        this.material = material;
        this.subMaterial = subMaterial;
    }

    goToNext() {
        if (this.currentIndex < this.material.sub_materials.length - 1) {
            const nextId = this.material.sub_materials[this.currentIndex + 1].id;
            router.visit(`/mahasiswa/materials/${this.material.id}/sub-materials/${nextId}`);
        } else {
            // Finish material
            router.visit(`/mahasiswa/materials/${this.material.id}`);
        }
    }

    goToPrev() {
        if (this.currentIndex > 0) {
            const prevId = this.material.sub_materials[this.currentIndex - 1].id;
            router.visit(`/mahasiswa/materials/${this.material.id}/sub-materials/${prevId}`);
        }
    }
}
