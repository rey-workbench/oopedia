<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Input from '@/components/ui/Input.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Alert from '@/components/ui/Alert.svelte';
    import { Link } from '@inertiajs/svelte';
    import { Loader2, ArrowRight, Ghost } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import { LoginState } from '@/states/Auth/AuthState.svelte';

    const state = new LoginState();
</script>

<App variant="auth" title="Login - OOPedia">
    <Card padding="p-10" hover={false}>
        {#snippet header()}
            <div class="mb-6 w-full text-center">
                <h3 class="text-xl font-bold tracking-widest text-slate-900">MASUK KE AKUN</h3>
                <p class="mt-2 text-[10px] font-bold tracking-widest text-slate-400 uppercase">
                    Gunakan akun OOPedia Anda
                </p>
            </div>
        {/snippet}

        <form
            onsubmit={(e) => {
                e.preventDefault();
                state.submit();
            }}
            class="space-y-6"
        >
            {#if state.form.errors['email']}
                <Alert variant="danger" dismissible={true}>{state.form.errors['email']}</Alert>
            {/if}

            <div class="space-y-2">
                <label
                    for="email"
                    class="text-xs font-bold tracking-wider text-slate-700 uppercase"
                >
                    Alamat Email <span class="text-rose-500">*</span>
                </label>
                <Input
                    id="email"
                    type="email"
                    bind:value={state.form.email}
                    placeholder="nama@email.com"
                    required
                    error={state.form.errors['email']}
                />
            </div>

            <div class="space-y-2">
                <label
                    for="password"
                    class="text-xs font-bold tracking-wider text-slate-700 uppercase"
                >
                    Kata Sandi <span class="text-rose-500">*</span>
                </label>
                <Input
                    id="password"
                    type="password"
                    bind:value={state.form.password}
                    placeholder="••••••••"
                    required
                    error={state.form.errors['password']}
                />
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="bg-primary-600 hover:bg-primary-700 flex w-full items-center justify-center gap-3 rounded-2xl px-6 py-4 text-xs font-bold tracking-widest text-white uppercase transition-all disabled:opacity-50"
                    disabled={state.form.processing}
                >
                    {#if state.form.processing}
                        <Loader2 size={18} class="animate-spin" /> MEMPROSES...
                    {:else}
                        MASUK SEKARANG <ArrowRight size={18} />
                    {/if}
                </button>
            </div>

            <div class="relative py-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-100"></div>
                </div>
                <div
                    class="relative flex justify-center text-[10px] font-bold tracking-widest uppercase"
                >
                    <span class="bg-white px-4 text-slate-400">Atau</span>
                </div>
            </div>

            <div class="flex flex-col gap-4">
                <p class="text-center text-xs font-bold tracking-wider text-slate-500 uppercase">
                    Tidak memiliki akun?
                    <Link href={ROUTES.AUTH.REGISTER} class="text-primary-600 hover:underline"
                        >Daftar Gratis</Link
                    >
                </p>
                <Button
                    type="button"
                    variant="ghost"
                    size="sm"
                    icon={Ghost}
                    onclick={() => state.submitAsGuest()}
                    class="w-full text-slate-400 hover:text-slate-900"
                >
                    Masuk Sebagai Tamu
                </Button>
            </div>
        </form>
    </Card>
</App>
