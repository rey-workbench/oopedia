import { router } from "@inertiajs/svelte";

/**
 * Show a browser confirm dialog, then send a DELETE request via Inertia router.
 *
 * @param url    - The route to DELETE (e.g. `/admin/materials/${id}`)
 * @param message - Custom confirmation prompt (defaults to Indonesian)
 */
export function confirmDelete(
    url: string,
    message: string = "Data yang dihapus tidak dapat dikembalikan. Lanjutkan?"
): void {
    if (confirm(message)) {
        router.delete(url);
    }
}
