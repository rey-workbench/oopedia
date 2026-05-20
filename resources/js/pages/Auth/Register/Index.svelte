<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Input from '@/components/ui/Input.svelte';
    import Button from '@/components/ui/Button.svelte';
    import { Link } from '@inertiajs/svelte';
    import { Loader2, UserPlus, X } from '@lucide/svelte';
    import { ROUTES } from '@/utils/route';
    import { RegisterState } from '@/states/Auth/AuthState.svelte';

    const state = new RegisterState({
        showSuccessToast: 'Registrasi berhasil!',
        showErrorToast: true,
    });
</script>

<App variant="auth" title="Daftar - OOPedia">
    <div
        class="relative flex min-h-screen flex-col bg-slate-50 px-4 py-4 font-sans text-slate-900 antialiased"
    >
        <!-- Header -->
        <div class="mx-auto flex w-full max-w-4xl items-center justify-between p-2">
            <Link
                href={ROUTES.HOME}
                class="p-2 text-slate-400 transition hover:text-slate-600"
                aria-label="Kembali"
            >
                <X size={32} strokeWidth={2.5} />
            </Link>
            <Button
                href={ROUTES.AUTH.LOGIN}
                variant="outline"
                size="sm"
                class="text-[13px] uppercase"
            >
                Log In
            </Button>
        </div>

        <!-- Main Content -->
        <div
            class="mx-auto flex w-full max-w-sm flex-1 flex-col items-center justify-center pt-4 pb-12"
        >
            <h1 class="mb-8 text-center text-[26px] font-black tracking-tight">
                Create your profile
            </h1>

            <form
                onsubmit={(event) => {
                    event.preventDefault();
                    state.submit();
                }}
                class="w-full space-y-4"
            >
                <div class="space-y-[14px]">
                    <Input
                        id="name"
                        type="text"
                        bind:value={state.form.name}
                        placeholder="Nama Lengkap"
                        autocomplete="name"
                        required
                        error={state.form.errors['name']}
                        inputClass="h-14 w-full rounded-2xl border-2 border-slate-300 bg-slate-50 px-5 text-base font-bold text-slate-900 placeholder:text-slate-400 focus:border-primary-500 focus:bg-white focus:ring-0 transition-colors"
                        label=""
                    />

                    <Input
                        id="email"
                        type="email"
                        bind:value={state.form.email}
                        placeholder="Email atau nama pengguna"
                        autocomplete="email"
                        required
                        error={state.form.errors['email']}
                        inputClass="h-14 w-full rounded-2xl border-2 border-slate-300 bg-slate-50 px-5 text-base font-bold text-slate-900 placeholder:text-slate-400 focus:border-primary-500 focus:bg-white focus:ring-0 transition-colors"
                        label=""
                    />

                    <Input
                        id="password"
                        type="password"
                        bind:value={state.form.password}
                        placeholder="Kata sandi"
                        autocomplete="new-password"
                        required
                        error={state.form.errors['password']}
                        inputClass="h-14 w-full rounded-2xl border-2 border-slate-300 bg-slate-50 px-5 text-base font-bold text-slate-900 placeholder:text-slate-400 focus:border-primary-500 focus:bg-white focus:ring-0 transition-colors"
                        label=""
                    />

                    <Input
                        id="password_confirmation"
                        type="password"
                        bind:value={state.form.password_confirmation}
                        placeholder="Konfirmasi kata sandi"
                        autocomplete="new-password"
                        required
                        error={state.form.errors['password_confirmation']}
                        inputClass="h-14 w-full rounded-2xl border-2 border-slate-300 bg-slate-50 px-5 text-base font-bold text-slate-900 placeholder:text-slate-400 focus:border-primary-500 focus:bg-white focus:ring-0 transition-colors"
                        label=""
                    />
                </div>

                <div class="pt-1">
                    <label
                        for="register_as_admin"
                        class="flex cursor-pointer items-center gap-3 rounded-2xl border-2 border-slate-300 bg-slate-50 p-4 transition hover:bg-slate-100"
                    >
                        <input
                            id="register_as_admin"
                            type="checkbox"
                            bind:checked={state.form.register_as_admin}
                            class="text-primary-500 focus:ring-primary-500 h-5 w-5 rounded border-2 border-slate-300"
                        />
                        <span class="text-sm leading-snug font-bold tracking-wide text-slate-600">
                            Daftar sebagai Dosen/Admin
                        </span>
                    </label>
                </div>

                <div class="pt-3">
                    <Button
                        type="submit"
                        variant="primary"
                        size="md"
                        class="w-full text-[15px]"
                        disabled={state.form.processing}
                    >
                        {#if state.form.processing}
                            <Loader2 size={22} class="animate-spin" />
                        {:else}
                            <UserPlus size={18} class="mr-1" />
                            Sign up
                        {/if}
                    </Button>
                </div>
            </form>

            <div class="my-6 flex w-full items-center gap-4">
                <div class="h-[2px] flex-1 bg-slate-200"></div>
                <span class="text-sm font-bold tracking-widest text-slate-400 uppercase">Or</span>
                <div class="h-[2px] flex-1 bg-slate-200"></div>
            </div>

            <div class="w-full space-y-3">
                <a
                    href="/auth/google"
                    class="group inline-flex w-full items-center justify-center rounded-2xl border-2 border-b-6 border-slate-200 border-b-slate-400 bg-white px-6 py-3 text-sm text-[15px] font-black tracking-widest text-slate-600 uppercase shadow-none transition-all duration-150 select-none hover:bg-slate-50 hover:text-slate-700 active:translate-y-[4px] active:border-b-2"
                >
                    <svg class="mr-2 h-5 w-5" viewBox="0 0 24 24">
                        <path
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                            fill="#4285F4"
                        ></path>
                        <path
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                            fill="#34A853"
                        ></path>
                        <path
                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                            fill="#FBBC05"
                        ></path>
                        <path
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                            fill="#EA4335"
                        ></path>
                        <path d="M1 1h22v22H1z" fill="none"></path>
                    </svg>
                    <span>Google</span>
                </a>
            </div>

            <div
                class="mt-8 max-w-[280px] text-center text-[12px] leading-relaxed font-bold text-slate-400"
            >
                By signing in to OOPedia, you agree to our <a
                    href="/"
                    class="font-black text-slate-500 transition hover:text-slate-700">Terms</a
                >
                and
                <a href="/" class="font-black text-slate-500 transition hover:text-slate-700"
                    >Privacy Policy</a
                >.
            </div>

            <div
                class="mt-4 max-w-[320px] text-center text-[11px] leading-relaxed font-bold text-slate-400"
            >
                This site is protected by reCAPTCHA Enterprise and the Google <a
                    href="/"
                    class="font-black text-slate-500 transition hover:text-slate-700"
                    >Privacy Policy</a
                >
                and
                <a href="/" class="font-black text-slate-500 transition hover:text-slate-700"
                    >Terms of Service</a
                > apply.
            </div>
        </div>
    </div>
</App>
