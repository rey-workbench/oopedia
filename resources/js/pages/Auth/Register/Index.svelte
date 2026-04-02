<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Input from '@/components/ui/Input.svelte';
    import { Link } from '@inertiajs/svelte';
    import { Loader2, UserPlus, ChevronLeft } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import { RegisterState } from '@/states/Auth/AuthState.svelte';
    import { fade, fly } from 'svelte/transition';

    const state = new RegisterState({
        showSuccessToast: 'Registrasi berhasil!',
        showErrorToast: true,
    });
</script>

<App variant="auth" title="Daftar - OOPedia">
    <div class="flex min-h-screen w-full flex-col lg:flex-row">
        <!-- Left: Cinematic Side -->
        <div class="relative hidden w-full overflow-hidden bg-[#111] lg:flex lg:w-[45%] xl:w-[40%]">
            <!-- Background Abstract Elements -->
            <div class="pointer-events-none absolute inset-0">
                <div
                    class="absolute -top-20 -left-20 h-80 w-80 rounded-full bg-emerald-500/10 opacity-30 blur-3xl"
                ></div>
                <div
                    class="absolute right-[-10%] bottom-[-10%] h-96 w-96 rounded-full bg-blue-500/10 opacity-30 blur-3xl"
                ></div>

                <div class="absolute top-1/4 right-0 h-64 w-64 rotate-12 opacity-20">
                    <img
                        src="/images/landing/abstract3.png"
                        alt=""
                        class="h-full w-full object-contain"
                    />
                </div>
                <div class="absolute bottom-1/4 left-0 h-48 w-48 -rotate-12 opacity-15">
                    <img
                        src="/images/landing/abstract4.png"
                        alt=""
                        class="h-full w-full object-contain"
                    />
                </div>
            </div>

            <div class="relative z-10 flex h-full w-full flex-col justify-between p-12">
                <!-- Branding -->
                <Link
                    href={ROUTES.HOME}
                    class="flex items-center gap-3 transition-transform hover:scale-105"
                >
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-xl"
                    >
                        <img src="/images/logo.png" alt="Logo" class="h-6 w-auto" />
                    </div>
                </Link>

                <!-- Content -->
                <div in:fly={{ x: -20, duration: 1000, delay: 200 }}>
                    <h1
                        class="font-serif text-5xl leading-tight font-medium tracking-tight text-white italic"
                    >
                        Start Your <br /> <span class="text-white/40">Knowledge</span> Journey.
                    </h1>
                    <p class="mt-6 text-lg font-medium tracking-wide text-white/40">
                        Hanya butuh beberapa detik untuk mulai belajar dengan fitur adaptif kami.
                    </p>

                    <div class="mt-10 space-y-6">
                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-white/5 font-serif italic text-white"
                            >
                                1
                            </div>
                            <span class="text-sm font-bold tracking-widest text-white/50 uppercase"
                                >Pilih Materi</span
                            >
                        </div>
                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-white/5 font-serif italic text-white"
                            >
                                2
                            </div>
                            <span class="text-sm font-bold tracking-widest text-white/50 uppercase"
                                >Ikuti Kuis Adaptif</span
                            >
                        </div>
                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-white/5 font-serif italic text-white"
                            >
                                3
                            </div>
                            <span class="text-sm font-bold tracking-widest text-white/50 uppercase"
                                >Kuasai Konsep</span
                            >
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-[10px] font-bold tracking-widest text-white/20 uppercase">
                    &copy; 2026 OOPEDIA TEAM.
                </div>
            </div>
        </div>

        <!-- Right: Form Side -->
        <div
            class="relative flex min-h-screen w-full items-center justify-center bg-[#FDFDFB] px-6 py-12 lg:w-[55%] xl:w-[60%]"
        >
            <!-- Decorative Elements Right Side -->
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div class="absolute top-0 right-0 h-36 w-36 opacity-10">
                    <img src="/images/landing/abstract2.png" alt="" class="h-full w-full object-contain blur-sm" />
                </div>
                <div class="absolute bottom-10 left-0 h-40 w-40 opacity-10">
                    <img src="/images/landing/abstract3.png" alt="" class="h-full w-full object-contain blur-md" />
                </div>
                <div class="absolute top-1/2 right-10 h-28 w-28 opacity-5">
                    <img src="/images/landing/abstract4.png" alt="" class="h-full w-full object-contain blur-sm" />
                </div>
                <div class="absolute bottom-1/3 left-10 h-24 w-24 opacity-5">
                    <img src="/images/landing/abstract1.png" alt="" class="h-full w-full object-contain blur-sm" />
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

            <div class="w-full max-w-xl" in:fade={{ duration: 1000, delay: 400 }}>
                <div class="mb-10 lg:text-center">
                    <h2 class="font-serif text-4xl font-medium tracking-tight text-black italic">
                        Buat Akun
                    </h2>
                    <p class="mt-3 text-base font-medium tracking-wide text-black/40">
                        Bergabung bersama <span class="text-black">1.2k+</span> pembelajar lainnya.
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
                                class="text-[10px] font-bold tracking-widest text-black/40 uppercase"
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
                                class="text-[10px] font-bold tracking-widest text-black/40 uppercase"
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
                                class="text-[10px] font-bold tracking-widest text-black/40 uppercase"
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
                                class="text-[10px] font-bold tracking-widest text-black/40 uppercase"
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
                        class="flex items-center gap-3 rounded-2xl border border-black/5 bg-black/[0.01] p-5"
                    >
                        <div class="relative flex items-center">
                            <input
                                type="checkbox"
                                id="register_as_admin"
                                bind:checked={state.form.register_as_admin}
                                class="h-5 w-5 cursor-pointer appearance-none rounded-lg border border-black/10 bg-white transition-all checked:bg-black"
                            />
                        </div>
                        <label
                            for="register_as_admin"
                            class="flex-1 cursor-pointer text-xs font-bold tracking-widest text-black/40 uppercase select-none"
                        >
                            Daftar sebagai Dosen <span
                                class="ml-2 font-medium lowercase text-black/20"
                                >(Perlu Peninjauan)</span
                            >
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="group relative h-14 w-full overflow-hidden rounded-2xl bg-[#111] font-bold tracking-widest text-white uppercase transition-all hover:bg-black active:scale-[0.98] disabled:opacity-50"
                        disabled={state.form.processing}
                    >
                        <div class="relative z-10 flex items-center justify-center gap-3">
                            {#if state.form.processing}
                                <Loader2 size={18} class="animate-spin" />
                                <span>Mendaftarkan...</span>
                            {:else}
                                <span>Buat Akun Sekarang</span>
                                <UserPlus
                                    size={18}
                                    class="transition-transform group-hover:scale-110"
                                />
                            {/if}
                        </div>
                    </button>

                    <p
                        class="mt-8 text-center text-xs font-bold tracking-widest text-black/30 uppercase"
                    >
                        Sudah punya akun?
                        <Link
                            href={ROUTES.AUTH.LOGIN}
                            class="text-black underline underline-offset-8 transition-all hover:text-black/60"
                            >Masuk ke Akun</Link
                        >
                    </p>
                </form>
            </div>
        </div>
    </div>
</App>

