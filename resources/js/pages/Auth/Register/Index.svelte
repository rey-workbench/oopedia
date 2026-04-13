<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Input from '@/components/ui/Input.svelte';
    import { Link } from '@inertiajs/svelte';
    import { Loader2, UserPlus, CircleHelp } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import { RegisterState } from '@/states/Auth/AuthState.svelte';
    import { tutorialState } from '@/states/ui/tutorialState.svelte';

    const state = new RegisterState({
        showSuccessToast: 'Registrasi berhasil!',
        showErrorToast: true,
    });
</script>

<App variant="auth" title="Daftar - OOPedia">
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
                    onclick={() => tutorialState.startTour('auth_register', true)}
                    class="rounded-xl p-2 text-slate-500 transition hover:bg-white hover:text-slate-900"
                    title="Bantuan Tutorial"
                >
                    <CircleHelp size={20} />
                </button>
            </div>

            <div class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center">
                <div class="mb-8">
                    <h1 class="text-3xl font-black tracking-tight text-slate-900">Create account</h1>
                    <p class="mt-2 text-sm font-medium text-slate-500">
                        Daftar untuk mulai belajar adaptif dan memantau progres Anda secara
                        terstruktur.
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
                        id="name"
                        type="text"
                        bind:value={state.form.name}
                        placeholder="Nama lengkap"
                        autocomplete="name"
                        required
                        error={state.form.errors['name']}
                        inputClass="h-13 rounded-2xl border border-slate-300 bg-transparent px-5 text-base font-semibold text-slate-700 placeholder:text-slate-400 focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                    />

                    <Input
                        id="email"
                        type="email"
                        bind:value={state.form.email}
                        placeholder="Email"
                        autocomplete="email"
                        required
                        error={state.form.errors['email']}
                        inputClass="h-13 rounded-2xl border border-slate-300 bg-transparent px-5 text-base font-semibold text-slate-700 placeholder:text-slate-400 focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                    />

                    <Input
                        id="password"
                        type="password"
                        bind:value={state.form.password}
                        placeholder="Password"
                        autocomplete="new-password"
                        required
                        error={state.form.errors['password']}
                        inputClass="h-13 rounded-2xl border border-slate-300 bg-transparent px-5 text-base font-semibold text-slate-700 placeholder:text-slate-400 focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                    />

                    <Input
                        id="password_confirmation"
                        type="password"
                        bind:value={state.form.password_confirmation}
                        placeholder="Konfirmasi password"
                        autocomplete="new-password"
                        required
                        inputClass="h-13 rounded-2xl border border-slate-300 bg-transparent px-5 text-base font-semibold text-slate-700 placeholder:text-slate-400 focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                    />

                    <label
                        for="register_as_admin"
                        class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-300 bg-white/70 p-3"
                    >
                        <input
                            id="register_as_admin"
                            type="checkbox"
                            bind:checked={state.form.register_as_admin}
                            class="mt-0.5 h-4 w-4 rounded border-slate-400 text-slate-900 focus:ring-slate-400"
                        />
                        <span class="text-xs font-semibold text-slate-600">
                            Daftar sebagai dosen (akun menunggu persetujuan admin).
                        </span>
                    </label>

                    <button
                        id="register-submit-btn"
                        type="submit"
                        class="mt-2 flex h-14 w-full items-center justify-center gap-2 rounded-2xl bg-slate-900 text-sm font-black tracking-[0.12em] text-white uppercase transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:bg-slate-300"
                        disabled={state.form.processing}
                    >
                        {#if state.form.processing}
                            <Loader2 size={18} class="animate-spin" />
                            <span>Memproses</span>
                        {:else}
                            <UserPlus size={16} />
                            <span>Create account</span>
                        {/if}
                    </button>
                </form>

                <p class="mt-6 text-center text-xs font-bold tracking-[0.16em] text-slate-500 uppercase">
                    Sudah punya akun?
                    <Link href={ROUTES.AUTH.LOGIN} class="text-slate-900 underline underline-offset-4"
                        >Sign in</Link
                    >
                </p>
            </div>

            <div class="pt-6 text-center text-3xl font-black tracking-tight text-slate-900">OOPedia</div>
        </section>

        <section class="relative hidden min-h-screen overflow-hidden bg-[#e3e3e3] p-8 lg:block xl:p-10">
            <div class="mx-auto flex h-full max-w-[560px] flex-col justify-center gap-4">
                <div class="ml-auto h-36 w-64 overflow-hidden rounded-sm bg-slate-300 shadow-sm xl:h-40 xl:w-72">
                    <img
                        src="/images/landing/polinema.png"
                        alt="Inspirasi ruang belajar"
                        class="h-full w-full object-cover"
                    />
                </div>

                <div class="mx-auto h-[52vh] w-full max-w-[520px] overflow-hidden rounded-sm bg-slate-300 shadow-sm">
                    <img
                        src="/images/materials/XC0lP6UTySZSqR7CCALgzAf6kVoh9CBDfiIReF1F.png"
                        alt="Ilustrasi pembelajaran"
                        class="h-full w-full object-cover"
                    />
                </div>

                <div class="ml-20 h-28 w-52 overflow-hidden rounded-sm bg-slate-300 shadow-sm xl:h-32 xl:w-60">
                    <img
                        src="/images/landing/jti.png"
                        alt="Ekosistem kampus"
                        class="h-full w-full object-cover"
                    />
                </div>

                <p class="pt-2 text-xs font-bold tracking-tight text-slate-700">
                    Akun baru langsung terhubung ke jalur belajar adaptif OOPedia dan sistem
                    evaluasi progres.
                </p>
            </div>
        </section>
    </div>
</App>