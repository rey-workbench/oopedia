<script  lang="ts">
    import { Link, page, router } from "@inertiajs/svelte";
    import {
        Menu,
        ChevronRight,
        CircleHelp,
        User,
        LogOut,
    } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";
    import { sidebarOpen } from "@/stores/sidebar";
    import { isAdmin } from "@/utils/roles";

    let { titlePage = "" } = $props();

    const auth = $derived($page.props.auth ?? {});
    const user = $derived(auth.user ?? null);
    const isAuthenticated = $derived(!!user);
    const isAdminRole = $derived(isAuthenticated && isAdmin(user?.role_id));
    const userName = $derived(user?.name ?? "Tamu");

    function logout() {
        router.post("/logout");
    }

    function toggleSidebar() {
        sidebarOpen.update((v) => !v);
    }
</script>

<nav
    class="sticky top-0 z-40 w-full bg-white/80 backdrop-blur-md border-b border-gray-100 px-4 sm:px-6 lg:px-8"
>
    <div class="flex h-16 items-center justify-between">
        <div class="flex items-center gap-4">
            <button
                on:click={toggleSidebar}
                aria-label="Toggle Sidebar"
                class="lg:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-50 transition-colors"
            >
                <Menu size={20} strokeWidth={2.5} />
            </button>

            <div
                class="hidden sm:block"
                data-intro="Breadcrumbs menunjukkan di mana posisi kamu saat ini."
                data-step="3"
            >
                <nav class="flex text-sm" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <span class="text-slate-400 font-medium"
                                >Halaman</span
                            >
                        </li>
                        <li>
                            <div class="flex items-center">
                                <ChevronRight
                                    size={10}
                                    strokeWidth={3}
                                    class="text-slate-300 mx-2"
                                />
                                <span
                                    class="text-slate-900 font-bold tracking-widest uppercase"
                                    >{titlePage}</span
                                >
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-4">
            {#if isAuthenticated}
                <div class="hidden md:flex flex-col items-end mr-2">
                    <span class="text-sm font-bold text-slate-900 leading-none"
                        >{userName}</span
                    >
                    <span
                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                        >{isAdminRole ? "Administrator" : "Mahasiswa"}</span
                    >
                </div>

                <button
                    id="start-page-tour"
                    aria-label="Start Page Tour"
                    class="p-2 rounded-xl text-slate-500 hover:bg-accent-50 hover:text-accent-600 transition-all group relative"
                    title="Mulai Tutorial"
                    data-intro="Klik tombol ini kapan saja jika kamu butuh bantuan atau ingin mengulang tutorial di halaman ini."
                    data-step="4"
                >
                    <CircleHelp size={20} strokeWidth={2.5} />
                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-accent-400 opacity-75"
                        ></span>
                        <span
                            class="relative inline-flex rounded-full h-3 w-3 bg-accent-500"
                        ></span>
                    </span>
                </button>

                <div
                    class="relative group"
                    data-intro="Kelola profil kamu atau keluar dari akun melalui menu ini."
                    data-step="5"
                >
                    <button
                        aria-label="Open project menu"
                        class="flex items-center gap-2 p-1 rounded-2xl border-2 border-transparent hover:border-accent-100 transition-all duration-300 group"
                    >
                        <div
                            class="w-10 h-10 rounded-xl overflow-hidden shadow-[inset_0_2px_4px_rgba(0,0,0,0.06)] ring-2 ring-white group-hover:ring-slate-100 bg-slate-100 border border-slate-200 transition-all flex items-center justify-center"
                        >
                            <img
                                src="/images/profile.gif"
                                alt="Profile"
                                class="w-full h-full object-cover"
                            />
                        </div>
                    </button>

                    <div
                        class="absolute right-0 mt-2 w-56 origin-top-right rounded-2xl bg-white p-2 shadow-2xl ring-1 ring-black/5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform scale-95 group-hover:scale-100"
                    >
                        <div class="px-4 py-3 border-b border-slate-50 mb-1">
                            <p class="text-sm font-bold text-slate-900">
                                {userName}
                            </p>
                            <p class="text-[10px] text-slate-400 truncate">
                                {user.email}
                            </p>
                        </div>

                        <Link
                            href={ROUTES.MAHASISWA.PROFILE}
                            class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-slate-600 hover:text-accent-600 hover:bg-accent-50 rounded-xl transition-all"
                        >
                            <User size={18} strokeWidth={2.5} class="w-5" />
                            Profil Saya
                        </Link>

                        <form on:submit|preventDefault={logout}>
                            <button
                                type="submit"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-rose-600 hover:bg-rose-50 rounded-xl transition-all"
                            >
                                <LogOut
                                    size={18}
                                    strokeWidth={2.5}
                                    class="w-5"
                                />
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            {:else}
                <div class="flex items-center gap-2">
                    <Link
                        href={ROUTES.AUTH.LOGIN}
                        class="px-6 py-2.5 text-sm font-bold uppercase tracking-widest text-slate-600 hover:text-accent-600 transition-all"
                        >Masuk</Link
                    >
                    <Link
                        href={ROUTES.AUTH.REGISTER}
                        class="px-6 py-2.5 bg-slate-900 text-white text-sm font-bold uppercase tracking-widest rounded-xl hover:bg-accent-600 transition-all shadow-lg shadow-slate-200"
                        >Daftar</Link
                    >
                </div>
            {/if}
        </div>
    </div>
</nav>
