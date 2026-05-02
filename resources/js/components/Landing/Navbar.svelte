<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { ROUTES } from '@/utils/route';
    import { Search, Menu as MenuIcon } from 'lucide-svelte';
    import { Motion } from 'svelte-motion';

    let menuOpen = $state(false);
    let scrollY = $state(0);
    let searchFocused = $state(false);

    const menuItems = [
        { label: 'Beranda', href: '/' },
        { label: 'Tentang', href: '/#tentang' },
        { label: 'Fitur', href: '/#fitur' },
        { label: 'Materi', href: '/materi' },
    ];

    let isScrolled = $derived(scrollY > 20);
</script>

<svelte:window bind:scrollY />

<header
    class="fixed top-0 right-0 left-0 z-50 transition-all duration-500 {isScrolled
        ? 'py-3'
        : 'py-6'}"
>
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6">
        <!-- Logo & Brand -->
        <Link href="/" class="group flex items-center gap-3">
            <img
                src="/images/logo.png"
                alt="OOPedia Logo"
                class="h-10 w-auto transition-transform group-hover:rotate-6"
            />
            <span class="hidden text-xl font-black tracking-tighter sm:block">
                <span class="mr-2 text-slate-900/20">|</span>
                <span class="text-brand-yellow">OOP</span><span class="text-slate-900">edia</span>
            </span>
        </Link>

        <!-- Centered Navigation (Desktop) -->
        <nav
            class="hidden items-center gap-1 rounded-full border border-slate-900/5 bg-white/40 p-1 shadow-sm backdrop-blur-xl md:flex"
        >
            {#each menuItems as item}
                <Link
                    href={item.href}
                    class="rounded-full px-5 py-2.5 text-xs font-black tracking-widest text-slate-900/60 uppercase transition-all hover:bg-slate-900/5 hover:text-slate-900"
                >
                    {item.label}
                </Link>
            {/each}
        </nav>

        <!-- Right Actions -->
        <div class="flex items-center gap-4">
            <!-- Search Bar (Expandable) -->
            <div class="hidden items-center md:flex">
                <Motion
                    animate={{ width: searchFocused ? 280 : 44 }}
                    transition={{ type: 'spring', stiffness: 300, damping: 30 }}
                    let:motion
                >
                    <div
                        use:motion
                        class="relative flex h-11 items-center overflow-hidden rounded-full border border-slate-900/5 bg-white/40 backdrop-blur-md transition-colors {searchFocused
                            ? 'border-slate-900/20 bg-white/80'
                            : ''}"
                    >
                        <Search
                            size={16}
                            class="absolute left-3.5 text-slate-900/30"
                            strokeWidth={3}
                        />
                        <input
                            type="text"
                            onfocus={() => (searchFocused = true)}
                            onblur={() => (searchFocused = false)}
                            placeholder="CARI MATERI..."
                            class="h-full w-full border-none bg-transparent pr-4 pl-11 text-[10px] font-black tracking-widest uppercase outline-none placeholder:text-slate-900/20"
                        />
                    </div>
                </Motion>
            </div>

            <!-- Auth Buttons -->
            <div class="flex items-center gap-2">
                <Link
                    href={ROUTES.AUTH.LOGIN}
                    class="hidden items-center rounded-full px-5 py-2.5 text-xs font-black tracking-widest text-slate-900/60 uppercase transition-colors hover:text-slate-900 md:inline-flex"
                >
                    Masuk
                </Link>
                <Link
                    href={ROUTES.AUTH.REGISTER}
                    class="rounded-full border-2 border-b-4 border-slate-950 bg-slate-900 px-6 py-2.5 text-xs font-black tracking-widest text-white uppercase transition-all hover:bg-slate-800 active:translate-y-1 active:border-b-2"
                >
                    Daftar
                </Link>
            </div>

            <!-- Mobile Menu Toggle -->
            <button
                type="button"
                onclick={() => (menuOpen = !menuOpen)}
                class="flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-900/5 bg-white/40 text-slate-900 backdrop-blur-md md:hidden"
            >
                <MenuIcon size={20} strokeWidth={3} />
            </button>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    {#if menuOpen}
        <Motion
            initial={{ opacity: 0, y: -20 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -20 }}
            let:motion
        >
            <div
                use:motion
                class="absolute top-full left-0 w-full bg-white/95 p-6 backdrop-blur-2xl md:hidden"
            >
                <nav class="flex flex-col gap-2">
                    {#each menuItems as item}
                        <Link
                            href={item.href}
                            onclick={() => (menuOpen = false)}
                            class="rounded-2xl px-6 py-4 text-sm font-black tracking-widest text-slate-900/60 uppercase transition-colors hover:bg-slate-900/5 hover:text-slate-900"
                        >
                            {item.label}
                        </Link>
                    {/each}

                    <div class="mt-4 flex flex-col gap-3 border-t border-slate-100 pt-6">
                        <Link
                            href={ROUTES.AUTH.LOGIN}
                            onclick={() => (menuOpen = false)}
                            class="flex items-center justify-center rounded-2xl border-2 border-slate-200 py-4 text-sm font-black tracking-widest text-slate-900 uppercase transition-colors hover:bg-slate-50"
                        >
                            Masuk
                        </Link>
                        <Link
                            href={ROUTES.AUTH.REGISTER}
                            onclick={() => (menuOpen = false)}
                            class="flex items-center justify-center rounded-2xl border-2 border-b-4 border-slate-950 bg-slate-900 py-4 text-sm font-black tracking-widest text-white uppercase transition-all active:translate-y-1 active:border-b-2"
                        >
                            Daftar Sekarang
                        </Link>
                    </div>
                </nav>
            </div>
        </Motion>
    {/if}
</header>
