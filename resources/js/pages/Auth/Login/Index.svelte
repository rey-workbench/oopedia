<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Input from '@/components/ui/Input.svelte';
    import Button from '@/components/ui/Button.svelte';
    import { Link } from '@inertiajs/svelte';
    import { Loader2, Ghost, X } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import { LoginState } from '@/states/Auth/AuthState.svelte';

    const state = new LoginState({
        showSuccessToast: 'Selamat datang kembali!',
        showErrorToast: true,
    });
</script>

<App variant="auth" title="Login - OOPedia">
    <div class="relative min-h-screen bg-slate-50 text-slate-900 flex flex-col px-4 py-4 font-sans antialiased">
        <!-- Header -->
        <div class="flex items-center justify-between w-full p-2 max-w-4xl mx-auto">
            <Link href={ROUTES.HOME} class="p-2 text-slate-400 hover:text-slate-600 transition" aria-label="Kembali">
                <X size={32} strokeWidth={2.5} />
            </Link>
            <Button href={ROUTES.AUTH.REGISTER} variant="secondary" size="sm" class="text-[13px] uppercase">
                Sign Up
            </Button>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col items-center justify-center w-full max-w-sm mx-auto -mt-12 sm:-mt-20">
            <h1 class="text-3xl font-black text-center mb-8 tracking-tight">Log in</h1>

            <form
                onsubmit={(event) => {
                    event.preventDefault();
                    state.submit();
                }}
                class="w-full space-y-4"
            >
                <div class="space-y-[14px]">
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

                    <div class="relative">
                        <Input
                            id="password"
                            type="password"
                            bind:value={state.form.password}
                            placeholder="Kata sandi"
                            autocomplete="current-password"
                            required
                            error={state.form.errors['password']}
                            inputClass="h-14 w-full pr-24 rounded-2xl border-2 border-slate-300 bg-slate-50 px-5 text-base font-bold text-slate-900 placeholder:text-slate-400 focus:border-primary-500 focus:bg-white focus:ring-0 transition-colors"
                            label=""
                        />
                        <button type="button" class="absolute right-5 top-1/2 -translate-y-1/2 text-[13px] font-bold tracking-widest text-[#1cb0f6] uppercase hover:text-sky-400 transition z-10">
                            Forgot?
                        </button>
                    </div>
                </div>

                <div class="pt-2">
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
                            Log in
                        {/if}
                    </Button>
                </div>
            </form>

            <div class="flex items-center w-full my-6 gap-4">
                <div class="h-[2px] flex-1 bg-slate-200"></div>
                <span class="text-sm font-bold tracking-widest text-slate-400 uppercase">Or</span>
                <div class="h-[2px] flex-1 bg-slate-200"></div>
            </div>

            <div class="w-full space-y-3">
                <Button
                    type="button"
                    variant="secondary"
                    size="md"
                    class="w-full text-[15px]"
                    onclick={() => state.submitAsGuest()}
                    disabled={state.form.processing}
                >
                    <Ghost size={20} class="mr-2" />
                    <span>Tamu</span>
                </Button>
            </div>

            <div class="mt-8 text-center text-[12px] font-bold text-slate-400 leading-relaxed max-w-[280px]">
                By signing in to OOPedia, you agree to our <a href="/" class="font-black text-slate-500 hover:text-slate-700 transition">Terms</a> and <a href="/" class="font-black text-slate-500 hover:text-slate-700 transition">Privacy Policy</a>.
            </div>
        </div>
    </div>
</App>