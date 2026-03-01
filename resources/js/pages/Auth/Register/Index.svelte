<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Input from '@/components/ui/Input.svelte';
    import Button from '@/components/ui/Button.svelte';
    import { Loader2, UserPlus } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import { RegisterState } from '@/states/Auth/AuthState.svelte';

    const state = new RegisterState();
</script>

<App variant="auth" title="Daftar - OOPedia">
    <Card padding="p-10" hover={false}>
        {#snippet header()}
            <div class="mb-6 w-full text-center">
                <h3 class="text-xl font-bold tracking-widest text-slate-900">BUAT AKUN BARU</h3>
                <p class="mt-2 text-[10px] font-bold tracking-widest text-slate-400 uppercase">
                    Bergabunglah dengan komunitas OOPedia
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
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="space-y-2">
                    <label
                        for="name"
                        class="text-xs font-bold tracking-wider text-slate-700 uppercase"
                    >
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <Input
                        id="name"
                        type="text"
                        bind:value={state.form.name}
                        placeholder="John Doe"
                        required
                        error={state.form.errors['name']}
                    />
                </div>
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
                        placeholder="john@example.com"
                        required
                        error={state.form.errors['email']}
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
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
                <div class="space-y-2">
                    <label
                        for="password_confirmation"
                        class="text-xs font-bold tracking-wider text-slate-700 uppercase"
                    >
                        Konfirmasi <span class="text-rose-500">*</span>
                    </label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        bind:value={state.form.password_confirmation}
                        placeholder="••••••••"
                        required
                    />
                </div>
            </div>

            <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50 p-4">
                <input
                    type="checkbox"
                    id="register_as_admin"
                    bind:checked={state.form.register_as_admin}
                    class="text-primary-600 focus:ring-primary-500 h-5 w-5 cursor-pointer rounded border-slate-300 transition-colors"
                />
                <label
                    for="register_as_admin"
                    class="flex-1 cursor-pointer text-xs font-bold tracking-wider text-slate-600 uppercase select-none"
                >
                    Daftar sebagai Dosen (Perlu Approval)
                </label>
            </div>

            <div class="pt-2">
                <Button
                    type="submit"
                    variant="primary"
                    class="w-full"
                    size="lg"
                    disabled={state.form.processing}
                >
                    {#if state.form.processing}
                        <Loader2 size={18} class="mr-3 animate-spin" /> MEMPROSES...
                    {:else}
                        DAFTAR SEKARANG <UserPlus size={18} class="ml-3" />
                    {/if}
                </Button>
            </div>

            <div class="relative py-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-100"></div>
                </div>
                <div
                    class="relative flex justify-center text-[10px] font-bold tracking-widest uppercase"
                >
                    <span class="bg-white px-4 text-slate-400">Sudah punya akun?</span>
                </div>
            </div>

            <div class="text-center">
                <Button href={ROUTES.AUTH.LOGIN} variant="secondary" class="w-full"
                    >MASUK KE AKUN</Button
                >
            </div>
        </form>
    </Card>
</App>
