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
    <div class="relative min-h-screen bg-slate-50 text-slate-900 flex flex-col px-4 py-4 font-sans antialiased">
        <!-- Header -->
        <div class="flex items-center justify-between w-full p-2 max-w-4xl mx-auto">
            <Link href={ROUTES.HOME} class="p-2 text-slate-400 hover:text-slate-600 transition" aria-label="Kembali">
                <X size={32} strokeWidth={2.5} />
            </Link>
            <Link href={ROUTES.AUTH.LOGIN} class="px-5 py-2.5 font-bold tracking-widest text-[13px] text-slate-500 border-2 border-b-4 border-slate-300 rounded-2xl uppercase hover:bg-slate-100 active:translate-y-[2px] active:border-b-2 transition">
                Log In
            </Link>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col items-center justify-center w-full max-w-sm mx-auto pb-12 pt-4">
            <h1 class="text-[26px] font-black text-center mb-8 tracking-tight">Create your profile</h1>

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
                        class="flex items-center gap-3 rounded-2xl border-2 border-slate-300 bg-slate-50 p-4 cursor-pointer hover:bg-slate-100 transition"
                    >
                        <input
                            id="register_as_admin"
                            type="checkbox"
                            bind:checked={state.form.register_as_admin}
                            class="h-5 w-5 rounded border-2 border-slate-300 text-primary-500 focus:ring-primary-500"
                        />
                        <span class="text-sm font-bold text-slate-600 leading-snug tracking-wide">
                            Daftar sebagai Dosen/Admin
                        </span>
                    </label>
                </div>

                <div class="pt-3">
                    <Button
                        type="submit"
                        variant="primary"
                        size="md"
                        class="w-full h-12 text-[15px]"
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

            <div class="mt-8 text-center text-[12px] font-bold text-slate-400 leading-relaxed max-w-[280px]">
                By signing in to OOPedia, you agree to our <a href="#" class="font-black text-slate-500 hover:text-slate-700 transition">Terms</a> and <a href="#" class="font-black text-slate-500 hover:text-slate-700 transition">Privacy Policy</a>.
            </div>
            
            <div class="mt-4 text-center text-[11px] font-bold text-slate-400 leading-relaxed max-w-[320px]">
                This site is protected by reCAPTCHA Enterprise and the Google <a href="#" class="font-black text-slate-500 hover:text-slate-700 transition">Privacy Policy</a> and <a href="#" class="font-black text-slate-500 hover:text-slate-700 transition">Terms of Service</a> apply.
            </div>
        </div>
    </div>
</App>