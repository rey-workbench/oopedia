<script>
    import Card from "@/ui/Card.svelte";
    import Button from "@/ui/Button.svelte";
    import Badge from "@/ui/Badge.svelte";
    import { ShieldCheck, UserX, Settings, Edit3, Edit2 } from "lucide-svelte";
    import { UserListState } from "@/states/Admin/UserListState.svelte";

    export let users = { data: [] };
    export let search = "";

    const state = new UserListState(users, search);
</script>

<Card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
    <div
        class="flex flex-col md:flex-row justify-between items-center gap-6 w-full px-6 py-4 border-b border-slate-50"
    >
        <p
            class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
        >
            Direktori Pengguna Sistem
        </p>
        <div class="w-full md:w-auto">
            <div class="relative group">
                <ShieldCheck
                    size={18}
                    class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-600 transition-colors"
                />
                <input
                    type="text"
                    bind:value={state.search}
                    on:input={state.handleSearch}
                    placeholder="Cari nama atau email..."
                    class="w-full md:w-64 pl-12 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-primary-100 focus:border-primary-600 transition-all outline-none"
                />
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr>
                    <th
                        class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                        >Identitas</th
                    >
                    <th
                        class="p-6 text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                        >Otorisasi Email</th
                    >
                    <th
                        class="p-6 text-center text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                        >Peran Sistem</th
                    >
                    <th
                        class="p-6 text-center text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                        >Status Akses</th
                    >
                    <th
                        class="p-6 text-right text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50"
                        >Aksi</th
                    >
                </tr>
            </thead>
            <tbody>
                {#each state.users.data as user (user.id)}
                    <tr
                        class={`group transition-colors border-b border-slate-50 last:border-0 ${user.role_id === 1 ? "bg-slate-900/5" : "hover:bg-slate-50"}`}
                    >
                        <td
                            class={`px-6 py-6 ${user.role_id === 1 ? "border-l-4 border-slate-900" : ""}`}
                        >
                            <div class="flex items-center gap-4">
                                <div
                                    class={`w-10 h-10 rounded-xl flex items-center justify-center font-bold shadow-sm uppercase text-xs ${user.role_id === 1 ? "bg-slate-900 text-white shadow-slate-200" : "bg-slate-100 text-slate-500"}`}
                                >
                                    {user.name.charAt(0)}
                                </div>
                                <div>
                                    <div
                                        class="font-bold text-slate-900 tracking-widest"
                                    >
                                        {user.name}
                                    </div>
                                    {#if user.role_id === 1}
                                        <span
                                            class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                                            >ADMIN SISTEM UTAMA</span
                                        >
                                    {/if}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <span
                                class={`text-xs font-bold text-slate-400 ${user.role_id === 1 ? "underline decoration-slate-200 underline-offset-4" : ""}`}
                                >{user.email}</span
                            >
                        </td>
                        <td class="px-6 py-6 text-center">
                            <Badge
                                variant={user.role_id === 1
                                    ? "dark"
                                    : user.role_id === 2
                                      ? "primary"
                                      : "secondary"}
                                size="xs"
                            >
                                {user.role?.role_name?.toUpperCase() ||
                                    "UNKNOWN"}
                            </Badge>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <div class="flex items-center justify-center gap-2">
                                {#if user.role_id === 1}
                                    <div
                                        class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"
                                    ></div>
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-widest text-emerald-600"
                                        >TANPA BATAS</span
                                    >
                                {:else}
                                    <div
                                        class={`w-2 h-2 rounded-full ${user.is_approved ? "bg-emerald-500" : "bg-amber-500"}`}
                                    ></div>
                                    <span
                                        class={`text-[10px] font-bold uppercase tracking-widest ${user.is_approved ? "text-emerald-500" : "text-amber-500"}`}
                                    >
                                        {user.is_approved
                                            ? "DISETUJUI"
                                            : "MENUNGGU"}
                                    </span>
                                {/if}
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex justify-end gap-2">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    href={`/admin/users/${user.id}/edit`}
                                    icon={user.role_id === 1 &&
                                    state.authUser.id === user.id
                                        ? Settings
                                        : user.role_id === 1
                                          ? Edit3
                                          : Edit2}
                                />

                                {#if state.isSuperAdmin && state.authUser.id !== user.id}
                                    <button
                                        on:click={() =>
                                            state.handleDelete(user.id)}
                                        class="p-2 rounded-xl font-bold uppercase tracking-widest transition-all duration-300 flex items-center justify-center gap-2 hover:bg-slate-100 text-slate-300 hover:text-rose-500 text-xs"
                                    >
                                        <UserX size={18} strokeWidth={2.5} />
                                    </button>
                                {/if}
                            </div>
                        </td>
                    </tr>
                {/each}
            </tbody>
        </table>

        <!-- Simple Pagination -->
        {#if state.users.links && state.users.links.length > 3}
            <div class="p-6 border-t border-slate-100 flex justify-center">
                <div class="flex gap-1">
                    {#each state.users.links as link}
                        {#if link.url}
                            <Button
                                href={link.url}
                                variant={link.active ? "primary" : "ghost"}
                                size="sm"
                                class={!link.active && !link.url
                                    ? "opacity-50 cursor-not-allowed"
                                    : ""}
                            >
                                {@html link.label}
                            </Button>
                        {:else}
                            <span
                                class="px-3 py-2 text-slate-400 text-xs font-bold"
                                >{@html link.label}</span
                            >
                        {/if}
                    {/each}
                </div>
            </div>
        {/if}
    </div>
</Card>
