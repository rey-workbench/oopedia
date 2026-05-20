<script lang="ts">
    import { Link, page, router } from '@inertiajs/svelte';
    import SidebarLink from '@/components/navigation/SidebarLink.svelte';
    import { ROUTES } from '@/utils/route';
    import { closeSidebar } from '@/states/ui';
    import { isAdmin, ROLE } from '@/utils/roles';
    import {
        LayoutDashboard,
        BookOpen,
        SquareActivity,
        GraduationCap,
        Settings,
        MessageSquareQuote,
        HelpCircle,
        Rocket,
        CheckCircle2,
        X,
        Shapes,
        Trophy,
        LogOut,
        LogIn,
        UserPlus,
        Brain,
        Clock,
    } from 'lucide-svelte';
    import { getTourIdFromUrl, registerGlobalTutorials } from '@/tutorial';
    import { onMount } from 'svelte';
    import { tutorialState } from '@/states/ui/tutorialState.svelte';

    onMount(() => {
        registerGlobalTutorials();
    });

    const auth = $derived(page.props['auth'] ?? {});
    const user = $derived((auth.user as import('@/types').User) ?? null);
    const isAdminRole = $derived(!!user && isAdmin(user.role?.role_name));
    const userRole = $derived(user?.role?.role_name ?? 'guest');

    const isActive = (url: string) => page.url === url || page.url.startsWith(url + '/');

    function logout() {
        router.post('/logout');
    }
</script>

<aside
    id="sidebar"
    class="no-scrollbar border-cosmos-border bg-cosmos-bg fixed inset-y-0 left-0 z-50 w-72 overflow-y-auto border-r-2 lg:w-64"
>
    <div
        class="bg-cosmos-bg/80 sticky top-0 z-10 flex items-center justify-between px-6 py-8 backdrop-blur-md"
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
                class="border-cosmos-border flex h-10 w-10 items-center justify-center rounded-2xl border-2 bg-white p-2"
            >
                <img src="/images/logo.png" alt="OOPedia" class="h-auto w-full" />
            </div>
            <div class="flex flex-col">
                <span class="font-display text-cosmos-text text-xl font-black tracking-tighter"
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
            class="group bg-primary-50 text-cosmos-muted flex h-10 w-10 items-center justify-center rounded-2xl border-2 border-transparent transition-all hover:border-rose-200 hover:bg-rose-50 hover:text-rose-500 active:translate-y-0.5"
        >
            <X size={20} strokeWidth={2.5} />
        </button>
    </div>

    <!-- Decorative line -->
    <div class="bg-cosmos-border mx-6 mb-8 h-px"></div>

    <nav aria-label="Navigasi Utama" class="space-y-6 px-5 pb-6">
        {#if isAdminRole}
            <div class="space-y-6">
                <div class="flex items-center gap-2 px-4">
                    <div class="bg-primary-500 h-2 w-2 rounded-full"></div>
                    <span class="text-cosmos-muted text-xs font-extrabold tracking-widest uppercase"
                        >Utama</span
                    >
                    <div class="bg-cosmos-border h-0.5 flex-1"></div>
                </div>
                <div class="space-y-2">
                    <SidebarLink
                        id="sidebar-admin-dashboard"
                        href={ROUTES.ADMIN.DASHBOARD}
                        icon={LayoutDashboard}
                        active={isActive(ROUTES.ADMIN.DASHBOARD)}>Dashboard</SidebarLink
                    >
                </div>
            </div>

            <div class="space-y-6">
                <div class="flex items-center gap-2 px-4">
                    <div class="bg-primary-500 h-2 w-2 rounded-full"></div>
                    <span class="text-cosmos-muted text-xs font-extrabold tracking-widest uppercase"
                        >Kurikulum</span
                    >
                    <div class="bg-cosmos-border h-0.5 flex-1"></div>
                </div>
                <div class="space-y-2" id="sidebar-admin-curriculum">
                    <SidebarLink
                        id="sidebar-admin-materials"
                        href={ROUTES.ADMIN.MATERIALS.INDEX}
                        icon={BookOpen}
                        active={page.url.startsWith(ROUTES.ADMIN.MATERIALS.INDEX)}
                        >Kelola Materi</SidebarLink
                    >
                    <SidebarLink
                        id="sidebar-admin-questions"
                        href={ROUTES.ADMIN.QUESTIONS.INDEX}
                        icon={SquareActivity}
                        active={page.url.startsWith(ROUTES.ADMIN.QUESTIONS.INDEX)}
                        >Kelola Soal</SidebarLink
                    >
                    <SidebarLink
                        id="sidebar-admin-adaptive"
                        href={ROUTES.ADMIN.ADAPTIVE_RULES.INDEX}
                        icon={Brain}
                        active={page.url.startsWith(ROUTES.ADMIN.ADAPTIVE_RULES.INDEX)}
                        >Engine Adaptif</SidebarLink
                    >
                </div>
            </div>

            <div class="space-y-6">
                <div class="flex items-center gap-2 px-4">
                    <div class="bg-primary-500 h-2 w-2 rounded-full"></div>
                    <span class="text-cosmos-muted text-xs font-extrabold tracking-widest uppercase"
                        >Manajemen</span
                    >
                    <div class="bg-cosmos-border h-0.5 flex-1"></div>
                </div>
                <div class="space-y-2">
                    <SidebarLink
                        id="sidebar-admin-students"
                        href={ROUTES.ADMIN.STUDENTS.INDEX}
                        icon={GraduationCap}
                        active={page.url.startsWith(ROUTES.ADMIN.STUDENTS.INDEX)}
                        >Data Mahasiswa</SidebarLink
                    >
                    {#if userRole === ROLE.SUPERADMIN}
                        <SidebarLink
                            id="sidebar-admin-users"
                            href={ROUTES.ADMIN.USERS.INDEX}
                            icon={Settings}
                            active={page.url.startsWith(ROUTES.ADMIN.USERS.INDEX)}
                            >Daftar Admin</SidebarLink
                        >
                        <SidebarLink
                            id="sidebar-admin-pending"
                            href={ROUTES.ADMIN.PENDING_ADMINS.INDEX}
                            icon={Clock}
                            active={page.url.startsWith(ROUTES.ADMIN.PENDING_ADMINS.INDEX)}
                        >
                            <div class="flex flex-1 items-center justify-between">
                                <span>Permohonan Akses</span>
                                {#if (page.props as any).pending_admins_count > 0}
                                    <span
                                        class="flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white shadow-sm"
                                    >
                                        {(page.props as any).pending_admins_count}
                                    </span>
                                {/if}
                            </div>
                        </SidebarLink>
                    {/if}
                    {#if userRole === ROLE.SUPERADMIN}
                        <SidebarLink
                            id="sidebar-admin-ueq"
                            href={ROUTES.ADMIN.UEQ.INDEX}
                            icon={MessageSquareQuote}
                            active={page.url.startsWith(ROUTES.ADMIN.UEQ.INDEX)}
                            >Survey UEQ</SidebarLink
                        >
                        <SidebarLink
                            id="sidebar-admin-mslq"
                            href={ROUTES.ADMIN.MSLQ.INDEX}
                            icon={MessageSquareQuote}
                            active={page.url.startsWith(ROUTES.ADMIN.MSLQ.INDEX)}
                            >Survey MSLQ</SidebarLink
                        >
                        <SidebarLink
                            id="sidebar-admin-sus"
                            href={ROUTES.ADMIN.SUS.INDEX}
                            icon={MessageSquareQuote}
                            active={page.url.startsWith(ROUTES.ADMIN.SUS.INDEX)}
                            >Survey SUS</SidebarLink
                        >
                    {/if}
                </div>
            </div>
            <div class="border-cosmos-border space-y-6 border-t pt-10">
                <div
                    class="text-cosmos-muted flex items-center gap-3 px-4 text-xs font-bold tracking-tight uppercase"
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
                            id="sidebar-admin-logout"
                            type="submit"
                            class="group text-cosmos-muted flex w-full items-center gap-4 rounded-2xl border-2 border-b-4 border-transparent px-4 py-3 font-bold tracking-tight transition-all duration-100 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 active:translate-y-[2px] active:border-b-0"
                        >
                            <div
                                class="bg-primary-50 flex h-8 w-8 items-center justify-center rounded-xl transition-colors duration-200 group-hover:bg-rose-100"
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
                    <div class="bg-primary-500 h-2 w-2 rounded-full"></div>
                    <span class="text-cosmos-muted text-xs font-extrabold tracking-widest uppercase"
                        >Belajar</span
                    >
                    <div class="bg-cosmos-border h-0.5 flex-1"></div>
                </div>
                <div class="space-y-2">
                    {#if userRole === ROLE.MAHASISWA}
                        <SidebarLink
                            id="sidebar-mahasiswa-dashboard"
                            href={ROUTES.MAHASISWA.DASHBOARD}
                            icon={LayoutDashboard}
                            active={page.url.startsWith(ROUTES.MAHASISWA.DASHBOARD)}
                            >Dashboard</SidebarLink
                        >
                    {/if}

                    <SidebarLink
                        id="sidebar-materials"
                        href={ROUTES.MAHASISWA.MATERIALS.INDEX}
                        icon={Shapes}
                        active={page.url.startsWith(ROUTES.MAHASISWA.MATERIALS.INDEX) &&
                            !page.url.includes('/questions')}
                    >
                        Materi PBO
                    </SidebarLink>

                    <SidebarLink
                        id="sidebar-quiz"
                        href={ROUTES.MAHASISWA.MATERIALS.QUESTIONS.CATALOG}
                        icon={SquareActivity}
                        active={page.url.includes('/materials/questions')}
                    >
                        Latihan Soal
                    </SidebarLink>
                </div>
            </div>

            {#if userRole === ROLE.MAHASISWA}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 px-4">
                        <div class="bg-primary-500 h-2 w-2 rounded-full"></div>
                        <span
                            class="text-cosmos-muted text-xs font-extrabold tracking-widest uppercase"
                            >Pencapaian & Progres</span
                        >
                        <div class="bg-cosmos-border h-0.5 flex-1"></div>
                    </div>
                    <div class="space-y-2">
                        <SidebarLink
                            id="sidebar-inprogress"
                            href={ROUTES.MAHASISWA.IN_PROGRESS}
                            icon={Rocket}
                            active={page.url === ROUTES.MAHASISWA.IN_PROGRESS}
                            >Sedang Dipelajari</SidebarLink
                        >
                        <SidebarLink
                            id="sidebar-completed"
                            href={ROUTES.MAHASISWA.COMPLETED}
                            icon={CheckCircle2}
                            active={page.url === ROUTES.MAHASISWA.COMPLETED}
                            >Materi Selesai</SidebarLink
                        >
                        <SidebarLink
                            id="sidebar-leaderboard"
                            href={ROUTES.MAHASISWA.LEADERBOARD}
                            icon={Trophy}
                            active={page.url.startsWith(ROUTES.MAHASISWA.LEADERBOARD)}
                            >Leaderboard</SidebarLink
                        >
                    </div>
                </div>
            {/if}

            {#if userRole === ROLE.MAHASISWA}
                <div class="space-y-6 pb-10">
                    <div class="flex items-center gap-2 px-4">
                        <div class="bg-primary-500 h-2 w-2 rounded-full"></div>
                        <span
                            class="text-cosmos-muted text-xs font-extrabold tracking-widest uppercase"
                            >Akun</span
                        >
                        <div class="bg-cosmos-border h-0.5 flex-1"></div>
                    </div>
                    <div class="space-y-2">
                        <SidebarLink
                            id="sidebar-mahasiswa-mslq"
                            href={ROUTES.MAHASISWA.MSLQ.CREATE}
                            icon={MessageSquareQuote}
                            active={page.url.startsWith(ROUTES.MAHASISWA.MSLQ.CREATE)}
                            >Kuesioner MSLQ</SidebarLink
                        >
                        <!-- <SidebarLink
                            id="sidebar-mahasiswa-ueq"
                            href={ROUTES.MAHASISWA.UEQ.CREATE}
                            icon={MessageSquareQuote}
                            active={page.url.startsWith(ROUTES.MAHASISWA.UEQ.CREATE)}
                            >Kuesioner UEQ</SidebarLink
                        > -->
                        <SidebarLink
                            id="sidebar-mahasiswa-sus"
                            href={ROUTES.MAHASISWA.SUS.CREATE}
                            icon={MessageSquareQuote}
                            active={page.url.startsWith(ROUTES.MAHASISWA.SUS.CREATE)}
                            >Kuesioner SUS</SidebarLink
                        >
                    </div>
                </div>
            {/if}

            <div class="space-y-6 border-t border-slate-100 pt-10">
                <div class="flex items-center gap-2 px-4">
                    <div class="h-2 w-2 rounded-full bg-rose-500"></div>
                    <span class="text-cosmos-muted text-xs font-extrabold tracking-widest uppercase"
                        >Sesi</span
                    >
                    <div class="bg-cosmos-border h-0.5 flex-1"></div>
                </div>
                <div class="space-y-2">
                    <button
                        id="sidebar-tutorial-button"
                        onclick={() => {
                            const tourId = getTourIdFromUrl(page.url, isAdminRole);
                            tutorialState.startTour(tourId, true);
                        }}
                        class="group text-cosmos-muted hover:border-primary-200 hover:bg-primary-50 hover:text-primary-600 flex w-full items-center gap-4 rounded-2xl border-2 border-b-4 border-transparent px-4 py-3 font-bold tracking-tight transition-all duration-100 active:translate-y-[2px] active:border-b-0"
                    >
                        <div
                            class="bg-primary-50 group-hover:bg-primary-100 flex h-8 w-8 items-center justify-center rounded-xl transition-colors duration-200"
                        >
                            <HelpCircle size={18} strokeWidth={2.5} />
                        </div>
                        <span class="flex-1 text-left">Bantuan Tutorial</span>
                    </button>

                    {#if user}
                        <form
                            onsubmit={(e) => {
                                e.preventDefault();
                                logout();
                            }}
                        >
                            <button
                                id="sidebar-logout-button"
                                type="submit"
                                class="group text-cosmos-muted flex w-full items-center gap-4 rounded-2xl border-2 border-b-4 border-transparent px-4 py-3 font-bold tracking-tight transition-all duration-100 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 active:translate-y-[2px] active:border-b-0"
                            >
                                <div
                                    class="bg-primary-50 flex h-8 w-8 items-center justify-center rounded-xl transition-colors duration-200 group-hover:bg-rose-100"
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
                            active={page.url === ROUTES.AUTH.LOGIN}
                        >
                            Masuk
                        </SidebarLink>
                        <SidebarLink
                            href={ROUTES.AUTH.REGISTER}
                            icon={UserPlus}
                            active={page.url === ROUTES.AUTH.REGISTER}
                        >
                            Daftar Akun
                        </SidebarLink>
                    {/if}
                </div>
            </div>
        {/if}
    </nav>
</aside>
