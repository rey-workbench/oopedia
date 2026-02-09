<script>
    import { Link, page, router } from "@inertiajs/svelte";
    import SidebarLink from "./SidebarLink.svelte";
    import {
        LayoutDashboard,
        BookOpen,
        SquareActivity,
        GraduationCap,
        Settings,
        MessageSquareQuote,
        LogOut,
        Shapes,
        Trophy,
        UserRound,
        X,
    } from "lucide-svelte";

    export let showSidebar = true;

    $: auth = $page.props.auth || {};
    $: user = auth.user;
    $: isAuthenticated = !!user;
    $: userRole = user ? user.role_id : null;
    $: isAdminRole = isAuthenticated && [1, 2].includes(userRole);
    $: isStudentRole = isAuthenticated && [3, 4].includes(userRole);

    // Simple check for active route based on URL start
    const isActive = (url) =>
        $page.url === url || $page.url.startsWith(url + "/");

    function logout() {
        router.post("/logout");
    }

    function toggleSidebar() {
        // Dispatch event or strictly control via parent,
        // but for now relying on parent to handle overlay logic or local toggle
        // A simple global custom event or prop callback would work.
        // Let's assume a prop for class toggling is passed or we emit.
        // For direct port, we might use a dispatcher.
        const event = new CustomEvent("toggle-sidebar");
        window.dispatchEvent(event);
    }
</script>

<aside
    id="sidebar"
    class="fixed left-0 top-0 z-50 h-screen w-72 transition-transform duration-500 overflow-y-auto
  {isAdminRole
        ? 'glass-dark border-r border-white/5'
        : 'glass border-r border-slate-100'}
  {showSidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}"
>
    <div
        class="px-8 py-10 flex items-center justify-between"
        data-intro="Ini adalah Logo OOPEDIA. Kamu bisa kembali ke dashboard dengan mengklik logo ini."
        data-step="1"
    >
        <Link
            href={isAdminRole ? "/admin/dashboard" : "/mahasiswa/dashboard"}
            class="flex items-center gap-4 group"
        >
            <div
                class="w-10 h-10 bg-white rounded-2xl flex items-center justify-center shadow-lg p-2 group-hover:rotate-12 transition-transform"
            >
                <img
                    src="/images/logo.png"
                    alt="OOPedia"
                    class="w-full h-auto"
                />
            </div>
            <span
                class="text-2xl font-bold tracking-widest {isAdminRole
                    ? 'text-white'
                    : 'text-slate-900'}">OOPEDIA</span
            >
        </Link>
        <button
            on:click={toggleSidebar}
            class="lg:hidden p-2 rounded-xl {isAdminRole
                ? 'text-slate-400 hover:text-white bg-slate-800'
                : 'text-slate-400 hover:text-slate-900 bg-slate-100'}"
        >
            <i class="fas fa-xmark"></i>
        </button>
    </div>

    <nav
        class="px-5 space-y-10 pb-10"
        data-intro="Gunakan menu navigasi ini untuk menjelajahi fitur-fitur yang tesedia di OOPEDIA."
        data-step="2"
    >
        {#if isAdminRole}
            <div class="space-y-6">
                <div
                    class="px-4 text-[10px] font-bold uppercase tracking-tight text-slate-500 flex items-center gap-3"
                >
                    <span class="w-2 h-0.5 bg-indigo-500/50"></span>
                    Utama
                </div>
                <div class="space-y-2">
                    <SidebarLink
                        href="/admin/dashboard"
                        icon="fas fa-chart-line"
                        active={isActive("/admin/dashboard")}
                        isAdmin={true}>Dashboard</SidebarLink
                    >
                </div>
            </div>

            <div class="space-y-6">
                <div
                    class="px-4 text-[10px] font-bold uppercase tracking-tight text-slate-500 flex items-center gap-3"
                >
                    <span class="w-2 h-0.5 bg-indigo-500/50"></span>
                    Kurikulum
                </div>
                <div class="space-y-2">
                    <SidebarLink
                        href="/admin/materials"
                        icon="fas fa-book"
                        active={$page.url.startsWith("/admin/materials")}
                        isAdmin={true}>Kelola Materi</SidebarLink
                    >
                    <SidebarLink
                        href="/admin/questions"
                        icon="fas fa-vial"
                        active={$page.url.startsWith("/admin/questions")}
                        isAdmin={true}>Kelola Soal</SidebarLink
                    >
                </div>
            </div>

            <div class="space-y-6">
                <div
                    class="px-4 text-[10px] font-bold uppercase tracking-tight text-slate-500 flex items-center gap-3"
                >
                    <span class="w-2 h-0.5 bg-indigo-500/50"></span>
                    Manajemen
                </div>
                <div class="space-y-2">
                    <SidebarLink
                        href="/admin/students"
                        icon="fas fa-user-graduate"
                        active={$page.url.startsWith("/admin/students")}
                        isAdmin={true}>Data Mahasiswa</SidebarLink
                    >
                    {#if userRole === 1}
                        <SidebarLink
                            href="/admin/users"
                            icon="fas fa-users-gear"
                            active={$page.url.startsWith("/admin/users")}
                            isAdmin={true}>Daftar Admin</SidebarLink
                        >
                    {/if}
                    <SidebarLink
                        href="/admin/ueq"
                        icon="fas fa-poll-h"
                        active={$page.url.startsWith("/admin/ueq")}
                        isAdmin={true}>Survey UEQ</SidebarLink
                    >
                </div>
            </div>

            <div class="space-y-6 pt-10 border-t border-slate-800/50">
                <div
                    class="px-4 text-[10px] font-bold uppercase tracking-tight text-slate-500 flex items-center gap-3"
                >
                    <span class="w-2 h-0.5 bg-rose-500/50"></span>
                    Sesi
                </div>
                <div class="space-y-2">
                    <form on:submit|preventDefault={logout}>
                        <button
                            type="submit"
                            class="w-full flex items-center gap-4 px-4 py-3.5 rounded-2xl font-bold tracking-tight transition-all duration-300 group text-slate-500 hover:text-rose-500 hover:bg-slate-800/50"
                        >
                            <div
                                class="w-8 h-8 rounded-xl flex items-center justify-center bg-slate-800 group-hover:bg-rose-500/20 transition-colors duration-300"
                            >
                                <svelte:component this={LogOut} size={18} strokeWidth={2.5} />
                            </div>
                            <span class="flex-1 text-left">Keluar Sistem</span>
                        </button>
                    </form>
                </div>
            </div>
        {:else if isStudentRole}
            <div class="space-y-6">
                <div
                    class="px-4 text-[10px] font-bold uppercase tracking-tight text-slate-500 flex items-center gap-3"
                >
                    <span class="w-2 h-0.5 bg-blue-500/50"></span>
                    Belajar
                </div>
                <div class="space-y-2">
                    <SidebarLink
                        href="/mahasiswa/dashboard"
                        icon="fas fa-shapes"
                        active={$page.url.startsWith("/mahasiswa/dashboard")}
                        >Dashboard</SidebarLink
                    >
                    <SidebarLink
                        href="/mahasiswa/materials"
                        icon="fas fa-book-open-reader"
                        active={($page.url.startsWith("/mahasiswa/materials") ||
                            $page.url.startsWith("/mahasiswa/submaterials")) &&
                            !$page.url.includes("/questions")}
                    >
                        Materi PBO
                    </SidebarLink>
                    <SidebarLink
                        href="/mahasiswa/materials/questions"
                        icon="fas fa-vial-circle-check"
                        active={$page.url.includes("/materials/questions")}
                        >Latihan Soal</SidebarLink
                    >
                </div>
            </div>

            <div class="space-y-6">
                <div
                    class="px-4 text-[10px] font-bold uppercase tracking-tight text-slate-500 flex items-center gap-3"
                >
                    <span class="w-2 h-0.5 bg-blue-500/50"></span>
                    Pencapaian
                </div>
                <div class="space-y-2">
                    <SidebarLink
                        href="/mahasiswa/leaderboard"
                        icon="fas fa-trophy"
                        active={$page.url.startsWith("/mahasiswa/leaderboard")}
                        >Leaderboard</SidebarLink
                    >
                </div>
            </div>

            <div class="space-y-6 pb-10">
                <div
                    class="px-4 text-[10px] font-bold uppercase tracking-tight text-slate-500 flex items-center gap-3"
                >
                    <span class="w-2 h-0.5 bg-blue-500/50"></span>
                    Akun
                </div>
                <div class="space-y-2">
                    <SidebarLink
                        href="/mahasiswa/profile"
                        icon="fas fa-user-astronaut"
                        active={$page.url.startsWith("/mahasiswa/profile")}
                        >Profil Saya</SidebarLink
                    >
                </div>
            </div>

            <div class="space-y-6 pt-10 border-t border-slate-100">
                <div
                    class="px-4 text-[10px] font-bold uppercase tracking-tight text-slate-500 flex items-center gap-3"
                >
                    <span class="w-2 h-0.5 bg-rose-500/50"></span>
                    Sesi
                </div>
                <div class="space-y-2">
                    <form on:submit|preventDefault={logout}>
                        <button
                            type="submit"
                            class="w-full flex items-center gap-4 px-4 py-3.5 rounded-2xl font-bold tracking-tight transition-all duration-300 group text-slate-500 hover:text-rose-600 hover:bg-rose-50"
                        >
                            <div
                                class="w-8 h-8 rounded-xl flex items-center justify-center bg-gray-100 group-hover:bg-rose-100 transition-colors duration-300"
                            >
                                <svelte:component this={LogOut} size={18} strokeWidth={2.5} />
                            </div>
                            <span class="flex-1 text-left">Keluar Sistem</span>
                        </button>
                    </form>
                </div>
            </div>
        {/if}
    </nav>
</aside>
