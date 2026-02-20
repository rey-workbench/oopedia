import { router } from "@inertiajs/svelte";
import { debounce } from "lodash";
import { confirmDelete } from "@/utils/confirmDelete";
import { BaseState } from "@/states/BaseState.svelte";
import { ROUTES } from "@/utils/route";

export class StudentListState extends BaseState {
    students = $state<any>({ data: [] });
    search = $state("");

    constructor(students: any, search: any) {
        super();
        this.students = students;
        this.search = search;
    }

    handleSearch = debounce(() => {
        router.get(
            ROUTES.ADMIN.STUDENTS.INDEX,
            { search: this.search },
            { preserveState: true, replace: true }
        );
    }, 300);

    handleDelete(id: any) {
        confirmDelete(
            ROUTES.ADMIN.STUDENTS.DELETE(id),
            "Hapus data mahasiswa ini?"
        );
    }
}
