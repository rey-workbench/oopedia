<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Input from '@/components/ui/Input.svelte';
    import Button from '@/components/ui/Button.svelte';
    import { Link, page } from '@inertiajs/svelte';
    import { Loader2, ArrowLeft, Mail } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import { ForgotPasswordState } from '@/states/Auth/AuthState.svelte';

    const state = new ForgotPasswordState({
        showSuccessToast: 'Link reset password telah dikirim ke email Anda!',
        showErrorToast: true,
    });

    const status = $derived(page.props['status'] as string);
</script>

<App variant="auth" title="Lupa Password - OOPedia">
    <div class="relative flex min-h-screen flex-col bg-slate-50 px-4 py-4 font-sans text-slate-900 antialiased">
        <!-- Header -->
        <div class="mx-auto flex w-full max-w-4xl items-center justify-between p-2">
            <Link
                href={ROUTES.AUTH.LOGIN}
                class="flex items-center gap-2 p-2 text-slate-400 transition hover:text-slate-600"
            >
                <ArrowLeft size={24} strokeWidth={2.5} />
                <span class="text-sm font-bold uppercase tracking-widest">Login</span>
            </Link>
        </div>

        <!-- Main Content -->
        <div class="mx-auto -mt-12 flex w-full max-w-sm flex-1 flex-col items-center justify-center sm:-mt-20">
            <div class="mb-6 flex h-20 w-20 items-center justify-center rounded-3xl border-2 border-b-4 border-primary-200 bg-primary-50 text-primary-500">
                <Mail size={40} strokeWidth={2.5} />
            </div>

            <h1 class="mb-2 text-center text-3xl font-black tracking-tight">Lupa Password?</h1>
            <p class="mb-8 text-center text-sm font-medium text-slate-500">
                Masukkan email Anda dan kami akan mengirimkan link untuk mengatur ulang password Anda.
            </p>

            {#if status}
                <div class="mb-6 w-full rounded-2xl border-2 border-b-4 border-emerald-200 bg-emerald-50 p-4 text-center text-sm font-bold text-emerald-600">
                    {status}
                </div>
            {/if}

            <form
                onsubmit={(event) => {
                    event.preventDefault();
                    state.submit();
                }}
                class="w-full space-y-6"
            >
                <Input
                    id="email"
                    type="email"
                    bind:value={state.form.email}
                    placeholder="Masukkan email Anda"
                    autocomplete="email"
                    required
                    error={state.form.errors['email']}
                    inputClass="h-14 w-full rounded-2xl border-2 border-slate-300 bg-slate-50 px-5 text-base font-bold text-slate-900 placeholder:text-slate-400 focus:border-primary-500 focus:bg-white focus:ring-0 transition-colors"
                    label=""
                />

                <Button
                    type="submit"
                    variant="primary"
                    size="md"
                    class="w-full text-[15px]"
                    disabled={state.form.processing}
                >
                    {#if state.form.processing}
                        <Loader2 size={22} class="animate-spin mr-2" />
                        Mengirim...
                    {:else}
                        Kirim Link Reset
                    {/if}
                </Button>
            </form>

            <div class="mt-8 text-center text-[12px] font-bold text-slate-400">
                Tiba-tiba ingat password? 
                <Link href={ROUTES.AUTH.LOGIN} class="font-black text-primary-600 hover:text-primary-800 transition">
                    Kembali ke Login
                </Link>
            </div>
        </div>
    </div>
</App>
