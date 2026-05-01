<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Input from '@/components/ui/Input.svelte';
    import Button from '@/components/ui/Button.svelte';
    import { Loader2, KeyRound } from 'lucide-svelte';
    import { ResetPasswordState } from '@/states/Auth/AuthState.svelte';

    interface Props {
        email: string;
        token: string;
    }

    const { email, token }: Props = $props();

    const state = new ResetPasswordState(email, token, {
        showSuccessToast: 'Password Anda telah berhasil diubah!',
        showErrorToast: true,
    });
</script>

<App variant="auth" title="Reset Password - OOPedia">
    <div class="relative flex min-h-screen flex-col bg-slate-50 px-4 py-4 font-sans text-slate-900 antialiased">
        <!-- Main Content -->
        <div class="mx-auto flex w-full max-w-sm flex-1 flex-col items-center justify-center">
            <div class="mb-6 flex h-20 w-20 items-center justify-center rounded-3xl border-2 border-b-4 border-primary-200 bg-primary-50 text-primary-500">
                <KeyRound size={40} strokeWidth={2.5} />
            </div>

            <h1 class="mb-2 text-center text-3xl font-black tracking-tight">Atur Password Baru</h1>
            <p class="mb-8 text-center text-sm font-medium text-slate-500">
                Silakan buat password baru yang kuat untuk akun Anda.
            </p>

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
                        placeholder="Email Anda"
                        autocomplete="email"
                        readonly
                        required
                        error={state.form.errors['email']}
                        inputClass="h-14 w-full rounded-2xl border-2 border-slate-200 bg-slate-100 px-5 text-base font-bold text-slate-500 cursor-not-allowed"
                        label=""
                    />

                    <Input
                        id="password"
                        type="password"
                        bind:value={state.form.password}
                        placeholder="Password Baru"
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
                        placeholder="Konfirmasi Password Baru"
                        autocomplete="new-password"
                        required
                        error={state.form.errors['password_confirmation']}
                        inputClass="h-14 w-full rounded-2xl border-2 border-slate-300 bg-slate-50 px-5 text-base font-bold text-slate-900 placeholder:text-slate-400 focus:border-primary-500 focus:bg-white focus:ring-0 transition-colors"
                        label=""
                    />
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
                            <Loader2 size={22} class="animate-spin mr-2" />
                            Memproses...
                        {:else}
                            Ubah Password
                        {/if}
                    </Button>
                </div>
            </form>
        </div>
    </div>
</App>
