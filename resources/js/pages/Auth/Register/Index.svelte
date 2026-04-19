<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Input from '@/components/ui/Input.svelte';
    import Button from '@/components/ui/Button.svelte';
    import { Link } from '@inertiajs/svelte';
    import { Loader2, UserPlus, X } from 'lucide-svelte';
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
