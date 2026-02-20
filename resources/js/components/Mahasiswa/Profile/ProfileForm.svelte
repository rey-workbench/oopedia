<script>
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import Input from "@/components/ui/Input.svelte";
    import Alert from "@/components/ui/Alert.svelte";
    import { useForm, page } from "@inertiajs/svelte";
    import { Loader2, Save, Check } from "lucide-svelte";

    export let user;

    let form = useForm({
        name: user.name,
        email: user.email,
        password: "",
        password_confirmation: "",
        _method: "PUT",
    });

    function handleSubmit() {
        $form.post("/mahasiswa/profile", {
            onSuccess: () => {
                form.reset("password", "password_confirmation");
            },
        });
    }

    $: flash = $page.props.flash || {};
</script>

<div class="lg:col-span-2 space-y-8">
    <h3 class="text-xl font-bold tracking-widest text-slate-900 uppercase">
        Pengaturan Profil
    </h3>

    <Card padding="p-8 md:p-12">
        {#if flash && flash.success}
            <Alert variant="success" className="mb-10">
                {flash.success}
            </Alert>
        {/if}

        <form on:submit|preventDefault={handleSubmit} class="space-y-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label
                        for="name"
                        class="block text-xs font-bold text-slate-400 uppercase tracking-widest px-1"
                        >Nama Lengkap</label
                    >
                    <Input
                        id="name"
                        type="text"
                        bind:value={$form.name}
                        className="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold focus:bg-white"
                        placeholder="Masukkan nama lengkap"
                        error={$form.errors.name}
                    />
                </div>

                <div class="space-y-3">
                    <label
                        for="email"
                        class="block text-xs font-bold text-slate-400 uppercase tracking-widest px-1"
                        >Alamat Email</label
                    >
                    <Input
                        id="email"
                        type="email"
                        bind:value={$form.email}
                        className="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold focus:bg-white"
                        placeholder="email@contoh.com"
                        error={$form.errors.email}
                    />
                </div>
            </div>

            <div class="pt-10 border-t border-slate-100">
                <h4
                    class="text-sm font-bold text-slate-900 uppercase tracking-widest mb-8"
                >
                    Update Password
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label
                            for="password"
                            class="block text-xs font-bold text-slate-400 uppercase tracking-widest px-1"
                            >Password Baru</label
                        >
                        <Input
                            id="password"
                            type="password"
                            bind:value={$form.password}
                            className="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold focus:bg-white"
                            placeholder="••••••••"
                            error={$form.errors.password}
                        />
                        <p
                            class="text-[10px] font-bold text-slate-400 px-1 italic"
                        >
                            * Kosongkan jika tidak ingin mengubah password
                        </p>
                    </div>

                    <div class="space-y-3">
                        <label
                            for="password_confirmation"
                            class="block text-xs font-bold text-slate-400 uppercase tracking-widest px-1"
                            >Konfirmasi Password</label
                        >
                        <Input
                            id="password_confirmation"
                            type="password"
                            bind:value={$form.password_confirmation}
                            className="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold focus:bg-white"
                            placeholder="••••••••"
                        />
                    </div>
                </div>
            </div>

            <div class="pt-10 flex justify-end">
                <Button
                    type="submit"
                    variant="primary"
                    size="xl"
                    class="w-full md:w-auto px-12 group"
                    disabled={$form.processing}
                >
                    {#if $form.processing}
                        <Loader2 size={18} class="mr-2 animate-spin" /> Menyimpan...
                    {:else}
                        <Save
                            size={18}
                            class="mr-2 group-hover:scale-110 transition-transform"
                        /> Simpan Perubahan
                    {/if}
                </Button>
            </div>
        </form>
    </Card>
</div>
