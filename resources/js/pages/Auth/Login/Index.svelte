<script>
    import GuestLayout from "@/layouts/GuestLayout.svelte";
    import Card from "@/components/ui/Card.svelte";
    import Input from "@/components/ui/Input.svelte";
    import Button from "@/components/ui/Button.svelte";
    import Alert from "@/components/ui/Alert.svelte";
    import { Link } from "@inertiajs/svelte";
    import { Loader2, ArrowRight, Ghost } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";
    import { LoginState } from "@/states/Auth/AuthState.svelte";

    const state = new LoginState();
    const form = state.form;
</script>

<GuestLayout title="Login - OOPedia">
    <Card padding="p-10" hover={false}>
        <div slot="header" class="text-center w-full mb-6">
            <h3 class="text-xl font-bold tracking-widest text-slate-900">
                MASUK KE AKUN
            </h3>
            <p
                class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2"
            >
                Gunakan akun OOPedia Anda
            </p>
        </div>

        <form on:submit|preventDefault={() => state.submit()} class="space-y-6">
            {#if $form.errors.email}
                <Alert variant="danger" dismissible={true}
                    >{$form.errors.email}</Alert
                >
            {/if}

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
                    placeholder="nama@email.com"
                    required
                    error={$form.errors.email}
                />
            </div>

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
                        MASUK SEKARANG <ArrowRight size={18} class="ml-3" />
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
                    <span class="bg-white px-4 text-slate-400">Atau</span>
                </div>
            </div>

            <div class="flex flex-col gap-4">
                <p
                    class="text-center text-xs font-bold text-slate-500 uppercase tracking-wider"
                >
                    Tidak memiliki akun?
                    <Link
                        href={ROUTES.AUTH.REGISTER}
                        class="text-primary-600 hover:underline"
                        >Daftar Gratis</Link
                    >
                </p>
                <Button
                    type="button"
                    variant="ghost"
                    size="sm"
                    icon={Ghost}
                    on:click={() => state.submitAsGuest()}
                    class="w-full text-slate-400 hover:text-slate-900"
                >
                    Masuk Sebagai Tamu
                </Button>
            </div>
        </form>
    </Card>
</GuestLayout>
