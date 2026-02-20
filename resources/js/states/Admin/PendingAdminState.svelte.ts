import { router } from "@inertiajs/svelte";

export class PendingAdminState {
    pendingAdmins = $state<any[]>([]);

    constructor(pendingAdmins: any) {
        this.pendingAdmins = pendingAdmins;
    }

    handleApprove(id: any) {
        router.post(`/admin/pending-admins/${id}/approve`);
    }

    handleReject(id: any) {
        router.post(`/admin/pending-admins/${id}/reject`);
    }
}
