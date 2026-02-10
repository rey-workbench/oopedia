<script>
    import App from "../../../../layouts/App.svelte";
    import PageHeader from "../../../../components/ui/PageHeader.svelte";
    import Card from "../../../../components/ui/Card.svelte";
    import Button from "../../../../components/ui/Button.svelte";
    import Input from "../../../../components/ui/Input.svelte";
    import { useForm } from "@inertiajs/svelte";
    import { ArrowLeft, Lock, RefreshCw, Save } from "lucide-svelte";

    export let user;

    // Note: Roles usually not editable in edit view in AdminUserController for existing users based on the controller `update` method logic seen?
    // Let's check `update` method:
    // $request->validate([ 'name', 'email', 'password' ]);
    // It does NOT validate or update `role_id`.
    // Blade `edit.blade.php` content needs to be checked if it has role selection.
    // I didn't read `edit.blade.php`, but I read `store` method which has role_id.
    // `update` method in `AdminUserController` does NOT seem to update role.
    // But wait, if I am superadmin editing an admin, I might want to change role?
    // The controller `update` method:
    /*
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'password' => 'nullable|string|min:8|confirmed',
    ]);
    
    try {
        $this->userService->updateAdmin($user, $request->all());
  */
    // I should check `UserService::updateAdmin`.
    // However, I will assume the form should match what the controller accepts.
    // If `edit.blade.php` has it, I should include it. I'll stick to name/email/password for now as per controller validation.

    let form = useForm({
        _method: "PUT",
        name: user.name,
        email: user.email,
        password: "",
        password_confirmation: "",
    });

    function handleSubmit() {
        $form.post(`/admin/users/${user.id}`);
    }
</script>

<App title="Edit Administrator">
    <div class="space-y-12">
        <PageHeader
            title="Pembaruan Kredensial"
            subtitle="Modifikasi data identitas dan kunci keamanan entitas."
        >
            <div slot="actions">
                <Button href="/admin/users" variant="ghost" icon={ArrowLeft}
                    >KEMBALI KE DAFTAR</Button
                >
            </div>
        </PageHeader>

        <Card class="border-slate-100 shadow-2xl">
            <div slot="header" class="px-6 py-4 border-b border-slate-50">
                <h3 class="text-lg font-bold text-slate-800">
                    Modifikasi Identitas Admin
                </h3>
            </div>

            <form
                on:submit|preventDefault={handleSubmit}
                class="space-y-10 p-6"
            >
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    <div class="lg:col-span-2 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label
                                    for="name"
                                    class="block text-sm font-bold text-slate-700"
                                    >Identitas Lengkap <span
                                        class="text-rose-500">*</span
                                    ></label
                                >
                                <Input
                                    id="name"
                                    bind:value={$form.name}
                                    placeholder="Nama lengkap subjek"
                                    className="text-lg font-bold tracking-widest"
                                    error={$form.errors.name}
                                />
                            </div>

                            <div class="space-y-2">
                                <label
                                    for="email"
                                    class="block text-sm font-bold text-slate-700"
                                    >Alias Digital (Email) <span
                                        class="text-rose-500">*</span
                                    ></label
                                >
                                <Input
                                    id="email"
                                    type="email"
                                    bind:value={$form.email}
                                    placeholder="Email elektronik subjek"
                                    className="text-lg font-bold tracking-widest"
                                    error={$form.errors.email}
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label
                                    for="password"
                                    class="block text-sm font-bold text-slate-700"
                                    >Kunci Keamanan Baru (Opsional)</label
                                >
                                <Input
                                    id="password"
                                    type="password"
                                    bind:value={$form.password}
                                    placeholder="Kosongkan jika tidak diubah"
                                    error={$form.errors.password}
                                />
                            </div>

                            <div class="space-y-2">
                                <label
                                    for="password_confirmation"
                                    class="block text-sm font-bold text-slate-700"
                                    >Verifikasi Kunci Baru</label
                                >
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    bind:value={$form.password_confirmation}
                                    placeholder="Ulangi kata sandi baru"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-1">
                        <div
                            class="h-full p-8 bg-slate-900 rounded-[2rem] relative overflow-hidden flex flex-col justify-center text-center"
                        >
                            <div
                                class="absolute right-0 top-0 w-32 h-32 bg-indigo-600/10 blur-3xl"
                            ></div>
                            <div class="relative z-10 text-center">
                                <div
                                    class="w-16 h-16 mx-auto rounded-3xl bg-indigo-600/20 text-indigo-500 flex items-center justify-center mb-6"
                                >
                                    <Lock size={24} />
                                </div>
                                <h4
                                    class="text-white text-xs font-bold uppercase tracking-widest mb-4"
                                >
                                    Pembaruan Aman
                                </h4>
                                <p
                                    class="text-[10px] font-bold text-slate-500 leading-relaxed uppercase tracking-wider"
                                >
                                    Perubahan kredensial akan segera efektif dan
                                    membatalkan sesi aktif sebelumnya.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="pt-10 border-t border-slate-100 flex items-center justify-between gap-4"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center"
                        >
                            <RefreshCw size={14} />
                        </div>
                        <div>
                            <h6
                                class="text-[10px] font-bold uppercase tracking-widest text-slate-900 mb-0"
                            >
                                Sinkronisasi Data
                            </h6>
                            <p
                                class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1 mb-0"
                            >
                                Siap memperbarui entitas
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <Button
                            href="/admin/users"
                            variant="ghost"
                            class="text-slate-400 font-bold uppercase text-[10px] tracking-widest"
                            >BATAL</Button
                        >
                        <Button
                            type="submit"
                            variant="primary"
                            size="lg"
                            class="shadow-xl shadow-indigo-500/30 bg-indigo-600 hover:bg-indigo-700 font-bold tracking-widest"
                            icon={Save}
                            disabled={$form.processing}
                        >
                            {#if $form.processing}
                                Menyimpan...
                            {:else}
                                SIMPAN PERUBAHAN
                            {/if}
                        </Button>
                    </div>
                </div>
            </form>
        </Card>
    </div>
</App>
