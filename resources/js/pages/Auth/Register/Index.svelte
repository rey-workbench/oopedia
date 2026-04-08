<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Input from '@/components/ui/Input.svelte';
    import { Link } from '@inertiajs/svelte';
    import { Loader2, UserPlus, ChevronLeft, Sparkles, BookOpen } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import { RegisterState } from '@/states/Auth/AuthState.svelte';
    import { fade, fly } from 'svelte/transition';
    import FloatingItem from '@/components/ui/FloatingItem.svelte';

    const state = new RegisterState({
        showSuccessToast: 'Registrasi berhasil!',
        showErrorToast: true,
    });
</script>

<App variant="auth" title="Daftar - OOPedia">
    <div class="flex min-h-screen w-full flex-col overflow-hidden lg:flex-row">
        <!-- Left: Cinematic Side (The Branding Panel) -->
        <div
            class="relative hidden w-full overflow-hidden border-r-2 border-slate-900 bg-[#0A0A0A] lg:flex lg:w-[45%] xl:w-[45%]"
        >
            <!-- Sophisticated Noise/Grain Texture Layer -->
            <div
                class="pointer-events-none absolute inset-0 z-0 opacity-[0.03]"
                style="background-image: url('data:image/svg+xml,%3Csvg viewBox=\'0 0 200 200\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cfilter id=\'noiseFilter\'%3E%3CfeTurbulence type=\'fractalNoise\' baseFrequency=\'0.65\' numOctaves=\'3\' stitchTiles=\'stitch\'/%3E%3C/filter%3E%3Crect width=\'100%25\' height=\'100%25\' filter=\'url(%23noiseFilter)\'/%3E%3C/svg%3E')"
            ></div>

            <!-- Dynamic Background Gradients -->
            <div class="pointer-events-none absolute inset-0 z-0 overflow-hidden">
                <div
                    class="absolute -top-[10%] -left-[10%] h-[60%] w-[60%] animate-pulse rounded-full bg-emerald-500/10 opacity-40 blur-[120px]"
                ></div>
                <div
                    class="absolute -right-[10%] -bottom-[20%] h-[70%] w-[70%] rounded-full bg-blue-500/10 opacity-30 blur-[120px]"
                ></div>

                <!-- Floating Decorative Elements -->
                <FloatingItem
                    image="/images/landing/abstract3.png"
                    top="15%"
                    right="10%"
                    width="280px"
                    height="280px"
                    opacity={0.25}
                    rotation={15}
                    blur="blur-md"
                    zIndex={0}
                />
                <FloatingItem
                    image="/images/landing/abstract4.png"
                    bottom="10%"
                    left="5%"
                    width="220px"
                    height="220px"
                    opacity={0.15}
                    rotation={-12}
                    blur="blur-sm"
                    zIndex={0}
                />
            </div>

            <div class="relative z-10 flex h-full w-full flex-col justify-between p-16">
                <!-- Branding Header -->
                <div in:fly={{ y: -20, duration: 1000 }}>
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
                </div>

                <!-- Cinematic Content -->
                <div class="max-w-md">
                    <div
                        class="mb-8 inline-flex items-center gap-2 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-4 py-1.5 backdrop-blur-md"
                        in:fade={{ duration: 800, delay: 200 }}
                    >
                        <div class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-400"></div>
                        <span
                            class="text-[10px] font-bold tracking-widest text-emerald-400 uppercase"
                            >Pendaftaran Akun</span
                        >
                    </div>

                    <h1
                        class="text-6xl leading-[1.1] font-black tracking-tight text-white uppercase"
                        in:fly={{ x: -30, duration: 1000, delay: 300 }}
                    >
                        Design Your <br />
                        <span
                            class="bg-gradient-to-r from-white via-white/60 to-white/20 bg-clip-text text-transparent"
                            >Digital</span
                        > Future.
                    </h1>

                    <p
                        class="mt-8 text-xl leading-relaxed font-medium tracking-normal text-white/40"
                        in:fly={{ x: -30, duration: 1000, delay: 400 }}
                    >
                        Bergabunglah dan jelajahi konten pembelajaran adaptif yang dirancang khusus
                        untuk mempercepat pemahaman Anda.
                    </p>

                    <div class="mt-12 space-y-10" in:fly={{ x: -20, duration: 1000, delay: 500 }}>
                        <div class="flex items-start gap-5">
                            <div
                                class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/5 ring-1 ring-white/10"
                            >
                                <Sparkles size={20} class="text-emerald-400" />
                            </div>
                            <div>
                                <p class="text-sm font-bold tracking-[0.2em] text-white uppercase">
                                    Adaptive Engine
                                </p>
                                <p class="mt-1 text-sm font-medium text-white/40">
                                    Materi yang menyesuaikan dengan tingkat kemampuan Anda secara
                                    real-time.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-5">
                            <div
                                class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/5 ring-1 ring-white/10"
                            >
                                <BookOpen size={20} class="text-blue-400" />
                            </div>
                            <div>
                                <p class="text-sm font-bold tracking-[0.2em] text-white uppercase">
                                    Curated Content
                                </p>
                                <p class="mt-1 text-sm font-medium text-white/40">
                                    Akses ribuan materi eksklusif yang disusun oleh para ahli di
                                    bidangnya.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Premium Footer -->
                <div
                    class="flex items-center gap-8 text-[10px] font-bold tracking-[0.2em] text-white/10 uppercase"
                    in:fade={{ delay: 800 }}
                >
                    <span>&copy; 2026 OOPEDIA TEAM</span>
                    <div class="h-1 w-1 rounded-full bg-white/10"></div>
                    <div class="flex gap-6">
                        {#each ['Twitter', 'Discord', 'Github'] as social}
                            <span
                                class="cursor-pointer text-white/20 transition-colors hover:text-emerald-400"
                            >
                                {social}
                            </span>
                        {/each}
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Form Side -->
        <div
            class="relative flex min-h-screen w-full items-center justify-center bg-[#FDFDFB] px-8 py-16 lg:w-[55%] xl:w-[58%]"
        >
            <!-- Decorative Elements Right Side -->
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div class="absolute top-0 right-0 h-36 w-36 opacity-10">
                    <img
                        src="/images/landing/abstract2.png"
                        alt=""
                        aria-hidden="true"
                        role="presentation"
                        class="h-full w-full object-contain blur-sm"
                    />
                </div>
                <div class="absolute bottom-10 left-0 h-40 w-40 opacity-10">
                    <img
                        src="/images/landing/abstract3.png"
                        alt=""
                        aria-hidden="true"
                        role="presentation"
                        class="h-full w-full object-contain blur-md"
                    />
                </div>
                <div class="absolute top-1/2 right-10 h-28 w-28 opacity-5">
                    <img
                        src="/images/landing/abstract4.png"
                        alt=""
                        aria-hidden="true"
                        role="presentation"
                        class="h-full w-full object-contain blur-sm"
                    />
                </div>
                <div class="absolute bottom-1/3 left-10 h-24 w-24 opacity-5">
                    <img
                        src="/images/landing/abstract1.png"
                        alt=""
                        aria-hidden="true"
                        role="presentation"
                        class="h-full w-full object-contain blur-sm"
                    />
                </div>
            </div>

            <!-- Back to Home Button (Desktop) -->
            <Link
                href={ROUTES.HOME}
                class="absolute top-12 right-12 hidden items-center gap-3 text-[10px] font-bold tracking-widest text-slate-600 uppercase transition-all hover:translate-x-[-10px] hover:text-slate-900 lg:flex"
            >
                <div
                    class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-900/2 ring-1 ring-slate-200"
                >
                    <ChevronLeft size={14} />
                </div>
                Kembali ke Beranda
            </Link>

            <div class="w-full max-w-xl" in:fade={{ duration: 1000, delay: 400 }}>
                <div class="mb-12 lg:text-center">
                    <div class="mb-4 flex justify-center lg:hidden">
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-900 shadow-2xl"
                        >
                            <img src="/images/logo.png" alt="Logo" class="h-8 w-auto invert" />
                        </div>
                    </div>
                    <h2 class="text-5xl font-black tracking-tighter text-slate-900 uppercase">
                        Mulai Belajar
                    </h2>
                    <p class="mt-4 text-base leading-relaxed font-medium text-slate-500">
                        Bergabunglah dengan ekosistem <span class="font-bold text-slate-900"
                            >1,248</span
                        > pembelajar cerdas lainnya.
                    </p>
                </div>

                <form
                    onsubmit={(e) => {
                        e.preventDefault();
                        state.submit();
                    }}
                    class="space-y-6"
                >
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <label
                                for="name"
                                class="text-[10px] font-bold tracking-widest text-slate-600 uppercase"
                                >Nama Lengkap</label
                            >
                            <Input
                                id="name"
                                type="text"
                                bind:value={state.form.name}
                                placeholder="Cth. John Doe"
                                required
                                error={state.form.errors['name']}
                                class="h-[70px] rounded-[24px] border-2 border-b-[6px] border-slate-200 bg-slate-900/2 px-8 text-lg font-medium transition-all focus:border-b-[6px] focus:border-slate-300 focus:bg-white focus:ring-0 focus:outline-none"
                            />
                        </div>
                        <div class="space-y-2">
                            <label
                                for="email"
                                class="text-[10px] font-bold tracking-widest text-slate-600 uppercase"
                                >Email</label
                            >
                            <Input
                                id="email"
                                type="email"
                                bind:value={state.form.email}
                                placeholder="nama@email.com"
                                required
                                error={state.form.errors['email']}
                                class="h-[70px] rounded-[24px] border-2 border-b-[6px] border-slate-200 bg-slate-900/2 px-8 text-lg font-medium transition-all focus:border-b-[6px] focus:border-slate-300 focus:bg-white focus:ring-0 focus:outline-none"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <label
                                for="password"
                                class="text-[10px] font-bold tracking-widest text-slate-600 uppercase"
                                >Kata Sandi</label
                            >
                            <Input
                                id="password"
                                type="password"
                                bind:value={state.form.password}
                                placeholder="••••••••"
                                required
                                error={state.form.errors['password']}
                                class="h-[70px] rounded-[24px] border-2 border-b-[6px] border-slate-200 bg-slate-900/2 px-8 text-lg font-medium transition-all focus:border-b-[6px] focus:border-slate-300 focus:bg-white focus:ring-0 focus:outline-none"
                            />
                        </div>
                        <div class="space-y-2">
                            <label
                                for="password_confirmation"
                                class="text-[10px] font-bold tracking-widest text-slate-600 uppercase"
                                >Konfirmasi</label
                            >
                            <Input
                                id="password_confirmation"
                                type="password"
                                bind:value={state.form.password_confirmation}
                                placeholder="••••••••"
                                required
                                class="h-[70px] rounded-[24px] border-2 border-b-[6px] border-slate-200 bg-slate-900/2 px-8 text-lg font-medium transition-all focus:border-b-[6px] focus:border-slate-300 focus:bg-white focus:ring-0 focus:outline-none"
                            />
                        </div>
                    </div>

                    <div
                        class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-slate-900/1 p-6 transition-all hover:bg-slate-900/3 hover:ring-1 hover:ring-slate-200"
                    >
                        <div class="relative flex items-center">
                            <input
                                type="checkbox"
                                id="register_as_admin"
                                bind:checked={state.form.register_as_admin}
                                class="h-6 w-6 cursor-pointer appearance-none rounded-lg border border-slate-300 bg-white transition-all checked:bg-slate-900 checked:ring-4 checked:ring-slate-200"
                            />
                            <div
                                class="pointer-events-none absolute inset-0 flex items-center justify-center opacity-0 transition-opacity peer-checked:opacity-100"
                            >
                                <Sparkles size={12} class="text-white" />
                            </div>
                        </div>
                        <label for="register_as_admin" class="flex-1 cursor-pointer select-none">
                            <span
                                class="block text-sm font-bold tracking-widest text-slate-900/60 uppercase"
                                >Daftar sebagai Dosen</span
                            >
                            <span class="mt-1 block text-xs font-medium text-slate-500 italic"
                                >Akses fitur pembuatan materi dan manajemen kelas (Perlu Peninjauan)</span
                            >
                        </label>
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            class="group relative h-[72px] w-full overflow-hidden rounded-[28px] border-2 border-b-8 border-slate-950 bg-slate-900 font-black tracking-[0.2em] text-white uppercase transition-all hover:bg-slate-800 active:translate-y-[4px] active:border-b-2 disabled:opacity-50"
                            disabled={state.form.processing}
                        >
                            <div
                                class="absolute inset-0 z-0 -translate-x-full bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-1000 group-hover:translate-x-full"
                            ></div>

                            <div class="relative z-10 flex items-center justify-center gap-4">
                                {#if state.form.processing}
                                    <Loader2 size={24} class="animate-spin text-white/50" />
                                    <span>Memproses...</span>
                                {:else}
                                    <span>Buat Akun Sekarang</span>
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-full bg-white/10 transition-all group-hover:translate-x-2 group-hover:bg-white/20"
                                    >
                                        <UserPlus size={18} strokeWidth={3} />
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

                    <p
                        class="pt-4 text-center text-[10px] font-bold tracking-[0.2em] text-slate-500 uppercase"
                    >
                        Sudah punya akun?
                        <Link
                            href={ROUTES.AUTH.LOGIN}
                            class="ml-2 text-slate-700 underline decoration-slate-300 decoration-2 underline-offset-12 transition-all hover:text-slate-900"
                            >Masuk ke Akun</Link
                        >
                    </p>
                </form>
            </div>
        </div>
    </div>
</App>
