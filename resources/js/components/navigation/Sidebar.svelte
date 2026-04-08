<script lang="ts">
    import { Link, page, router } from '@inertiajs/svelte';
    import SidebarLink from '@/components/navigation/SidebarLink.svelte';
    import { ROUTES } from '@/utils/route';
    import { sidebarState, closeSidebar } from '@/states/ui';
    import { isAdmin, ROLE } from '@/utils/roles';
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
        LogIn,
        UserPlus,
        Lock,
    } from 'lucide-svelte';
    import { slide } from 'svelte/transition';

    const auth = $derived($page.props['auth'] ?? {});
    const user = $derived(auth.user ?? null);
    const isAdminRole = $derived(!!user && isAdmin(user.role?.role_name));
    const userRole = $derived(user?.role?.role_name ?? null);
    const sidebarOpen = $derived(sidebarState.isOpen);

    const isActive = (url: string) => $page.url === url || $page.url.startsWith(url + '/');

    function logout() {
        router.post('/logout');
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
    class="no-scrollbar fixed inset-y-0 left-0 z-50 w-72 overflow-y-auto border-r-2 border-cosmos-border bg-cosmos-bg transition-all duration-300 ease-in-out lg:w-64
  {sidebarOpen
        ? 'translate-x-0'
        : '-translate-x-full lg:translate-x-0'}"
>
    <div
        class="sticky top-0 z-10 flex items-center justify-between bg-cosmos-bg/80 px-6 py-8 backdrop-blur-md"
        data-intro="Ini adalah Logo OOPEDIA. Kamu bisa kembali ke dashboard dengan mengklik logo ini."
        data-step="1"
    >
        <Link
            href={isAdminRole
                ? ROUTES.ADMIN.DASHBOARD
                : userRole === ROLE.MAHASISWA
                  ? ROUTES.MAHASISWA.DASHBOARD
                  : ROUTES.MAHASISWA.MATERIALS.INDEX}
            class="group flex items-center gap-3"
        >
            <div
                class="flex h-10 w-10 items-center justify-center rounded-2xl border-2 border-cosmos-border bg-white p-2"
            >
                <img src="/images/logo.png" alt="OOPedia" class="h-auto w-full" />
            </div>
            <div class="flex flex-col">
                <span class="font-display text-xl font-black tracking-tighter text-cosmos-text"
                    >OOPEDIA</span
                >
                <span class="text-primary-500 text-[8px] font-bold tracking-[0.2em] uppercase"
                    >Learning System</span
                >
            </div>
        </Link>
        <button
            onclick={() => closeSidebar()}
            aria-label="Tutup sidebar"
            class="group flex h-10 w-10 items-center justify-center rounded-2xl border-2 border-transparent bg-primary-50 text-cosmos-muted transition-all hover:border-rose-200 hover:bg-rose-50 hover:text-rose-500 active:translate-y-0.5 lg:hidden"
        >
            <X size={20} strokeWidth={2.5} />
        </button>
    </div>

    <!-- Decorative line -->
    <div class="mx-6 mb-8 h-px bg-cosmos-border"></div>

    <nav
        aria-label="Navigasi Utama"
        class="space-y-6 px-5 pb-6"
        data-intro="Gunakan menu navigasi ini untuk menjelajahi fitur-fitur yang tesedia di OOPEDIA."
        data-step="2"
    >
        {#if isAdminRole}
            <div class="space-y-6">
                <div class="flex items-center gap-2 px-4">
                    <div class="h-2 w-2 rounded-full bg-primary-500"></div>
                    <span
                        class="text-[9px] font-extrabold tracking-widest text-cosmos-muted uppercase"
                        >Utama</span
                    >
                    <div class="h-0.5 flex-1 bg-cosmos-border"></div>
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
                <div class="flex items-center gap-2 px-4">
                    <div class="h-2 w-2 rounded-full bg-primary-500"></div>
                    <span
                        class="text-[9px] font-extrabold tracking-widest text-cosmos-muted uppercase"
                        >Kurikulum</span
                    >
                    <div class="h-0.5 flex-1 bg-cosmos-border"></div>
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
                <div class="flex items-center gap-2 px-4">
                    <div class="h-2 w-2 rounded-full bg-primary-500"></div>
                    <span
                        class="text-[9px] font-extrabold tracking-widest text-cosmos-muted uppercase"
                        >Manajemen</span
                    >
                    <div class="h-0.5 flex-1 bg-cosmos-border"></div>
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

            <div class="space-y-6 border-t border-cosmos-border pt-10">
                <div
                    class="flex items-center gap-3 px-4 text-[10px] font-bold tracking-tight text-cosmos-muted uppercase"
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
                            class="group flex w-full items-center gap-4 rounded-2xl border-2 border-transparent border-b-4 px-4 py-3 font-bold tracking-tight text-cosmos-muted transition-all duration-100 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 active:translate-y-[2px] active:border-b-0"
                        >
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary-50 transition-colors duration-200 group-hover:bg-rose-100"
                            >
                                <LogOut size={18} strokeWidth={2.5} />
                            </div>
                            <span class="flex-1 text-left">Keluar Sistem</span>
                        </button>
                    </form>
                </div>
            </div>
        {:else}
            <div class="space-y-6">
                <div class="flex items-center gap-2 px-4">
                    <div class="h-2 w-2 rounded-full bg-primary-500"></div>
                    <span
                        class="text-[9px] font-extrabold tracking-widest text-cosmos-muted uppercase"
                        >Belajar</span
                    >
                    <div class="h-0.5 flex-1 bg-cosmos-border"></div>
                </div>
                <div class="space-y-2">
                    {#if userRole === ROLE.MAHASISWA}
                        <SidebarLink
                            href={ROUTES.MAHASISWA.DASHBOARD}
                            icon={LayoutDashboard}
                            active={$page.url.startsWith(ROUTES.MAHASISWA.DASHBOARD)}
                            >Dashboard</SidebarLink
                        >
                    {/if}

                    <div class="space-y-1">
                        <button
                            onclick={() => (isMateriOpen = !isMateriOpen)}
                            aria-expanded={isMateriOpen}
                            aria-controls="materials-submenu"
                            class="group flex w-full items-center gap-4 rounded-2xl border-2 border-transparent border-b-4 px-4 py-3 font-bold tracking-tight transition-all duration-100 select-none active:translate-y-[2px] active:border-b-0
                            {isMateriOpen
                                ? 'bg-primary-500 border-primary-600 text-white'
                                : 'text-cosmos-muted hover:bg-primary-50 hover:text-primary-500 hover:border-primary-200'}"
                        >
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-xl transition-colors duration-200
                                {isMateriOpen
                                    ? 'bg-white/10'
                                    : 'bg-primary-50 group-hover:bg-primary-100/50'}"
                            >
                                <Shapes
                                    size={18}
                                    strokeWidth={2.5}
                                    class={isMateriOpen
                                        ? 'text-white'
                                        : 'text-cosmos-muted group-hover:text-primary-500'}
                                />
                            </div>
                            <span class="flex-1 text-left">Materi PBO</span>
                            <ChevronDown
                                size={16}
                                class="transition-transform duration-200 {isMateriOpen
                                    ? 'rotate-180 text-white'
                                    : 'text-cosmos-muted'}"
                            />
                        </button>

                        {#if isMateriOpen}
                            <div
                                id="materials-submenu"
                                transition:slide={{ duration: 300 }}
                                class="mt-1 space-y-1 pl-4"
                                role="region"
                                aria-label="Daftar Materi PBO"
                            >
                                {#each materials as material}
                                    {#if material.is_locked}
                                        <div
                                            class="group flex w-full cursor-not-allowed items-center gap-4 rounded-2xl px-4 py-3.5 font-bold tracking-tight text-slate-300 opacity-60"
                                            aria-disabled="true"
                                            title="Materi terkunci - selesaikan materi sebelumnya"
                                        >
                                            <div
                                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary-50"
                                                aria-hidden="true"
                                            >
                                                <Lock size={16} strokeWidth={2.5} />
                                            </div>
                                            <span
                                                class="line-clamp-1 flex-1 text-left text-sm font-medium"
                                            >
                                                {material.title}
                                                <span class="sr-only">(Terkunci)</span>
                                            </span>
                                        </div>
                                    {:else}
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
                                    {/if}
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

            {#if userRole === ROLE.MAHASISWA}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 px-4">
                        <div class="h-2 w-2 rounded-full bg-primary-500"></div>
                        <span
                            class="text-[9px] font-extrabold tracking-widest text-cosmos-muted uppercase"
                            >Pencapaian</span
                        >
                        <div class="h-0.5 flex-1 bg-cosmos-border"></div>
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
            {/if}

            {#if userRole === ROLE.MAHASISWA}
                <div class="space-y-6 pb-10">
                    <div class="flex items-center gap-2 px-4">
                        <div class="h-2 w-2 rounded-full bg-primary-500"></div>
                        <span
                            class="text-[9px] font-extrabold tracking-widest text-cosmos-muted uppercase"
                            >Akun</span
                        >
                        <div class="h-0.5 flex-1 bg-cosmos-border"></div>
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
            {/if}

            <div class="space-y-6 border-t border-slate-100 pt-10">
                <div class="flex items-center gap-2 px-4">
                    <div class="h-2 w-2 rounded-full bg-rose-500"></div>
                    <span
                        class="text-[9px] font-extrabold tracking-widest text-cosmos-muted uppercase"
                        >Sesi</span
                    >
                    <div class="h-0.5 flex-1 bg-cosmos-border"></div>
                </div>
                <div class="space-y-2">
                    {#if user}
                        <form
                            onsubmit={(e) => {
                                e.preventDefault();
                                logout();
                            }}
                        >
                            <button
                                type="submit"
                                class="group flex w-full items-center gap-4 rounded-2xl border-2 border-transparent border-b-4 px-4 py-3 font-bold tracking-tight text-cosmos-muted transition-all duration-100 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 active:translate-y-[2px] active:border-b-0"
                            >
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary-50 transition-colors duration-200 group-hover:bg-rose-100"
                                >
                                    <LogOut size={18} strokeWidth={2.5} />
                                </div>
                                <span class="flex-1 text-left">Keluar Sistem</span>
                            </button>
                        </form>
                    {:else}
                        <SidebarLink
                            href={ROUTES.AUTH.LOGIN}
                            icon={LogIn}
                            active={$page.url === ROUTES.AUTH.LOGIN}
                        >
                            Masuk
                        </SidebarLink>
                        <SidebarLink
                            href={ROUTES.AUTH.REGISTER}
                            icon={UserPlus}
                            active={$page.url === ROUTES.AUTH.REGISTER}
                        >
                            Daftar Akun
                        </SidebarLink>
                    {/if}
                </div>
            </div>
        {/if}
    </nav>
</aside>
