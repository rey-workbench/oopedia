<script>
    import App from "@/layouts/App.svelte";
    import Button from "@/components/ui/Button.svelte";
    import DataForm from "@/components/ui/DataForm.svelte";
    import Input from "@/components/ui/Input.svelte";
    import InfoPanel from "@/components/shared/InfoPanel.svelte";
    import { ArrowLeft, UserPlus, Shield, ChevronDown } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";
    import { UserFormState } from "@/states/Admin/UserState.svelte";

    export let roles = [];

    const state = new UserFormState(null);
    const form = state.form;

    const securityItems = [
        "Password disimpan secara terenkripsi (bcrypt)",
        "Email harus unik dalam sistem",
        "Akun admin memerlukan approval dari Super Admin",
        "Semua aktivitas login dicatat dalam audit log",
    ];
</script>

<App title="Pembuatan Administrator">
    <div class="space-y-12">
        <div class="mb-8">
            <h1
                class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 leading-tight font-display"
            >
                Pembuatan Administrator
            </h1>
            <div class="flex items-center gap-2 mt-3" role="presentation">
                <div class="h-1.5 w-12 bg-primary-600 rounded-full"></div>
                <div class="h-1.5 w-4 bg-slate-200 rounded-full"></div>
                <div class="h-1.5 w-2 bg-slate-100 rounded-full"></div>
            </div>
            <p
                class="mt-4 text-slate-500 font-medium leading-relaxed max-w-3xl"
            >
                Otorisasi entitas baru ke dalam pusat kendali sistem.
            </p>
            <div class="mt-6 flex flex-wrap gap-4">
                <div>
                    <Button
                        href={ROUTES.ADMIN.USERS.INDEX}
                        variant="ghost"
                        icon={ArrowLeft}>KEMBALI KE DAFTAR</Button
                    >
                </div>
            </div>
        </div>

        <DataForm
            title="Arsitektur Kredensial & Identitas"
            onSubmit={() => state.submit()}
            isEdit={false}
            processing={$form.processing}
            submitLabel="OTORISASI ENTITAS"
            submitIcon={UserPlus}
            cancelHref={ROUTES.ADMIN.USERS.INDEX}
        >
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <div class="lg:col-span-2">
                    <div class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label
                                    for="name"
                                    class="text-[10px] font-bold uppercase text-slate-400 tracking-widest"
                                    >Nama Lengkap</label
                                >
                                <Input
                                    id="name"
                                    bind:value={$form.name}
                                    placeholder="John Doe"
                                    error={$form.errors.name}
                                />
                            </div>
                            <div class="space-y-2">
                                <label
                                    for="email"
                                    class="text-[10px] font-bold uppercase text-slate-400 tracking-widest"
                                    >Alamat Email</label
                                >
                                <Input
                                    id="email"
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
                                    for="password"
                                    class="text-[10px] font-bold uppercase text-slate-400 tracking-widest"
                                    >Password</label
                                >
                                <Input
                                    id="password"
                                    type="password"
                                    bind:value={$form.password}
                                    placeholder="••••••••"
                                    error={$form.errors.password}
                                />
                            </div>
                            <div class="space-y-2">
                                <label
                                    for="password_confirmation"
                                    class="text-[10px] font-bold uppercase text-slate-400 tracking-widest"
                                    >Konfirmasi Password</label
                                >
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    bind:value={$form.password_confirmation}
                                    placeholder="••••••••"
                                />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label
                                for="role_id"
                                class="text-[10px] font-bold uppercase text-slate-400 tracking-widest"
                                >Peran Sistem</label
                            >
                            <div class="relative">
                                <select
                                    id="role_id"
                                    bind:value={$form.role_id}
                                    class="w-full px-4 py-3 border-2 border-slate-100 rounded-2xl bg-white text-sm font-bold focus:ring-4 focus:ring-primary-50 focus:border-primary-500 outline-none transition-all appearance-none"
                                >
                                    <option value="">Pilih Peran</option>
                                    {#each roles as role}
                                        <option value={role.id}
                                            >{role.name}</option
                                        >
                                    {/each}
                                </select>
                                <ChevronDown
                                    size={16}
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                                />
                            </div>
                            {#if $form.errors.role_id}
                                <p
                                    class="text-[10px] font-bold text-rose-500 uppercase tracking-widest"
                                >
                                    {$form.errors.role_id}
                                </p>
                            {/if}
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-1">
                    <InfoPanel
                        icon={Shield}
                        title="Protokol Keamanan"
                        items={securityItems}
                    />
                </div>
            </div>
        </DataForm>
    </div>
</App>
