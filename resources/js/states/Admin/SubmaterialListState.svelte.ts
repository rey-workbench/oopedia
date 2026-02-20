import { router } from "@inertiajs/svelte";
import { confirmDelete } from "@/utils/confirmDelete";

export class SubmaterialListState {
    material = $state<any>(null);
    subMaterials = $state<any[]>([]);

    constructor(material: any, subMaterials: any) {
        this.material = material;
        this.subMaterials = subMaterials;
    }

    handleDelete(id: any) {
        confirmDelete(
            `/admin/materials/${this.material.id}/sub-materials/${id}`,
            "Hapus sub-materi ini?",
        );
    }
}
