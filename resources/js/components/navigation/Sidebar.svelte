<script lang="ts">
    import { Link, page, router } from '@inertiajs/svelte';
    import SidebarLink from '@/components/navigation/SidebarLink.svelte';
    import { ROUTES } from '@/utils/route';
    import { sidebarOpen } from '@/stores/sidebar';
    import { isAdmin, isStudent, ROLE } from '@/utils/roles';
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
    } from 'lucide-svelte';
    import { slide } from 'svelte/transition';

    const auth = $derived($page.props['auth'] ?? {});
    const user = $derived(auth.user ?? null);
    const isAdminRole = $derived(!!user && isAdmin(user.role_id));
    const isStudentRole = $derived(!!user && isStudent(user.role_id));
    const userRole = $derived(user?.role_id ?? null);

    const isActive = (url: string) => $page.url === url || $page.url.startsWith(url + '/');

    function logout() {
        router.post('/logout');
    }

    function closeSidebar() {
        sidebarOpen.set(false);
    }

    let isMateriOpen = $state(
        isActive(ROUTES.MAHASISWA.MATERIALS.INDEX) ||
            $page.url.startsWith('/mahasiswa/submaterials') ||
            $page.url.startsWith('/mahasiswa/materials/')
    );

    const materials = $derived($page.props['sidebar_materials'] ?? []);
</script>

<aside
    id="sidebar"
    class="glass no-scrollbar fixed top-0 left-0 z-50 h-screen w-64 overflow-y-auto border-r border-slate-100 transition-transform duration-500
  {$sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}"
>
    <div
        class="flex items-center justify-between px-6 py-6"
        data-intro="Ini adalah Logo OOPEDIA. Kamu bisa kembali ke dashboard dengan mengklik logo ini."
        data-step="1"
    >
        <Link
            href={isAdminRole ? ROUTES.ADMIN.DASHBOARD : ROUTES.MAHASISWA.DASHBOARD}
            class="group flex items-center gap-3"
        >
            <div
                class="flex h-8 w-8 items-center justify-center rounded-xl bg-white p-1.5 shadow-lg transition-transform group-hover:rotate-12"
            >
                <img src="/images/logo.png" alt="OOPedia" class="h-auto w-full" />
            </div>
            <span class="text-lg font-bold tracking-widest text-slate-900">OOPEDIA</span>
        </Link>
        <button
            onclick={closeSidebar}
            aria-label="Tutup sidebar"
            class="rounded-xl bg-slate-100 p-2 text-slate-400 hover:text-slate-900 lg:hidden"
        >
            <X size={20} />
        </button>
    </div>

    <nav
        aria-label="Navigasi Utama"
        class="space-y-6 px-5 pb-6"
        data-intro="Gunakan menu navigasi ini untuk menjelajahi fitur-fitur yang tesedia di OOPEDIA."
        data-step="2"
    >
        {#if isAdminRole}
            <div class="space-y-6">
                <div
                    class="flex items-center gap-3 px-4 text-[10px] font-bold tracking-tight text-slate-500 uppercase"
                >
                    <span class="bg-primary-500/50 h-0.5 w-2"></span>
                    Utama
                </div>
                <div class="space-y-2">
                    <SidebarLink
                        href={ROUTES.ADMIN.DASHBOARD}
                        icon={LayoutDashboard}
                        active={isActive(ROUTES.ADMIN.DASHBOARD)}>Dashboard</SidebarLink
                    >
                </div>
            </div>

            <div class="space-y-6">
                <div
                    class="flex items-center gap-3 px-4 text-[10px] font-bold tracking-tight text-slate-500 uppercase"
                >
                    <span class="bg-primary-500/50 h-0.5 w-2"></span>
                    Kurikulum
                </div>
                <div class="space-y-2">
                    <SidebarLink
                        href={ROUTES.ADMIN.MATERIALS.INDEX}
                        icon={BookOpen}
                        active={$page.url.startsWith(ROUTES.ADMIN.MATERIALS.INDEX)}
                        >Kelola Materi</SidebarLink
                    >
                    <SidebarLink
                        href={ROUTES.ADMIN.QUESTIONS.INDEX}
                        icon={SquareActivity}
                        active={$page.url.startsWith(ROUTES.ADMIN.QUESTIONS.INDEX)}
                        >Kelola Soal</SidebarLink
                    >
                </div>
            </div>

            <div class="space-y-6">
                <div
                    class="flex items-center gap-3 px-4 text-[10px] font-bold tracking-tight text-slate-500 uppercase"
                >
                    <span class="bg-primary-500/50 h-0.5 w-2"></span>
                    Manajemen
                </div>
                <div class="space-y-2">
                    <SidebarLink
                        href={ROUTES.ADMIN.STUDENTS.INDEX}
                        icon={GraduationCap}
                        active={$page.url.startsWith(ROUTES.ADMIN.STUDENTS.INDEX)}
                        >Data Mahasiswa</SidebarLink
                    >
                    {#if userRole === ROLE.SUPERADMIN}
                        <SidebarLink
                            href={ROUTES.ADMIN.USERS.INDEX}
                            icon={Settings}
                            active={$page.url.startsWith(ROUTES.ADMIN.USERS.INDEX)}
                            >Daftar Admin</SidebarLink
                        >
                    {/if}
                    {#if userRole === ROLE.SUPERADMIN}
                        <SidebarLink
                            href={ROUTES.ADMIN.UEQ.INDEX}
                            icon={MessageSquareQuote}
                            active={$page.url.startsWith(ROUTES.ADMIN.UEQ.INDEX)}
                            >Survey UEQ</SidebarLink
                        >
                    {/if}
                </div>
            </div>

            <div class="space-y-6 border-t border-slate-100 pt-10">
                <div
                    class="flex items-center gap-3 px-4 text-[10px] font-bold tracking-tight text-slate-500 uppercase"
                >
                    <span class="h-0.5 w-2 bg-rose-500/50"></span>
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
                            class="group flex w-full items-center gap-4 rounded-2xl px-4 py-3.5 font-bold tracking-tight text-slate-500 transition-all duration-300 hover:bg-rose-50 hover:text-rose-600"
                        >
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-gray-100 transition-colors duration-300 group-hover:bg-rose-100"
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
                    class="flex items-center gap-3 px-4 text-[10px] font-bold tracking-tight text-slate-500 uppercase"
                >
                    <span class="bg-primary-500/50 h-0.5 w-2"></span>
                    Belajar
                </div>
                <div class="space-y-2">
                    <SidebarLink
                        href={ROUTES.MAHASISWA.DASHBOARD}
                        icon={LayoutDashboard}
                        active={$page.url.startsWith(ROUTES.MAHASISWA.DASHBOARD)}
                        >Dashboard</SidebarLink
                    >

                    <div class="space-y-1">
                        <button
                            onclick={() => (isMateriOpen = !isMateriOpen)}
                            class="group flex w-full items-center gap-4 rounded-2xl px-4 py-3.5 font-bold tracking-tight transition-all duration-300
                            {isMateriOpen
                                ? 'text-primary-600 bg-primary-50'
                                : 'hover:text-accent-600 hover:bg-accent-50 text-slate-500'}"
                        >
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-xl transition-colors duration-300
                                {isMateriOpen
                                    ? 'bg-primary-100'
                                    : 'group-hover:bg-accent-100 bg-gray-100'}"
                            >
                                <Shapes
                                    size={18}
                                    strokeWidth={2.5}
                                    class={isMateriOpen
                                        ? 'text-primary-600'
                                        : 'group-hover:text-accent-600 text-slate-400'}
                                />
                            </div>
                            <span class="flex-1 text-left">Materi PBO</span>
                            <ChevronDown
                                size={16}
                                class="transition-transform duration-300 {isMateriOpen
                                    ? 'text-primary-600 rotate-180'
                                    : 'text-slate-400'}"
                            />
                        </button>

                        {#if isMateriOpen}
                            <div transition:slide={{ duration: 300 }} class="mt-1 space-y-1 pl-4">
                                {#each materials as material}
                                    <SidebarLink
                                        href="/mahasiswa/materials/{material.id}"
                                        icon={BookOpen}
                                        active={$page.url.startsWith(
                                            `/mahasiswa/materials/${material.id}`
                                        )}
                                    >
                                        <span class="line-clamp-1 text-sm font-medium"
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
                        active={$page.url.includes('/materials/questions')}
                    >
                        Latihan Soal
                    </SidebarLink>
                </div>
            </div>

            <div class="space-y-6">
                <div
                    class="flex items-center gap-3 px-4 text-[10px] font-bold tracking-tight text-slate-500 uppercase"
                >
                    <span class="bg-primary-500/50 h-0.5 w-2"></span>
                    Pencapaian
                </div>
                <div class="space-y-2">
                    <SidebarLink
                        href={ROUTES.MAHASISWA.LEADERBOARD}
                        icon={Trophy}
                        active={$page.url.startsWith(ROUTES.MAHASISWA.LEADERBOARD)}
                        >Leaderboard</SidebarLink
                    >
                </div>
            </div>

            <div class="space-y-6 pb-10">
                <div
                    class="flex items-center gap-3 px-4 text-[10px] font-bold tracking-tight text-slate-500 uppercase"
                >
                    <span class="bg-primary-500/50 h-0.5 w-2"></span>
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

            <div class="space-y-6 border-t border-slate-100 pt-10">
                <div
                    class="flex items-center gap-3 px-4 text-[10px] font-bold tracking-tight text-slate-500 uppercase"
                >
                    <span class="h-0.5 w-2 bg-rose-500/50"></span>
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
                            class="group flex w-full items-center gap-4 rounded-2xl px-4 py-3.5 font-bold tracking-tight text-slate-500 transition-all duration-300 hover:bg-rose-50 hover:text-rose-600"
                        >
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-gray-100 transition-colors duration-300 group-hover:bg-rose-100"
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
