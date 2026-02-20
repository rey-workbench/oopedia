import { router } from "@inertiajs/svelte";
import { confirmDelete } from "@/utils/confirmDelete";
import { BaseState } from "@/states/BaseState.svelte";
import { ROUTES } from "@/utils/route";

export class MaterialListState extends BaseState {
    materials = $state<any[]>([]);
    search = $state("");

    constructor(materials: any, search: any) {
        super();
        this.materials = materials;
        this.search = search;
    }

    handleSearch() {
        router.get(
            ROUTES.ADMIN.MATERIALS.INDEX,
            { search: this.search },
            { preserveState: true, replace: true }
        );
    }

    handleDelete(id: any) {
        confirmDelete(
            ROUTES.ADMIN.MATERIALS.DELETE(id),
            "Hapus materi ini secara permanen dari basis data?"
        );
    }
}
