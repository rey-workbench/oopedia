import { router } from "@inertiajs/svelte";
import { debounce } from "lodash";
import { confirmDelete } from "@/utils/confirmDelete";

export class UserListState {
    users = $state<any>({ data: [] });
    search = $state("");

    constructor(users: any, search: any) {
        this.users = users;
        this.search = search;
    }

    handleSearch = debounce(() => {
        router.get(
            "/admin/users",
            { search: this.search },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            }
        );
    }, 300);

    handleDelete(id: any) {
        confirmDelete(`/admin/users/${id}`, "Hapus pengguna ini?");
    }
}
