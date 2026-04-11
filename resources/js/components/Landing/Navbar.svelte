<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { ROUTES } from '@/utils/route';
    import { Search, Plus, X, ChevronRight, CircleHelp } from 'lucide-svelte';
    import { tutorialState } from '@/states/ui/tutorialState.svelte';

    let menuOpen = $state(false);
    let scrollY = $state(0);

    const menuItems = [
        { label: 'Beranda', href: '/' },
        { label: 'Tentang', href: '/#tentang' },
        { label: 'Fitur', href: '/#fitur' },
        { label: 'Materi', href: '/materi' },
        { label: 'Harga', href: '/#harga' },
    ];

    const closeMenu = () => {
        menuOpen = false;
    };

    let isScrolled = $derived(scrollY > 50);
    let isDark = $derived(!isScrolled && scrollY > 8000);

    let headerBg = $derived(
        isScrolled ? 'bg-white/95 backdrop-blur-xl border-b-6 border-slate-200' : 'bg-transparent'
    );
    let menuBtnBg = $derived(
        isDark
            ? 'bg-slate-900 border-2 border-slate-700 border-b-6 text-white'
            : 'bg-white border-2 border-slate-200 border-b-6 text-slate-900 shadow-sm'
    );
    let searchIconColor = $derived(isDark ? 'text-white/30' : 'text-slate-900/30');
    let placeholderColor = $derived(
        isDark ? 'placeholder:text-white/30' : 'placeholder:text-slate-900/30'
    );
    let authTextColor = $derived(
        isDark ? 'text-white/60 hover:text-white' : 'text-slate-900/60 hover:text-slate-900'
    );
</script>

<svelte:window bind:scrollY />

<header
    class="pointer-events-none fixed top-0 right-0 left-0 z-50 flex items-center justify-between px-4 py-4 transition-all duration-300 sm:px-6 {headerBg}"
>
    <div class="menu-container pointer-events-auto relative">
        <button
            type="button"
            onclick={() => (menuOpen = !menuOpen)}
            class="flex min-h-12 cursor-pointer items-center gap-2 rounded-2xl px-6 py-3.5 text-[10px] font-black tracking-[0.2em] uppercase transition-all hover:bg-slate-50 active:translate-y-[4px] active:border-b-2 {menuBtnBg}"
        >
            {menuOpen ? 'Tutup' : 'Menu'}
            {#if menuOpen}
                <X size={12} class="stroke-[3px] opacity-40" />
            {:else}
                <Plus size={12} class="stroke-[3px] opacity-40" />
            {/if}
        </button>

        {#if menuOpen}
            <div
                class="absolute top-full left-0 mt-3 w-64 rounded-3xl border-2 border-b-8 border-slate-200 bg-white p-2 shadow-xl"
            >
                {#each menuItems as item}
                    <Link
                        href={item.href}
                        onclick={closeMenu}
                        class="group flex items-center justify-between rounded-2xl px-5 py-4 text-[11px] font-black tracking-widest text-slate-900/70 uppercase transition-all hover:bg-slate-900/5 hover:text-slate-900"
                    >
                        <span>{item.label}</span>
                        <ChevronRight
                            size={14}
                            class="opacity-0 transition-opacity group-hover:opacity-40"
                        />
                    </Link>
                {/each}
            </div>
        {/if}
    </div>

    <div
        class="pointer-events-auto absolute left-1/2 hidden w-full max-w-lg -translate-x-1/2 md:block"
    >
        <div
            class="flex min-h-12 cursor-text items-center gap-4 rounded-3xl border-2 border-b-6 border-slate-200 bg-white px-6 py-4 shadow-sm transition-all hover:border-slate-300"
        >
            <Search size={16} class={searchIconColor} strokeWidth={3} />
            <input
                type="text"
                aria-label="Cari materi OOP"
                placeholder="Cari materi OOP..."
                class="w-full border-none bg-transparent text-xs font-black tracking-widest uppercase outline-none {placeholderColor}"
            />
        </div>
    </div>

    <div class="pointer-events-auto flex items-center gap-3 sm:gap-5">
        <button
            onclick={() => tutorialState.startTour('landing', true)}
            class="group hover:border-accent-200 relative rounded-2xl border-2 border-transparent p-2 text-slate-900/40 transition-all hover:bg-slate-900/5 hover:text-slate-900 active:translate-y-0.5"
            title="Saran: Mulai Tutorial"
        >
            <CircleHelp size={20} strokeWidth={1.5} />
            <span class="absolute -top-1 -right-1 flex h-3 w-3">
                <span
                    class="bg-accent-400 absolute inline-flex h-full w-full animate-ping rounded-full opacity-75"
                ></span>
                <span class="bg-accent-500 relative inline-flex h-3 w-3 rounded-full"></span>
            </span>
        </button>

        <Link
            href={ROUTES.AUTH.LOGIN}
            class="rounded-full px-3 py-2 text-[10px] font-black tracking-[0.2em] uppercase transition-colors {authTextColor}"
        >
            Masuk
        </Link>
        <Link
            href={ROUTES.AUTH.REGISTER}
            class="rounded-2xl border-2 border-b-6 border-slate-950 bg-slate-900 px-6 py-4 text-[9px] font-black tracking-[0.2em] text-white uppercase shadow-sm transition-all hover:bg-slate-800 active:translate-y-[4px] active:border-b-2 sm:px-8"
        >
            Daftar
        </Link>
    </div>
</header>
