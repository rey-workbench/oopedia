<script>
    import App from "../../../layouts/App.svelte";
    import PageHeader from "../../../components/ui/PageHeader.svelte";
    import Card from "../../../components/ui/Card.svelte";
    import Button from "../../../components/ui/Button.svelte";
    import Input from "../../../components/ui/Input.svelte";
    import { useForm } from "@inertiajs/svelte";
    import {
        ArrowLeft,
        ChevronDown,
        Shield,
        Cpu,
        UserPlus,
    } from "lucide-svelte";

    // We should pass roles from controller if we want to be dynamic,
    // or hardcode based on Blade: @foreach($roles as $role)
    // Assuming controller passes 'roles' prop.
    export let roles = [];

    let form = useForm({
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        role_id: "",
    });

    function handleSubmit() {
        $form.post("/admin/users");
    }
</script>

<App title="Pembuatan Administrator">
    <div class="space-y-12">
        <PageHeader
            title="Pembuatan Administrator"
            subtitle="Otorisasi entitas baru ke dalam pusat kendali sistem."
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
                    Arsitektur Kredensial & Identitas
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
                                    >Kunci Keamanan <span class="text-rose-500"
                                        >*</span
                                    ></label
                                >
                                <Input
                                    id="password"
                                    type="password"
                                    bind:value={$form.password}
                                    placeholder="Inisialisasi kata sandi"
                                    error={$form.errors.password}
                                />
                            </div>

                            <div class="space-y-2">
                                <label
                                    for="password_confirmation"
                                    class="block text-sm font-bold text-slate-700"
                                    >Verifikasi Kunci Keamanan <span
                                        class="text-rose-500">*</span
                                    ></label
                                >
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    bind:value={$form.password_confirmation}
                                    placeholder="Inisialisasi ulang kata sandi"
                                />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label
                                for="role_id"
                                class="block text-sm font-bold text-slate-700"
                                >Otorisasi Peran Sistem <span
                                    class="text-rose-500">*</span
                                ></label
                            >
                            <div class="relative">
                                <select
                                    id="role_id"
                                    bind:value={$form.role_id}
                                    class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold tracking-widest outline-none focus:ring-4 focus:ring-blue-100 transition-all appearance-none cursor-pointer uppercase"
                                >
                                    <option value="" disabled selected
                                        >Pilih Peran</option
                                    >
                                    {#each roles as role}
                                        <option value={role.id}
                                            >{role.role_name.toUpperCase()}</option
                                        >
                                    {/each}
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-500"
                                >
                                    <ChevronDown size={14} />
                                </div>
                            </div>
                            {#if $form.errors.role_id}
                                <p class="text-rose-500 text-xs mt-1">
                                    {$form.errors.role_id}
                                </p>
                            {/if}
                        </div>
                    </div>

                    <div class="lg:col-span-1">
                        <div
                            class="h-full p-8 bg-slate-900 rounded-[2rem] relative overflow-hidden flex flex-col justify-center text-center"
                        >
                            <div
                                class="absolute right-0 top-0 w-32 h-32 bg-blue-600/10 blur-3xl"
                            ></div>
                            <div class="relative z-10 text-center">
                                <div
                                    class="w-16 h-16 mx-auto rounded-3xl bg-blue-600/20 text-blue-500 flex items-center justify-center mb-6"
                                >
                                    <Shield size={24} />
                                </div>
                                <h4
                                    class="text-white text-xs font-bold uppercase tracking-widest mb-4"
                                >
                                    Protokol Keamanan
                                </h4>
                                <p
                                    class="text-[10px] font-bold text-slate-500 leading-relaxed uppercase tracking-wider"
                                >
                                    Pastikan identitas dan level otorisasi
                                    sesuai dengan kebijakan keamanan data
                                    OOPEDIA.
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
                            class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center"
                        >
                            <Cpu size={14} />
                        </div>
                        <div>
                            <h6
                                class="text-[10px] font-bold uppercase tracking-widest text-slate-900 mb-0"
                            >
                                Otorisasi Utama
                            </h6>
                            <p
                                class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1 mb-0"
                            >
                                Siap mengotorisasi entitas baru
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
                            class="shadow-xl shadow-blue-500/30 font-bold tracking-widest"
                            icon={UserPlus}
                            disabled={$form.processing}
                        >
                            {#if $form.processing}
                                Menambahkan...
                            {:else}
                                OTORISASI ENTITAS
                            {/if}
                        </Button>
                    </div>
                </div>
            </form>
        </Card>
    </div>
</App>
