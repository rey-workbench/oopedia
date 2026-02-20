<script>
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import Input from "@/components/ui/Input.svelte";
    import {
        ChevronDown,
        Shield,
        Cpu,
        UserPlus,
        Lock,
        RefreshCw,
        Save,
    } from "lucide-svelte";
    import { UserFormState } from "@/states/Admin/UserState.svelte";

    // For create mode
    export let roles = [];

    // For edit mode
    export let user = null;

    const state = new UserFormState(user);
    const form = state.form;
</script>

<Card class="border-slate-100 shadow-2xl">
    <div slot="header">
        <h3 class="text-lg font-bold text-slate-800">
            {state.isEdit
                ? "Modifikasi Identitas Admin"
                : "Arsitektur Kredensial & Identitas"}
        </h3>
    </div>

    <form
        on:submit|preventDefault={() => state.submit()}
        class="space-y-10 p-6"
    >
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label
                            for="name"
                            class="block text-sm font-bold text-slate-700"
                            >Identitas Lengkap <span class="text-rose-500"
                                >*</span
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
                            >Alias Digital (Email) <span class="text-rose-500"
                                >*</span
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
                            >{state.isEdit
                                ? "Kunci Keamanan Baru (Opsional)"
                                : "Kunci Keamanan"}
                            {#if !state.isEdit}<span class="text-rose-500"
                                    >*</span
                                >{/if}</label
                        >
                        <Input
                            id="password"
                            type="password"
                            bind:value={$form.password}
                            placeholder={state.isEdit
                                ? "Kosongkan jika tidak diubah"
                                : "Inisialisasi kata sandi"}
                            error={$form.errors.password}
                        />
                    </div>

                    <div class="space-y-2">
                        <label
                            for="password_confirmation"
                            class="block text-sm font-bold text-slate-700"
                            >{state.isEdit
                                ? "Verifikasi Kunci Baru"
                                : "Verifikasi Kunci Keamanan"}
                            {#if !state.isEdit}<span class="text-rose-500"
                                    >*</span
                                >{/if}</label
                        >
                        <Input
                            id="password_confirmation"
                            type="password"
                            bind:value={$form.password_confirmation}
                            placeholder={state.isEdit
                                ? "Ulangi kata sandi baru"
                                : "Inisialisasi ulang kata sandi"}
                        />
                    </div>
                </div>

                {#if !state.isEdit}
                    <div class="space-y-2">
                        <label
                            for="role_id"
                            class="block text-sm font-bold text-slate-700"
                            >Otorisasi Peran Sistem <span class="text-rose-500"
                                >*</span
                            ></label
                        >
                        <div class="relative">
                            <select
                                id="role_id"
                                bind:value={$form.role_id}
                                class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold tracking-widest outline-none focus:ring-4 focus:ring-primary-100 transition-all appearance-none cursor-pointer uppercase"
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
                {/if}
            </div>

            <div class="lg:col-span-1">
                <div
                    class="h-full p-8 bg-slate-900 rounded-[2rem] relative overflow-hidden flex flex-col justify-center text-center"
                >
                    <div
                        class="absolute right-0 top-0 w-32 h-32 bg-primary-600/10 blur-3xl"
                    ></div>
                    <div class="relative z-10 text-center">
                        <div
                            class="w-16 h-16 mx-auto rounded-3xl bg-primary-600/20 text-primary-600 flex items-center justify-center mb-6"
                        >
                            {#if state.isEdit}
                                <Lock size={24} />
                            {:else}
                                <Shield size={24} />
                            {/if}
                        </div>
                        <h4
                            class="text-white text-xs font-bold uppercase tracking-widest mb-4"
                        >
                            {state.isEdit
                                ? "Pembaruan Aman"
                                : "Protokol Keamanan"}
                        </h4>
                        <p
                            class="text-[10px] font-bold text-slate-500 leading-relaxed uppercase tracking-wider"
                        >
                            {state.isEdit
                                ? "Perubahan kredensial akan segera efektif dan membatalkan sesi aktif sebelumnya."
                                : "Pastikan identitas dan level otorisasi sesuai dengan kebijakan keamanan data OOPEDIA."}
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
                    class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center"
                >
                    {#if state.isEdit}
                        <RefreshCw size={14} />
                    {:else}
                        <Cpu size={14} />
                    {/if}
                </div>
                <div>
                    <h6
                        class="text-[10px] font-bold uppercase tracking-widest text-slate-900 mb-0"
                    >
                        {state.isEdit ? "Sinkronisasi Data" : "Otorisasi Utama"}
                    </h6>
                    <p
                        class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1 mb-0"
                    >
                        {state.isEdit
                            ? "Siap memperbarui entitas"
                            : "Siap mengotorisasi entitas baru"}
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
                    class="shadow-xl shadow-primary-900/30 font-bold tracking-widest"
                    icon={state.isEdit ? Save : UserPlus}
                    disabled={$form.processing}
                >
                    {#if $form.processing}
                        {state.isEdit ? "Menyimpan..." : "Menambahkan..."}
                    {:else}
                        {state.isEdit
                            ? "SIMPAN PERUBAHAN"
                            : "OTORISASI ENTITAS"}
                    {/if}
                </Button>
            </div>
        </div>
    </form>
</Card>
