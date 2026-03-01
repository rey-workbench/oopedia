<script>
    import App from "@/layouts/App.svelte";
    import Card from "@/components/ui/Card.svelte";
    import Input from "@/components/ui/Input.svelte";
    import Button from "@/components/ui/Button.svelte";
    import { Link } from "@inertiajs/svelte";
    import { Loader2, UserPlus } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";
    import { RegisterState } from "@/states/Auth/AuthState.svelte";

    const state = new RegisterState();
    const form = state.form;
</script>

<App variant="auth" title="Daftar - OOPedia">
    <Card padding="p-10" hover={false}>
        <div slot="header" class="text-center w-full mb-6">
            <h3 class="text-xl font-bold tracking-widest text-slate-900">
                BUAT AKUN BARU
            </h3>
            <p
                class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2"
            >
                Bergabunglah dengan komunitas OOPedia
            </p>
        </div>

        <form on:submit|preventDefault={() => state.submit()} class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label
                        for="name"
                        class="text-xs font-bold text-slate-700 uppercase tracking-wider"
                    >
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <Input
                        id="name"
                        type="text"
                        bind:value={$form.name}
                        placeholder="John Doe"
                        required
                        error={$form.errors.name}
                    />
                </div>
                <div class="space-y-2">
                    <label
                        for="email"
                        class="text-xs font-bold text-slate-700 uppercase tracking-wider"
                    >
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <Input
                        id="email"
                        type="email"
                        bind:value={$form.email}
                        placeholder="john@example.com"
                        required
                        error={$form.errors.email}
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label
                        for="password"
                        class="text-xs font-bold text-slate-700 uppercase tracking-wider"
                    >
                        Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <Input
                        id="password"
                        type="password"
                        bind:value={$form.password}
                        placeholder="••••••••"
                        required
                        error={$form.errors.password}
                    />
                </div>
                <div class="space-y-2">
                    <label
                        for="password_confirmation"
                        class="text-xs font-bold text-slate-700 uppercase tracking-wider"
                    >
                        Konfirmasi <span class="text-rose-500">*</span>
                    </label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        bind:value={$form.password_confirmation}
                        placeholder="••••••••"
                        required
                    />
                </div>
            </div>

            <div
                class="flex items-center gap-3 bg-slate-50 p-4 rounded-xl border border-slate-100"
            >
                <input
                    type="checkbox"
                    id="register_as_admin"
                    bind:checked={$form.register_as_admin}
                    class="w-5 h-5 rounded border-slate-300 text-primary-600 focus:ring-primary-500 transition-colors cursor-pointer"
                />
                <label
                    for="register_as_admin"
                    class="text-xs font-bold text-slate-600 uppercase tracking-wider cursor-pointer select-none flex-1"
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
                    disabled={$form.processing}
                >
                    {#if $form.processing}
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
                    class="relative flex justify-center text-[10px] uppercase font-bold tracking-widest"
                >
                    <span class="bg-white px-4 text-slate-400"
                        >Sudah punya akun?</span
                    >
                </div>
            </div>

            <div class="text-center">
                <Button
                    href={ROUTES.AUTH.LOGIN}
                    variant="secondary"
                    class="w-full">MASUK KE AKUN</Button
                >
            </div>
        </form>
    </Card>
</App>
