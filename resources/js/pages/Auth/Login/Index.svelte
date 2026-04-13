<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Input from '@/components/ui/Input.svelte';
    import { Link } from '@inertiajs/svelte';
    import { Loader2, ArrowRight, CircleHelp, Ghost } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import { LoginState } from '@/states/Auth/AuthState.svelte';
    import { tutorialState } from '@/states/ui/tutorialState.svelte';

    const state = new LoginState({
        showSuccessToast: 'Selamat datang kembali!',
        showErrorToast: true,
    });
</script>

<App variant="auth" title="Login - OOPedia">
    <div class="grid min-h-screen grid-cols-1 bg-[#e7e7e7] lg:grid-cols-2">
        <section class="relative flex min-h-screen flex-col bg-[#efefef] px-6 py-8 sm:px-10 lg:px-16">
            <div class="mb-8 flex items-center justify-between sm:mb-10">
                <Link href={ROUTES.HOME} class="flex items-center gap-3 text-slate-900">
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-300 bg-white"
                    >
                        <img src="/images/logo.png" alt="Logo OOPedia" class="h-7 w-auto" />
                    </div>
                    <span class="text-xs font-black tracking-[0.25em] uppercase">OOPedia</span>
                </Link>

                <button
                    type="button"
                    onclick={() => tutorialState.startTour('auth_login', true)}
                    class="rounded-xl p-2 text-slate-500 transition hover:bg-white hover:text-slate-900"
                    title="Bantuan Tutorial"
                >
                    <CircleHelp size={20} />
                </button>
            </div>

            <div class="mx-auto flex w-full max-w-sm flex-1 flex-col justify-center">
                <div class="mb-8">
                    <h1 class="text-3xl font-black tracking-tight text-slate-900">Welcome back</h1>
                    <p class="mt-2 text-sm font-medium text-slate-500">
                        Masuk untuk melanjutkan progres belajar adaptif Anda di OOPedia.
                    </p>
                </div>

                <form
                    onsubmit={(event) => {
                        event.preventDefault();
                        state.submit();
                    }}
                    class="space-y-4"
                >
                    <Input
                        id="email"
                        type="email"
                        bind:value={state.form.email}
                        placeholder="Email"
                        autocomplete="email"
                        required
                        error={state.form.errors['email']}
                        inputClass="h-14 rounded-2xl border border-slate-300 bg-transparent px-5 text-base font-semibold text-slate-700 placeholder:text-slate-400 focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                    />

                    <Input
                        id="password"
                        type="password"
                        bind:value={state.form.password}
                        placeholder="Password"
                        autocomplete="current-password"
                        required
                        error={state.form.errors['password']}
                        inputClass="h-14 rounded-2xl border border-slate-300 bg-transparent px-5 text-base font-semibold text-slate-700 placeholder:text-slate-400 focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                    />

                    <button
                        id="login-submit-btn"
                        type="submit"
                        class="mt-2 flex h-14 w-full items-center justify-center gap-2 rounded-2xl bg-slate-900 text-sm font-black tracking-[0.12em] text-white uppercase transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:bg-slate-300"
                        disabled={state.form.processing}
                    >
                        {#if state.form.processing}
                            <Loader2 size={18} class="animate-spin" />
                            <span>Memproses</span>
                        {:else}
                            <span>Next</span>
                            <ArrowRight size={16} />
                        {/if}
                    </button>
                </form>

                <button
                    id="guest-login-btn"
                    type="button"
                    onclick={() => state.submitAsGuest()}
                    class="mt-4 flex h-12 w-full items-center justify-center gap-2 rounded-2xl border border-slate-300 bg-white text-xs font-black tracking-[0.14em] text-slate-700 uppercase transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                    disabled={state.form.processing}
                >
                    <Ghost size={16} />
                    <span>Masuk sebagai tamu</span>
                </button>

                <p class="mt-5 text-center text-sm font-semibold text-slate-400">Forgot password?</p>

                <p class="mt-6 text-center text-xs font-bold tracking-[0.16em] text-slate-500 uppercase">
                    Belum punya akun?
                    <Link href={ROUTES.AUTH.REGISTER} class="text-slate-900 underline underline-offset-4"
                        >Sign up</Link
                    >
                </p>
            </div>

            <div class="pt-6 text-center text-3xl font-black tracking-tight text-slate-900">OOPedia</div>
        </section>

        <section class="relative hidden min-h-screen overflow-hidden bg-[#e3e3e3] p-8 lg:block xl:p-10">
            <div class="mx-auto flex h-full max-w-[560px] flex-col justify-center gap-4">
                <div class="ml-auto h-36 w-64 overflow-hidden rounded-sm bg-slate-300 shadow-sm xl:h-40 xl:w-72">
                    <img
                        src="/images/landing/jti.png"
                        alt="Inspirasi ruang belajar"
                        class="h-full w-full object-cover"
                    />
                </div>

                <div class="mx-auto h-[52vh] w-full max-w-[520px] overflow-hidden rounded-sm bg-slate-300 shadow-sm">
                    <img
                        src="/images/materials/C08ZSJEVqjBVu44FXcZveWyUzDnR0GcUEj374m5X.jpg"
                        alt="Ilustrasi suasana belajar"
                        class="h-full w-full object-cover"
                    />
                </div>

                <div class="ml-20 h-28 w-52 overflow-hidden rounded-sm bg-slate-300 shadow-sm xl:h-32 xl:w-60">
                    <img
                        src="/images/landing/polinema.png"
                        alt="Komunitas pembelajar"
                        class="h-full w-full object-cover"
                    />
                </div>

                <p class="pt-2 text-xs font-bold tracking-tight text-slate-700">
                    Belajar terasa personal, bukan generik. OOPedia menyesuaikan langkah sesuai
                    ritme Anda.
                </p>
            </div>
        </section>
    </div>
</App>