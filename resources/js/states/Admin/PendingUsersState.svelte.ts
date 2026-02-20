import { router } from "@inertiajs/svelte";

export class PendingUsersState {
    logout() {
        router.post("/logout");
    }
}
