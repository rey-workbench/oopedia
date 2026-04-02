<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { ROUTES } from '@/utils/route';
    import { Search, Plus, X, ChevronRight } from 'lucide-svelte';

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
    let isDarkSection = $derived(scrollY > 8000);

    let headerBg = $derived(
        isScrolled ? 'bg-white/95 backdrop-blur-xl border-b border-black/5' : 'bg-transparent'
    );
    let menuBtnBg = $derived(
        isDarkSection && !isScrolled
            ? 'bg-black/80 text-white border border-white/10'
            : 'bg-white/80 text-black border border-black/5'
    );
    let searchBg = $derived(
        isDarkSection && !isScrolled
            ? 'bg-black/80 border border-white/10'
            : 'bg-white/80 border border-black/5'
    );
    let searchIconColor = $derived(
        isDarkSection && !isScrolled ? 'text-white/30' : 'text-black/30'
    );
    let placeholderColor = $derived(
        isDarkSection && !isScrolled ? 'placeholder:text-white/30' : 'placeholder:text-black/30'
    );
    let authTextColor = $derived(
        isDarkSection && !isScrolled
            ? 'text-white/60 hover:text-white'
            : 'text-black/60 hover:text-black'
    );
    let authBtnBg = $derived(
        isDarkSection && !isScrolled ? 'bg-white text-black' : 'bg-black text-white'
    );
</script>

<svelte:window bind:scrollY />

<header
    class="pointer-events-none fixed top-0 right-0 left-0 z-50 flex items-center justify-between p-6 transition-all duration-300 {headerBg}"
>
    <div class="menu-container pointer-events-auto relative">
        <button
            type="button"
            onclick={() => (menuOpen = !menuOpen)}
            class="flex cursor-pointer items-center gap-2 rounded-full px-5 py-3 text-[10px] font-black tracking-[0.2em] uppercase shadow-xl backdrop-blur-3xl transition-all hover:bg-white {menuBtnBg}"
        >
            {menuOpen ? 'Tutup' : 'Menu'}
            {#if menuOpen}
                <X size={12} class="opacity-40" />
            {:else}
                <Plus size={12} class="opacity-40" />
            {/if}
        </button>

        {#if menuOpen}
            <div
                class="absolute top-full left-0 mt-3 w-56 rounded-2xl border border-black/5 bg-white/95 p-2 shadow-2xl backdrop-blur-3xl"
            >
                {#each menuItems as item}
                    <Link
                        href={item.href}
                        onclick={closeMenu}
                        class="group flex items-center justify-between rounded-xl px-4 py-3 text-[11px] font-medium tracking-wide text-black/70 transition-all hover:bg-black/5 hover:text-black"
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
        class="pointer-events-auto absolute left-1/2 hidden w-full max-w-md -translate-x-1/2 md:block"
    >
        <div
            class="flex cursor-text items-center gap-4 rounded-full px-5 py-3.5 shadow-xl backdrop-blur-3xl transition-all hover:bg-white {searchBg}"
        >
            <Search size={16} class={searchIconColor} />
            <input
                type="text"
                placeholder="Cari materi OOP..."
                class="w-full border-none bg-transparent text-xs font-medium outline-none {placeholderColor}"
            />
        </div>
    </div>

    <div class="pointer-events-auto flex items-center gap-5">
        <Link
            href={ROUTES.AUTH.LOGIN}
            class="text-[10px] font-black tracking-[0.2em] uppercase transition-colors {authTextColor}"
        >
            Masuk
        </Link>
        <Link
            href={ROUTES.AUTH.REGISTER}
            class="rounded-full px-6 py-3.5 text-[9px] font-black tracking-[0.2em] uppercase shadow-2xl transition-transform hover:scale-105 active:scale-95 {authBtnBg}"
        >
            Daftar
        </Link>
    </div>
</header>
