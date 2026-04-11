<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Input from '@/components/ui/Input.svelte';
    import { Link } from '@inertiajs/svelte';
    import { Loader2, ArrowRight, Ghost, ChevronLeft, Star, Users } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import { LoginState } from '@/states/Auth/AuthState.svelte';
    import { fade, fly } from 'svelte/transition';
    import FloatingItem from '@/components/ui/FloatingItem.svelte';

    const state = new LoginState({
        showSuccessToast: 'Selamat datang kembali!',
        showErrorToast: true,
    });
</script>

<style>
    :global(body) {
        overflow-x: hidden;
    }
</style>

<App variant="auth" title="Login - OOPedia">
    <div class="flex min-h-screen w-full flex-col lg:flex-row">
        <!-- Left: Cinematic Side (The Branding Panel) -->
        <div
            class="relative hidden w-full overflow-hidden border-r-2 border-slate-900 bg-[#0A0A0A] lg:flex lg:w-[55%] xl:w-[60%]"
        >
            <!-- Sophisticated Noise/Grain Texture Layer -->
            <div
                class="pointer-events-none absolute inset-0 z-0 opacity-[0.03]"
                style="background-image: url('data:image/svg+xml,%3Csvg viewBox=\'0 0 200 200\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cfilter id=\'noiseFilter\'%3E%3CfeTurbulence type=\'fractalNoise\' baseFrequency=\'0.65\' numOctaves=\'3\' stitchTiles=\'stitch\'/%3E%3C/filter%3E%3Crect width=\'100%25\' height=\'100%25\' filter=\'url(%23noiseFilter)\'/%3E%3C/svg%3E')"
            ></div>

            <!-- Dynamic Background Elements -->
            <div class="pointer-events-none absolute inset-0 z-0 overflow-hidden">
                <div
                    class="absolute -top-40 -left-40 h-[600px] w-[600px] rounded-full bg-indigo-600/10 opacity-60 blur-[120px]"
                ></div>
                <div
                    class="absolute -right-40 -bottom-40 h-[600px] w-[600px] rounded-full bg-emerald-600/10 opacity-40 blur-[120px]"
                ></div>

                <!-- Floating Decorative Elements -->
                <FloatingItem
                    image="/images/landing/abstract1.png"
                    top="15%"
                    right="10%"
                    width="250px"
                    height="250px"
                    opacity={0.3}
                    rotation={15}
                    blur="blur-sm"
                    zIndex={0}
                />
                <FloatingItem
                    image="/images/landing/abstract2.png"
                    bottom="20%"
                    left="10%"
                    width="300px"
                    height="300px"
                    opacity={0.2}
                    rotation={-10}
                    blur="blur-md"
                    zIndex={0}
                />
            </div>

            <div class="relative z-10 flex h-full w-full flex-col justify-between p-20">
                <!-- Header: Minimal Branding -->
                <Link href={ROUTES.HOME} class="group flex items-center gap-4 text-white">
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-2xl border border-white/10 bg-white/5 shadow-2xl backdrop-blur-xl transition-all group-hover:scale-105 group-hover:bg-white/10"
                    >
                        <img src="/images/logo.png" alt="Logo" class="h-9 w-auto" />
                    </div>
                    <div class="flex flex-col">
                        <span class="text-2xl leading-none font-bold tracking-[0.2em] uppercase"
                            >OOPEDIA</span
                        >
                        <span
                            class="mt-1 text-[10px] font-bold tracking-[0.3em] text-white/30 uppercase"
                            >Adaptive Learning</span
                        >
                    </div>
                </Link>

                <!-- Center: Hero Text -->
                <div class="max-w-xl">
                    <div
                        class="mb-8 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1.5 backdrop-blur-md"
                        in:fade={{ duration: 800, delay: 200 }}
                    >
                        <div class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-400"></div>
                        <span class="text-[10px] font-bold tracking-widest text-white/50 uppercase"
                            >Versi 2.0 Kini Tersedia</span
                        >
                    </div>

                    <h1
                        class="text-7xl leading-none font-black tracking-tight text-white uppercase"
                        in:fly={{ y: 30, duration: 1000, delay: 300 }}
                    >
                        Satu langkah <br />
                        lebih dekat <br />
                        <span class="text-white/40">Penguasaan PBO.</span>
                    </h1>

                    <p
                        class="mt-8 max-w-lg text-xl leading-relaxed text-white/40"
                        in:fly={{ y: 20, duration: 1000, delay: 400 }}
                    >
                        Alami kurikulum yang beradaptasi dengan kecepatan belajar anda. Presisi,
                        Modern, dan Efektif.
                    </p>

                    <!-- Enhanced Stats Cards with Glassmorphism -->
                    <div
                        class="mt-16 flex items-center gap-6"
                        in:fade={{ duration: 1000, delay: 600 }}
                    >
                        <div
                            class="group relative flex items-center gap-4 rounded-3xl border-2 border-b-6 border-white/10 bg-white/5 p-8 transition-all hover:bg-white/10"
                        >
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-500/20 text-indigo-400"
                            >
                                <Users size={24} strokeWidth={3} />
                            </div>
                            <div>
                                <div class="text-3xl font-black text-white">1.2k+</div>
                                <div
                                    class="mt-0.5 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                >
                                    Mahasiswa
                                </div>
                            </div>
                        </div>

                        <div
                            class="group relative flex items-center gap-4 rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-2xl transition-all hover:bg-white/10"
                        >
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500/10 text-amber-400"
                            >
                                <Star size={24} />
                            </div>
                            <div>
                                <div class="text-2xl font-semibold text-white">4.8/5</div>
                                <div
                                    class="mt-0.5 text-[10px] font-bold tracking-widest text-white/30 uppercase"
                                >
                                    Rating
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div
                    class="flex items-center gap-8 text-[10px] font-bold tracking-[0.2em] text-white/10 uppercase"
                >
                    <span>&copy; 2026 OOPEDIA</span>
                    <div class="h-1 w-1 rounded-full bg-white/10"></div>
                    <span>DEEP LEARNING DIVISION</span>
                </div>
            </div>
        </div>

        <!-- Right: The Workspace Panel -->
        <div
            class="relative flex min-h-screen w-full items-center justify-center bg-[#FDFDFB] px-8 py-16 lg:w-[45%] xl:w-[40%]"
        >
            <!-- Decorative Subtle Elements -->
            <div class="pointer-events-none absolute inset-0 overflow-hidden opacity-40">
                <div
                    class="absolute top-[10%] right-[10%] h-64 w-64 rounded-full bg-indigo-500/5 blur-[100px]"
                ></div>
                <div
                    class="absolute bottom-[10%] left-[10%] h-64 w-64 rounded-full bg-emerald-500/5 blur-[100px]"
                ></div>

                <div class="absolute -top-10 -right-10 h-48 w-48 opacity-[0.03] blur-3xl">
                    <img
                        src="/images/landing/abstract1.png"
                        alt=""
                        aria-hidden="true"
                        role="presentation"
                        class="h-full w-full object-contain"
                    />
                </div>
                <div class="absolute bottom-20 -left-10 h-40 w-40 opacity-[0.03] blur-3xl">
                    <img
                        src="/images/landing/abstract2.png"
                        alt=""
                        aria-hidden="true"
                        role="presentation"
                        class="h-full w-full object-contain"
                    />
                </div>
            </div>

            <!-- Header Content (Mobile + UI control) -->
            <div class="absolute top-10 right-10 left-10 flex items-center justify-between">
                <div class="lg:hidden">
                    <Link href={ROUTES.HOME} class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white shadow-lg"
                        >
                            <img src="/images/logo.png" alt="Logo" class="h-6 w-auto" />
                        </div>
                        <span class="text-xs font-bold tracking-[0.2em] text-slate-900 uppercase"
                            >OOPEDIA</span
                        >
                    </Link>
                </div>

                <Link
                    href={ROUTES.HOME}
                    class="group ml-auto flex items-center gap-2 text-[10px] font-bold tracking-[0.2em] text-slate-900/40 uppercase transition-all hover:text-slate-900"
                >
                    <ChevronLeft
                        size={14}
                        class="transition-transform group-hover:-translate-x-1"
                    />
                    <span>Beranda</span>
                </Link>
            </div>

            <!-- The Form Core -->
            <div class="w-full max-w-sm" in:fade={{ duration: 1000, delay: 500 }}>
                <div class="mb-12">
                    <div
                        class="mb-6 inline-block animate-pulse rounded-full border border-slate-200 bg-slate-900/2 px-3 py-1"
                    >
                        <span class="text-[9px] font-bold tracking-widest text-slate-500 uppercase"
                            >Akses Mahasiswa</span
                        >
                    </div>
                    <h2 class="text-5xl font-black tracking-tighter text-slate-900 uppercase">
                        Masuk
                    </h2>
                    <p class="mt-4 text-base leading-relaxed font-medium text-slate-500">
                        Teruskan progres Anda dan capai level baru hari ini.
                    </p>
                </div>

                <form
                    onsubmit={(e) => {
                        e.preventDefault();
                        state.submit();
                    }}
                    class="space-y-8"
                >
                    <div class="space-y-3">
                        <label
                            for="email"
                            class="ml-1 text-[10px] font-bold tracking-[0.2em] text-slate-600 uppercase"
                        >
                            Alamat Email
                        </label>
                        <div class="relative">
                            <Input
                                id="email"
                                type="email"
                                bind:value={state.form.email}
                                placeholder="nama@email.com"
                                required
                                autocomplete="email"
                                error={state.form.errors['email']}
                                inputClass="h-[70px] px-8 text-lg font-medium"
                            />
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="ml-1 flex items-center justify-between">
                            <label
                                for="password"
                                class="text-[10px] font-bold tracking-[0.2em] text-slate-600 uppercase"
                            >
                                Kata Sandi
                            </label>
                            <Link
                                href="#"
                                class="text-[10px] font-bold tracking-widest text-slate-500 uppercase transition-colors hover:text-slate-700"
                                >Lupa?</Link
                            >
                        </div>
                        <div class="relative">
                            <Input
                                id="password"
                                type="password"
                                bind:value={state.form.password}
                                placeholder="••••••••"
                                required
                                autocomplete="current-password"
                                error={state.form.errors['password']}
                                inputClass="h-[70px] px-8 text-lg font-medium"
                            />
                        </div>
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            class="group relative h-[72px] w-full overflow-hidden rounded-[28px] border-2 border-b-8 border-slate-950 bg-slate-900 font-black tracking-[0.2em] text-white uppercase transition-all hover:bg-slate-800 active:translate-y-[4px] active:border-b-2 disabled:opacity-50"
                            disabled={state.form.processing}
                        >
                            <!-- Shiny Highlight -->
                            <div
                                class="absolute inset-0 z-0 -translate-x-full bg-linear-to-r from-transparent via-white/10 to-transparent transition-transform duration-1000 group-hover:translate-x-full"
                            ></div>

                            <div class="relative z-10 flex items-center justify-center gap-4">
                                {#if state.form.processing}
                                    <Loader2 size={24} class="animate-spin text-white/50" />
                                    <span>Memproses...</span>
                                {:else}
                                    <span>Masuk ke Akun</span>
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-full bg-white/10 transition-all group-hover:translate-x-2 group-hover:bg-white/20"
                                    >
                                        <ArrowRight size={18} strokeWidth={3} />
                                    </div>
                                {/if}
                            </div>
                        </button>
                    </div>

                    <div class="relative flex items-center justify-center py-4">
                        <div class="h-px w-full bg-slate-900/3"></div>
                        <span
                            class="absolute bg-[#FDFDFB] px-6 text-[10px] font-bold tracking-[0.3em] text-slate-400 uppercase"
                        >
                            Atau
                        </span>
                    </div>

                    <button
                        type="button"
                        onclick={() => state.submitAsGuest()}
                        class="group flex h-[72px] w-full items-center justify-center gap-4 rounded-[28px] border-2 border-b-6 border-slate-200 bg-white text-[10px] font-black tracking-[0.2em] text-slate-700 uppercase transition-all hover:bg-slate-50 active:translate-y-[4px] active:border-b-2 disabled:opacity-50"
                        disabled={state.form.processing}
                    >
                        <Ghost
                            size={20}
                            class="transition-transform group-hover:scale-110"
                            strokeWidth={3}
                        />
                        <span>Masuk Sebagai Tamu</span>
                    </button>

                    <p
                        class="pt-8 text-center text-[10px] font-bold tracking-[0.2em] text-slate-500 uppercase"
                    >
                        Belum punya akun?
                        <Link
                            href={ROUTES.AUTH.REGISTER}
                            class="ml-2 text-slate-700 underline decoration-slate-300 decoration-2 underline-offset-12 transition-all hover:text-slate-900"
                            >Mulai Sekarang</Link
                        >
                    </p>
                </form>
            </div>

            <!-- Fine Print -->
            <div
                class="absolute right-10 bottom-10 left-10 text-center text-[10px] font-medium tracking-[0.2em] text-slate-400 uppercase"
            >
                &mdash; Smart Learning System &mdash;
            </div>
        </div>
    </div>
</App>
