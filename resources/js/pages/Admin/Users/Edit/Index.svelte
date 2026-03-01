<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/shared/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
        import Input from "@/components/ui/Input.svelte";
    import InfoPanel from "@/components/shared/InfoPanel.svelte";
    import { ArrowLeft, Save, Lock } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";
    import { UserFormState } from "@/states/Admin/UserState.svelte";

    export let user;

    const state = new UserFormState(user);
    const form = state.form;

    const safeUpdateItems = [
        "Kosongkan kolom password jika tidak ingin mengubahnya",
        "Password lama tidak diperlukan untuk reset",
        "Perubahan email mungkin memerlukan verifikasi ulang",
        "Semua perubahan dicatat dalam log sistem",
    ];
</script>

<App title="Edit Administrator">
    <div class="space-y-12">
        <PageHeader
            title="Pembaruan Kredensial"
            subtitle="Modifikasi data identitas dan kunci keamanan entitas."
        >
            <div slot="actions">
                <Button
                    href={ROUTES.ADMIN.USERS.INDEX}
                    variant="ghost"
                    icon={ArrowLeft}>KEMBALI KE DAFTAR</Button
                >
            </div>
        </PageHeader>

        
<form onsubmit={(e) => { e.preventDefault(); () => state.submit()(e); }} class="space-y-12">
    <div class="bg-white rounded-3xl p-6 shadow-2xl border border-slate-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
        <div class="mb-6">
            <h3 class="text-lg font-bold text-slate-800">
                Modifikasi Identitas Admin
            </h3>
        </div>

        <div class="space-y-10 p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <div class="lg:col-span-2">
                    <div class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-bold uppercase text-slate-400 tracking-widest"
                                    >Nama Lengkap</label
                                >
                                <Input
                                    bind:value={$form.name}
                                    placeholder="John Doe"
                                    error={$form.errors.name}
                                />
                            </div>
                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-bold uppercase text-slate-400 tracking-widest"
                                    >Alamat Email</label
                                >
                                <Input
                                    type="email"
                                    bind:value={$form.email}
                                    placeholder="john@email.com"
                                    error={$form.errors.email}
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-bold uppercase text-slate-400 tracking-widest"
                                >
                                    Password Baru <span
                                        class="text-slate-300 normal-case font-normal tracking-normal"
                                        >(Opsional)</span
                                    >
                                </label>
                                <Input
                                    type="password"
                                    bind:value={$form.password}
                                    placeholder="••••••••"
                                    error={$form.errors.password}
                                />
                            </div>
                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-bold uppercase text-slate-400 tracking-widest"
                                >
                                    Konfirmasi <span
                                        class="text-slate-300 normal-case font-normal tracking-normal"
                                        >(Opsional)</span
                                    >
                                </label>
                                <Input
                                    type="password"
                                    bind:value={$form.password_confirmation}
                                    placeholder="••••••••"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-1">
                    <InfoPanel
                        icon={Lock}
                        title="Pembaruan Aman"
                        items={safeUpdateItems}
                    />
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    
                </div>

                <div class="flex gap-4">
                    
                    <Button href={ROUTES.ADMIN.USERS.INDEX} variant="ghost">
                        <span class="text-[10px] font-bold uppercase text-slate-400 tracking-widest">BATAL</span>
                    </Button>
                    <Button
                        type="submit"
                        variant="primary"
                        size="lg"
                        class="shadow-xl shadow-primary-900/20"
                        icon={Save}
                        disabled={$form.processing}
                    >
                        {#if $form.processing}
                            Memproses...
                        {:else}
                            SIMPAN PERUBAHAN
                        {/if}
                    </Button>
                </div>
            </div>
        </div>
    </div>
</form>
    </div>
</App>
