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
        <!-- Left: Cinematic Side -->
        <div
            class="relative hidden w-full overflow-hidden bg-[#0A0A0A] lg:flex lg:w-[45%] xl:w-[42%]"
        >
            <!-- Noise/Grain Texture Overlay -->
            <div
                class="pointer-events-none absolute inset-0 z-10 opacity-15 mix-blend-overlay"
                style="background-image: url('https://grainy-gradients.vercel.app/noise.svg');"
            ></div>

            <!-- Dynamic Background Gradients -->
            <div class="absolute inset-0 overflow-hidden">
                <div
                    class="absolute -top-[10%] -left-[10%] h-[60%] w-[60%] animate-pulse rounded-full bg-emerald-500/10 opacity-40 blur-[120px]"
                ></div>
                <div
                    class="absolute -right-[10%] -bottom-[20%] h-[70%] w-[70%] rounded-full bg-blue-500/10 opacity-30 blur-[120px]"
                ></div>
            </div>

            <!-- Parallax Floating Elements -->
            <div class="pointer-events-none absolute inset-0 z-0 overflow-hidden">
                <FloatingItem
                    image="/images/landing/abstract3.png"
                    top="15%"
                    right="10%"
                    width="280px"
                    height="280px"
                    opacity={0.25}
                    rotation={15}
                />
                <FloatingItem
                    image="/images/landing/abstract4.png"
                    bottom="10%"
                    left="5%"
                    width="220px"
                    height="220px"
                    opacity={0.15}
                    rotation={-12}
                />
            </div>

            <div class="relative z-20 flex h-full w-full flex-col justify-between p-16">
                <!-- Branding Header -->
                <div in:fly={{ y: -20, duration: 1000 }}>
                    <Link href={ROUTES.HOME} class="group flex items-center gap-4 transition-all">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 ring-1 ring-white/20 backdrop-blur-xl transition-all group-hover:scale-110 group-hover:bg-white"
                        >
                            <img
                                src="/images/logo.png"
                                alt="Logo"
                                class="h-7 w-auto transition-transform group-hover:scale-90"
                            />
                        </div>
                        <span
                            class="font-serif text-xl font-medium tracking-tighter text-white italic opacity-80 group-hover:opacity-100"
                        >
                            oopedia
                        </span>
                    </Link>
                </div>

                <!-- Cinematic Content -->
                <div class="max-w-md">
                    <div class="mb-8" in:fly={{ x: -30, duration: 1000, delay: 200 }}>
                        <span
                            class="inline-block rounded-full bg-emerald-500/10 px-4 py-1.5 text-[10px] font-bold tracking-widest text-emerald-400 uppercase ring-1 ring-emerald-500/20"
                        >
                            Pendaftaran Akun
                        </span>
                    </div>

                    <h1
                        class="font-serif text-6xl leading-[1.1] font-medium tracking-tight text-white italic"
                        in:fly={{ x: -30, duration: 1000, delay: 300 }}
                    >
                        Design Your <br />
                        <span
                            class="bg-linear-to-r from-white via-white/60 to-white/20 bg-clip-text text-transparent"
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
                                class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/5 ring-1 ring-white/10"
                            >
                                <Sparkles size={12} class="text-emerald-400" />
                            </div>
                            <div>
                                <p
                                    class="text-sm font-bold tracking-widest text-white/60 uppercase"
                                >
                                    Adaptive Engine
                                </p>
                                <p class="mt-1 text-sm text-white/30">
                                    Materi yang menyesuaikan dengan tingkat kemampuan Anda secara
                                    real-time.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-5">
                            <div
                                class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/5 ring-1 ring-white/10"
                            >
                                <BookOpen size={12} class="text-blue-400" />
                            </div>
                            <div>
                                <p
                                    class="text-sm font-bold tracking-widest text-white/60 uppercase"
                                >
                                    Curated Content
                                </p>
                                <p class="mt-1 text-sm text-white/30">
                                    Akses ribuan materi eksklusif yang disusun oleh para ahli di
                                    bidangnya.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Premium Footer -->
                <div
                    class="flex items-center justify-between opacity-30 transition-opacity hover:opacity-100"
                    in:fade={{ delay: 800 }}
                >
                    <span class="text-[10px] font-bold tracking-[0.3em] text-white uppercase">
                        &copy; 2026 OOPEDIA TEAM.
                    </span>
                    <div class="flex gap-6">
                        {#each ['Twitter', 'Discord', 'Github'] as social}
                            <span
                                class="cursor-pointer text-[10px] font-bold tracking-widest text-white uppercase transition-colors hover:text-emerald-400"
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
                class="absolute top-12 right-12 hidden items-center gap-3 text-[10px] font-bold tracking-widest text-slate-600 uppercase transition-all hover:translate-x-[-10px] hover:text-black lg:flex"
            >
                <div
                    class="flex h-8 w-8 items-center justify-center rounded-full bg-black/2 ring-1 ring-black/5"
                >
                    <ChevronLeft size={14} />
                </div>
                Kembali ke Beranda
            </Link>

            <div class="w-full max-w-xl" in:fade={{ duration: 1000, delay: 400 }}>
                <div class="mb-12 lg:text-center">
                    <div class="mb-4 flex justify-center lg:hidden">
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-black shadow-2xl"
                        >
                            <img src="/images/logo.png" alt="Logo" class="h-8 w-auto invert" />
                        </div>
                    </div>
                    <h2 class="font-serif text-5xl font-medium tracking-tight text-black italic">
                        Mulai Belajar.
                    </h2>
                    <p class="mt-4 text-lg font-medium tracking-wide text-slate-500">
                        Bergabunglah dengan ekosistem <span class="font-semibold text-black"
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
                                class="h-14 rounded-2xl border-black/5 bg-black/2 px-5 focus:border-black/20 focus:bg-white"
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
                                class="h-14 rounded-2xl border-black/5 bg-black/2 px-5 focus:border-black/20 focus:bg-white"
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
                                class="h-14 rounded-2xl border-black/5 bg-black/2 px-5 focus:border-black/20 focus:bg-white"
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
                                class="h-14 rounded-2xl border-black/5 bg-black/2 px-5 focus:border-black/20 focus:bg-white"
                            />
                        </div>
                    </div>

                    <div
                        class="flex items-center gap-4 rounded-2xl border border-black/5 bg-black/1 p-6 transition-all hover:bg-black/3 hover:ring-1 hover:ring-black/5"
                    >
                        <div class="relative flex items-center">
                            <input
                                type="checkbox"
                                id="register_as_admin"
                                bind:checked={state.form.register_as_admin}
                                class="h-6 w-6 cursor-pointer appearance-none rounded-lg border border-black/10 bg-white transition-all checked:bg-black checked:ring-4 checked:ring-black/10"
                            />
                            <div
                                class="pointer-events-none absolute inset-0 flex items-center justify-center opacity-0 transition-opacity peer-checked:opacity-100"
                            >
                                <Sparkles size={12} class="text-white" />
                            </div>
                        </div>
                        <label for="register_as_admin" class="flex-1 cursor-pointer select-none">
                            <span
                                class="block text-sm font-bold tracking-widest text-black/60 uppercase"
                                >Daftar sebagai Dosen</span
                            >
                            <span class="mt-1 block text-xs font-medium text-slate-500 italic"
                                >Akses fitur pembuatan materi dan manajemen kelas (Perlu Peninjauan)</span
                            >
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="group relative h-16 w-full overflow-hidden rounded-2xl bg-[#0F172A] font-bold tracking-widest text-white uppercase shadow-2xl transition-all hover:bg-black hover:shadow-black/20 active:scale-[0.98] disabled:opacity-50"
                        disabled={state.form.processing}
                    >
                        <div class="relative z-10 flex items-center justify-center gap-4">
                            {#if state.form.processing}
                                <Loader2 size={20} class="animate-spin" />
                                <span>Menyiapkan Akun...</span>
                            {:else}
                                <span>Buat Akun Sekarang</span>
                                <div
                                    class="flex h-6 w-6 items-center justify-center rounded-full bg-white/10 transition-transform group-hover:scale-110 group-hover:rotate-12"
                                >
                                    <UserPlus size={14} />
                                </div>
                            {/if}
                        </div>
                        <!-- Button Shine Effect -->
                        <div
                            class="absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent via-white/20 to-transparent opacity-0 transition-opacity group-hover:opacity-100"
                        ></div>
                    </button>

                    <div class="relative py-4 text-center">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-black/3"></div>
                        </div>
                        <span
                            class="relative bg-[#FDFDFB] px-4 text-[10px] font-bold tracking-[0.3em] text-slate-500 uppercase"
                        >
                            Sudah punya akun?
                        </span>
                    </div>

                    <p class="text-center">
                        <Link
                            href={ROUTES.AUTH.LOGIN}
                            class="group relative inline-flex items-center gap-2 text-xs font-bold tracking-widest text-black/40 uppercase transition-all hover:text-black"
                        >
                            <span class="relative">
                                Masuk ke Akun
                                <span
                                    class="absolute -bottom-1 left-0 h-px w-0 bg-black transition-all group-hover:w-full"
                                ></span>
                            </span>
                        </Link>
                    </p>
                </form>
            </div>
        </div>
    </div>
</App>
