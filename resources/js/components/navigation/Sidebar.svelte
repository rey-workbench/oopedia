<script lang="ts">
    import { Link, page, router } from "@inertiajs/svelte";
    import SidebarLink from "@/components/navigation/SidebarLink.svelte";
    import { ROUTES } from "@/utils/route";
    import { sidebarOpen } from "@/stores/sidebar";
    import { isAdmin, isStudent, ROLE } from "@/utils/roles";
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
        ChevronDown,
    } from "lucide-svelte";
    import { slide } from "svelte/transition";

    const auth = $derived($page.props.auth ?? {});
    const user = $derived(auth.user ?? null);
    const isAdminRole = $derived(!!user && isAdmin(user.role_id));
    const isStudentRole = $derived(!!user && isStudent(user.role_id));
    const userRole = $derived(user?.role_id ?? null);

    const isActive = (url: string) =>
        $page.url === url || $page.url.startsWith(url + "/");

    function logout() {
        router.post("/logout");
    }

    function closeSidebar() {
        sidebarOpen.set(false);
    }

    let isMateriOpen = $state(
        isActive(ROUTES.MAHASISWA.MATERIALS.INDEX) ||
            $page.url.startsWith("/mahasiswa/submaterials") ||
            $page.url.startsWith("/mahasiswa/materials/"),
    );

    const materials = $derived($page.props.sidebar_materials ?? []);
</script>

<aside
    id="sidebar"
    class="fixed left-0 top-0 z-50 h-screen w-64 transition-transform duration-500 overflow-y-auto glass border-r border-slate-100 no-scrollbar
  {$sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}"
>
    <div
        class="px-6 py-6 flex items-center justify-between"
        data-intro="Ini adalah Logo OOPEDIA. Kamu bisa kembali ke dashboard dengan mengklik logo ini."
        data-step="1"
    >
        <Link
            href={isAdminRole
                ? ROUTES.ADMIN.DASHBOARD
                : ROUTES.MAHASISWA.DASHBOARD}
            class="flex items-center gap-3 group"
        >
            <div
                class="w-8 h-8 bg-white rounded-xl flex items-center justify-center shadow-lg p-1.5 group-hover:rotate-12 transition-transform"
            >
                <img
                    src="/images/logo.png"
                    alt="OOPedia"
                    class="w-full h-auto"
                />
            </div>
            <span class="text-lg font-bold tracking-widest text-slate-900"
                >OOPEDIA</span
            >
        </Link>
        <button
            onclick={closeSidebar}
            aria-label="Tutup sidebar"
            class="lg:hidden p-2 rounded-xl text-slate-400 hover:text-slate-900 bg-slate-100"
        >
            <X size={20} />
        </button>
    </div>

    <nav
        aria-label="Navigasi Utama"
        class="px-5 space-y-6 pb-6"
        data-intro="Gunakan menu navigasi ini untuk menjelajahi fitur-fitur yang tesedia di OOPEDIA."
        data-step="2"
    >
        {#if isAdminRole}
            <div class="space-y-6">
                <div
                    class="px-4 text-[10px] font-bold uppercase tracking-tight text-slate-500 flex items-center gap-3"
                >
                    <span class="w-2 h-0.5 bg-primary-500/50"></span>
                    Utama
                </div>
                <div class="space-y-2">
                    <SidebarLink
                        href={ROUTES.ADMIN.DASHBOARD}
                        icon={LayoutDashboard}
                        active={isActive(ROUTES.ADMIN.DASHBOARD)}
                        >Dashboard</SidebarLink
                    >
                </div>
            </div>

            <div class="space-y-6">
                <div
                    class="px-4 text-[10px] font-bold uppercase tracking-tight text-slate-500 flex items-center gap-3"
                >
                    <span class="w-2 h-0.5 bg-primary-500/50"></span>
                    Kurikulum
                </div>
                <div class="space-y-2">
                    <SidebarLink
                        href={ROUTES.ADMIN.MATERIALS.INDEX}
                        icon={BookOpen}
                        active={$page.url.startsWith(
                            ROUTES.ADMIN.MATERIALS.INDEX,
                        )}>Kelola Materi</SidebarLink
                    >
                    <SidebarLink
                        href={ROUTES.ADMIN.QUESTIONS.INDEX}
                        icon={SquareActivity}
                        active={$page.url.startsWith(
                            ROUTES.ADMIN.QUESTIONS.INDEX,
                        )}>Kelola Soal</SidebarLink
                    >
                </div>
            </div>

            <div class="space-y-6">
                <div
                    class="px-4 text-[10px] font-bold uppercase tracking-tight text-slate-500 flex items-center gap-3"
                >
                    <span class="w-2 h-0.5 bg-primary-500/50"></span>
                    Manajemen
                </div>
                <div class="space-y-2">
                    <SidebarLink
                        href={ROUTES.ADMIN.STUDENTS.INDEX}
                        icon={GraduationCap}
                        active={$page.url.startsWith(
                            ROUTES.ADMIN.STUDENTS.INDEX,
                        )}>Data Mahasiswa</SidebarLink
                    >
                    {#if userRole === ROLE.SUPERADMIN}
                        <SidebarLink
                            href={ROUTES.ADMIN.USERS.INDEX}
                            icon={Settings}
                            active={$page.url.startsWith(
                                ROUTES.ADMIN.USERS.INDEX,
                            )}>Daftar Admin</SidebarLink
                        >
                    {/if}
                    {#if userRole === ROLE.SUPERADMIN}
                        <SidebarLink
                            href={ROUTES.ADMIN.UEQ.INDEX}
                            icon={MessageSquareQuote}
                            active={$page.url.startsWith(
                                ROUTES.ADMIN.UEQ.INDEX,
                            )}>Survey UEQ</SidebarLink
                        >
                    {/if}
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
                    <form
                        onsubmit={(e) => {
                            e.preventDefault();
                            logout();
                        }}
                    >
                        <button
                            type="submit"
                            class="w-full flex items-center gap-4 px-4 py-3.5 rounded-2xl font-bold tracking-tight transition-all duration-300 group text-slate-500 hover:text-rose-600 hover:bg-rose-50"
                        >
                            <div
                                class="w-8 h-8 rounded-xl flex items-center justify-center bg-gray-100 group-hover:bg-rose-100 transition-colors duration-300"
                            >
                                <LogOut size={18} strokeWidth={2.5} />
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
                    <span class="w-2 h-0.5 bg-primary-500/50"></span>
                    Belajar
                </div>
                <div class="space-y-2">
                    <SidebarLink
                        href={ROUTES.MAHASISWA.DASHBOARD}
                        icon={LayoutDashboard}
                        active={$page.url.startsWith(
                            ROUTES.MAHASISWA.DASHBOARD,
                        )}>Dashboard</SidebarLink
                    >

                    <div class="space-y-1">
                        <button
                            onclick={() => (isMateriOpen = !isMateriOpen)}
                            class="w-full flex items-center gap-4 px-4 py-3.5 rounded-2xl font-bold tracking-tight transition-all duration-300 group
                            {isMateriOpen
                                ? 'text-primary-600 bg-primary-50'
                                : 'text-slate-500 hover:text-accent-600 hover:bg-accent-50'}"
                        >
                            <div
                                class="w-8 h-8 rounded-xl flex items-center justify-center transition-colors duration-300
                                {isMateriOpen
                                    ? 'bg-primary-100'
                                    : 'bg-gray-100 group-hover:bg-accent-100'}"
                            >
                                <Shapes
                                    size={18}
                                    strokeWidth={2.5}
                                    class={isMateriOpen
                                        ? "text-primary-600"
                                        : "text-slate-400 group-hover:text-accent-600"}
                                />
                            </div>
                            <span class="flex-1 text-left">Materi PBO</span>
                            <ChevronDown
                                size={16}
                                class="transition-transform duration-300 {isMateriOpen
                                    ? 'rotate-180 text-primary-600'
                                    : 'text-slate-400'}"
                            />
                        </button>

                        {#if isMateriOpen}
                            <div
                                transition:slide={{ duration: 300 }}
                                class="pl-4 space-y-1 mt-1"
                            >
                                {#each materials as material}
                                    <SidebarLink
                                        href="/mahasiswa/materials/{material.id}"
                                        icon={BookOpen}
                                        active={$page.url.startsWith(
                                            `/mahasiswa/materials/${material.id}`,
                                        )}
                                    >
                                        <span
                                            class="text-sm line-clamp-1 font-medium"
                                            >{material.title}</span
                                        >
                                    </SidebarLink>
                                {/each}
                            </div>
                        {/if}
                    </div>

                    <SidebarLink
                        href={ROUTES.MAHASISWA.MATERIALS.QUESTIONS.CATALOG}
                        icon={SquareActivity}
                        active={$page.url.includes("/materials/questions")}
                    >
                        Latihan Soal
                    </SidebarLink>
                </div>
            </div>

            <div class="space-y-6">
                <div
                    class="px-4 text-[10px] font-bold uppercase tracking-tight text-slate-500 flex items-center gap-3"
                >
                    <span class="w-2 h-0.5 bg-primary-500/50"></span>
                    Pencapaian
                </div>
                <div class="space-y-2">
                    <SidebarLink
                        href={ROUTES.MAHASISWA.LEADERBOARD}
                        icon={Trophy}
                        active={$page.url.startsWith(
                            ROUTES.MAHASISWA.LEADERBOARD,
                        )}>Leaderboard</SidebarLink
                    >
                </div>
            </div>

            <div class="space-y-6 pb-10">
                <div
                    class="px-4 text-[10px] font-bold uppercase tracking-tight text-slate-500 flex items-center gap-3"
                >
                    <span class="w-2 h-0.5 bg-primary-500/50"></span>
                    Akun
                </div>
                <div class="space-y-2">
                    <SidebarLink
                        href={ROUTES.MAHASISWA.PROFILE}
                        icon={UserRound}
                        active={$page.url.startsWith(ROUTES.MAHASISWA.PROFILE)}
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
                    <form
                        onsubmit={(e) => {
                            e.preventDefault();
                            logout();
                        }}
                    >
                        <button
                            type="submit"
                            class="w-full flex items-center gap-4 px-4 py-3.5 rounded-2xl font-bold tracking-tight transition-all duration-300 group text-slate-500 hover:text-rose-600 hover:bg-rose-50"
                        >
                            <div
                                class="w-8 h-8 rounded-xl flex items-center justify-center bg-gray-100 group-hover:bg-rose-100 transition-colors duration-300"
                            >
                                <LogOut size={18} strokeWidth={2.5} />
                            </div>
                            <span class="flex-1 text-left">Keluar Sistem</span>
                        </button>
                    </form>
                </div>
            </div>
        {/if}
    </nav>
</aside>
