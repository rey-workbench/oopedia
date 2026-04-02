<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Input from '@/components/ui/Input.svelte';
    import { Link } from '@inertiajs/svelte';
    import { Loader2, ArrowRight, Ghost, ChevronLeft } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import { LoginState } from '@/states/Auth/AuthState.svelte';
    import { fade, fly } from 'svelte/transition';

    const state = new LoginState({ showSuccessToast: 'Login berhasil!', showErrorToast: true });
</script>

<App variant="auth" title="Login - OOPedia">
    <div class="flex min-h-screen w-full flex-col lg:flex-row">
        <!-- Left: Cinematic Side -->
        <div class="relative hidden w-full overflow-hidden bg-[#111] lg:flex lg:w-[55%] xl:w-[60%]">
            <!-- Background Abstract Elements -->
            <div class="pointer-events-none absolute inset-0">
                <div
                    class="absolute -top-20 -left-20 h-96 w-96 rounded-full bg-blue-500/20 opacity-40 blur-3xl"
                ></div>
                <div
                    class="absolute top-1/2 -right-20 h-96 w-96 rounded-full bg-purple-500/20 opacity-30 blur-3xl"
                ></div>

                <div class="absolute top-10 left-10 h-64 w-64 opacity-20">
                    <img
                        src="/images/landing/abstract1.png"
                        alt=""
                        class="h-full w-full object-contain"
                    />
                </div>
                <div class="absolute right-10 bottom-10 h-80 w-80 opacity-20">
                    <img
                        src="/images/landing/abstract2.png"
                        alt=""
                        class="h-full w-full object-contain"
                    />
                </div>
            </div>

            <div class="relative z-10 flex h-full w-full flex-col justify-between p-16">
                <!-- Branding -->
                <Link
                    href={ROUTES.HOME}
                    class="flex items-center gap-3 transition-transform hover:scale-105"
                >
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white shadow-xl"
                    >
                        <img src="/images/logo.png" alt="Logo" class="h-8 w-auto" />
                    </div>
                    <span class="text-2xl font-bold tracking-widest text-white">OOPEDIA</span>
                </Link>

                <!-- Content -->
                <div class="max-w-xl" in:fly={{ y: 20, duration: 1000, delay: 200 }}>
                    <h1
                        class="font-serif text-6xl leading-[1.1] font-medium tracking-tight text-white italic"
                    >
                        Mastering Object Oriented <span class="text-white/40">Programming</span>
                    </h1>
                    <p class="mt-8 text-xl font-medium tracking-wide text-white/50">
                        Platform pembelajaran adaptif untuk membantu anda menguasai konsep PBO
                        dengan cara yang modern dan menyenangkan.
                    </p>

                    <div class="mt-12 flex items-center gap-8 border-t border-white/10 pt-12">
                        <div>
                            <div class="text-3xl font-medium text-white italic">1.2k+</div>
                            <div class="mt-1 text-xs font-bold tracking-widest text-white/30 uppercase">
                                Mahasiswa Terdaftar
                            </div>
                        </div>
                        <div>
                            <div class="text-3xl font-medium text-white italic">4.8/5</div>
                            <div class="mt-1 text-xs font-bold tracking-widest text-white/30 uppercase">
                                Rating Platform
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-xs font-bold tracking-widest text-white/20 uppercase">
                    &copy; 2026 OOPEDIA TEAM. ALL RIGHTS RESERVED.
                </div>
            </div>
        </div>

        <!-- Right: Form Side -->
        <div
            class="relative flex min-h-screen w-full items-center justify-center bg-[#FDFDFB] px-6 py-12 lg:w-[45%] xl:w-[40%]"
        >
            <!-- Decorative Elements Right Side -->
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div class="absolute -top-10 -right-10 h-40 w-40 opacity-10">
                    <img src="/images/landing/abstract1.png" alt="" class="h-full w-full object-contain blur-sm" />
                </div>
                <div class="absolute bottom-20 -left-10 h-32 w-32 opacity-10">
                    <img src="/images/landing/abstract2.png" alt="" class="h-full w-full object-contain blur-md" />
                </div>
                <div class="absolute top-1/3 right-0 h-24 w-24 opacity-5">
                    <img src="/images/landing/abstract3.png" alt="" class="h-full w-full object-contain blur-sm" />
                </div>
                <div class="absolute bottom-1/3 left-0 h-20 w-20 opacity-5">
                    <img src="/images/landing/abstract4.png" alt="" class="h-full w-full object-contain blur-sm" />
                </div>
            </div>

            <!-- Mobile Branding -->
            <div class="absolute top-10 left-10 lg:hidden">
                <Link href={ROUTES.HOME} class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-black/5 bg-white shadow-lg"
                    >
                        <img src="/images/logo.png" alt="Logo" class="h-6 w-auto" />
                    </div>
                    <span class="text-lg font-bold tracking-widest text-black">OOPEDIA</span>
                </Link>
            </div>

            <!-- Back to Home Button (Desktop) -->
            <Link
                href={ROUTES.HOME}
                class="absolute top-10 right-10 hidden items-center gap-2 text-xs font-bold tracking-widest text-black/40 uppercase transition-colors hover:text-black lg:flex"
            >
                <ChevronLeft size={14} />
                Kembali ke Beranda
            </Link>

            <div class="w-full max-w-sm" in:fade={{ duration: 1000, delay: 400 }}>
                <div class="mb-10">
                    <h2 class="font-serif text-4xl font-medium tracking-tight text-black italic">
                        Masuk
                    </h2>
                    <p class="mt-3 text-base font-medium tracking-wide text-black/40">
                        Selamat datang kembali di <span class="text-black">OOPedia</span>.
                    </p>
                </div>

                <form
                    onsubmit={(e) => {
                        e.preventDefault();
                        state.submit();
                    }}
                    class="space-y-6"
                >
                    <div class="space-y-2">
                        <label
                            for="email"
                            class="text-[10px] font-bold tracking-widest text-black/40 uppercase"
                        >
                            Email
                        </label>
                        <Input
                            id="email"
                            type="email"
                            bind:value={state.form.email}
                            placeholder="nama@email.com"
                            required
                            autocomplete="email"
                            error={state.form.errors['email']}
                            class="h-14 rounded-2xl border-black/5 bg-black/2 px-5 focus:border-black/20 focus:bg-white"
                        />
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label
                                for="password"
                                class="text-[10px] font-bold tracking-widest text-black/40 uppercase"
                            >
                                Kata Sandi
                            </label>
                            <Link
                                href="#"
                                class="text-[10px] font-bold tracking-widest text-black/30 uppercase hover:text-black"
                                >Lupa?</Link
                            >
                        </div>
                        <Input
                            id="password"
                            type="password"
                            bind:value={state.form.password}
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                            error={state.form.errors['password']}
                            class="h-14 rounded-2xl border-black/5 bg-black/2 px-5 focus:border-black/20 focus:bg-white"
                        />
                    </div>

                    <button
                        type="submit"
                        class="group relative h-14 w-full overflow-hidden rounded-2xl bg-[#111] font-bold tracking-widest text-white uppercase transition-all hover:bg-black active:scale-[0.98] disabled:opacity-50"
                        disabled={state.form.processing}
                    >
                        <div class="relative z-10 flex items-center justify-center gap-3">
                            {#if state.form.processing}
                                <Loader2 size={18} class="animate-spin" />
                                <span>Memproses...</span>
                            {:else}
                                <span>Masuk Sekarang</span>
                                <ArrowRight
                                    size={18}
                                    class="transition-transform group-hover:translate-x-1"
                                />
                            {/if}
                        </div>
                    </button>

                    <div class="relative flex items-center justify-center py-2">
                        <div class="h-px w-full bg-black/5"></div>
                        <span
                            class="absolute bg-[#FDFDFB] px-4 text-[10px] font-bold tracking-widest text-black/20 uppercase"
                            >Atau</span
                        >
                    </div>

                    <button
                        type="button"
                        onclick={() => state.submitAsGuest()}
                        class="flex h-14 w-full items-center justify-center gap-3 rounded-2xl border border-black/5 bg-white font-bold tracking-widest text-black/40 uppercase transition-all hover:bg-black/2 hover:text-black/60"
                        disabled={state.form.processing}
                    >
                        <Ghost size={18} />
                        <span>Masuk Sebagai Tamu</span>
                    </button>

                    <p
                        class="pt-4 text-center text-xs font-bold tracking-widest text-black/30 uppercase"
                    >
                        Belum punya akun?
                        <Link
                            href={ROUTES.AUTH.REGISTER}
                            class="text-black underline underline-offset-8 transition-all hover:text-black/60"
                            >Buat Akun Baru</Link
                        >
                    </p>
                </form>
            </div>
        </div>
    </div>
</App>

