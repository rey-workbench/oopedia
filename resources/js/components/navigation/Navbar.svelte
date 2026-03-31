<script lang="ts">
    import { Link, page, router } from '@inertiajs/svelte';
    import { Menu, ChevronRight, CircleHelp, User, LogOut } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import { sidebarState } from '@/states/UI';
    import { isAdmin } from '@/utils/roles';

    interface Props {
        titlePage?: string;
    }

    let { titlePage = '' }: Props = $props();

    const auth = $derived($page.props['auth'] ?? {});
    const user = $derived(auth.user ?? null);
    const isAuthenticated = $derived(!!user);
    const isAdminRole = $derived(isAuthenticated && isAdmin(user?.role?.role_name));
    const userName = $derived(user?.name ?? 'Tamu');

    function logout() {
        router.post('/logout');
    }
</script>

<nav
    class="sticky top-0 z-40 w-full border-b border-gray-100 bg-white/80 px-4 backdrop-blur-md sm:px-6 lg:px-8"
>
    <div class="flex h-16 items-center justify-between">
        <div class="flex items-center gap-4">
            <button
                onclick={() => sidebarState.toggle()}
                aria-label="Toggle Sidebar"
                class="rounded-xl p-2 text-slate-500 transition-colors hover:bg-slate-50 lg:hidden"
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
                            <span class="font-medium text-slate-400">Halaman</span>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <ChevronRight
                                    size={10}
                                    strokeWidth={3}
                                    class="mx-2 text-slate-300"
                                />
                                <span class="font-bold tracking-widest text-slate-900 uppercase"
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
                <div class="mr-2 hidden flex-col items-end md:flex">
                    <span class="text-sm leading-none font-bold text-slate-900">{userName}</span>
                    <span class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                        >{isAdminRole ? 'Administrator' : 'Mahasiswa'}</span
                    >
                </div>

                <button
                    id="start-page-tour"
                    aria-label="Start Page Tour"
                    class="hover:bg-accent-50 hover:text-accent-600 group relative rounded-xl p-2 text-slate-500 transition-all"
                    title="Mulai Tutorial"
                    data-intro="Klik tombol ini kapan saja jika kamu butuh bantuan atau ingin mengulang tutorial di halaman ini."
                    data-step="4"
                >
                    <CircleHelp size={20} strokeWidth={2.5} />
                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                        <span
                            class="bg-accent-400 absolute inline-flex h-full w-full animate-ping rounded-full opacity-75"
                        ></span>
                        <span class="bg-accent-500 relative inline-flex h-3 w-3 rounded-full"
                        ></span>
                    </span>
                </button>

                <div
                    class="group relative"
                    data-intro="Kelola profil kamu atau keluar dari akun melalui menu ini."
                    data-step="5"
                >
                    <button
                        aria-label="Open project menu"
                        class="hover:border-accent-100 group flex items-center gap-2 rounded-2xl border-2 border-transparent p-1 transition-all duration-300"
                    >
                        <div
                            class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-slate-100 shadow-[inset_0_2px_4px_rgba(0,0,0,0.06)] ring-2 ring-white transition-all group-hover:ring-slate-100"
                        >
                            <img
                                src="/images/profile.gif"
                                alt="Profile"
                                class="h-full w-full object-cover"
                            />
                        </div>
                    </button>

                    <div
                        class="invisible absolute right-0 mt-2 w-56 origin-top-right scale-95 transform rounded-2xl bg-white p-2 opacity-0 shadow-2xl ring-1 ring-black/5 transition-all duration-300 group-hover:visible group-hover:scale-100 group-hover:opacity-100"
                    >
                        <div class="mb-1 border-b border-slate-50 px-4 py-3">
                            <p class="text-sm font-bold text-slate-900">
                                {userName}
                            </p>
                            <p class="truncate text-[10px] text-slate-400">
                                {user.email}
                            </p>
                        </div>

                        <Link
                            href={ROUTES.MAHASISWA.PROFILE}
                            class="hover:text-accent-600 hover:bg-accent-50 flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 transition-all"
                        >
                            <User size={18} strokeWidth={2.5} class="w-5" />
                            Profil Saya
                        </Link>

                        <form
                            onsubmit={(e) => {
                                e.preventDefault();
                                logout();
                            }}
                        >
                            <button
                                type="submit"
                                class="flex w-full items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-bold text-rose-600 transition-all hover:bg-rose-50"
                            >
                                <LogOut size={18} strokeWidth={2.5} class="w-5" />
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            {:else}
                <div class="flex items-center gap-2">
                    <Link
                        href={ROUTES.AUTH.LOGIN}
                        class="hover:text-accent-600 px-6 py-2.5 text-sm font-bold tracking-widest text-slate-600 uppercase transition-all"
                        >Masuk</Link
                    >
                    <Link
                        href={ROUTES.AUTH.REGISTER}
                        class="hover:bg-accent-600 rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-bold tracking-widest text-white uppercase shadow-lg shadow-slate-200 transition-all"
                        >Daftar</Link
                    >
                </div>
            {/if}
        </div>
    </div>
</nav>
