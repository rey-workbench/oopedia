<script>
    import App from "../../../layouts/App.svelte";
    import Button from "../../../components/ui/Button.svelte";
    import Input from "../../../components/ui/Input.svelte";
    import { useForm, page } from "@inertiajs/svelte";
    import { onMount } from "svelte";
    import { UserCircle, Check, Lock, Loader2, Save } from "lucide-svelte";

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

<App title="Profil Mahasiswa">
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Profile Header -->
            <div class="relative mb-12">
                <div
                    class="h-64 w-full bg-gradient-to-br from-indigo-600 via-blue-700 to-blue-800 rounded-[2.5rem] shadow-xl overflow-hidden relative"
                >
                    <div class="absolute inset-0 bg-slate-900/20"></div>
                    <!-- Background could be an image, using css gradient for now matching blade -->
                </div>

                <div class="absolute -bottom-6 left-8 right-8">
                    <div
                        class="bg-white/80 backdrop-blur-xl rounded-[2rem] p-6 shadow-2xl border border-white/50 flex flex-col md:flex-row items-center gap-6"
                    >
                        <div class="relative">
                            <div
                                class="w-24 h-24 rounded-2xl overflow-hidden border-4 border-white shadow-lg shrink-0 flex items-center justify-center bg-indigo-100"
                            >
                                <!-- Fallback avatar or image -->
                                <UserCircle size={64} class="text-indigo-300" />
                            </div>
                            <div
                                class="absolute -bottom-2 -right-2 w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center text-white border-2 border-white shadow-lg"
                            >
                                <Check size={14} class="text-white" />
                            </div>
                        </div>
                        <div class="text-center md:text-left flex-1">
                            <h3
                                class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-1"
                            >
                                Akun Terverifikasi
                            </h3>
                            <h2
                                class="text-3xl font-bold text-slate-900 tracking-widest mb-1"
                            >
                                {user.name}
                            </h2>
                            <p
                                class="text-sm font-bold text-slate-500 uppercase"
                            >
                                Mahasiswa Terdaftar
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div
                class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 md:p-12 mb-10"
            >
                {#if flash && flash.success}
                    <div
                        class="mb-8 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4 animate-in fade-in slide-in-from-top-4 duration-500"
                    >
                        <div
                            class="w-10 h-10 bg-emerald-500 text-white rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-emerald-100"
                        >
                            <Check size={18} class="text-white" />
                        </div>
                        <p class="text-emerald-800 font-bold">
                            {flash.success}
                        </p>
                    </div>
                {/if}

                <form on:submit|preventDefault={handleSubmit} class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label
                                for="name"
                                class="block text-sm font-bold text-slate-700"
                                >Nama Lengkap <span class="text-rose-500"
                                    >*</span
                                ></label
                            >
                            <Input
                                id="name"
                                type="text"
                                bind:value={$form.name}
                                className="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold"
                                placeholder="Nama Lengkap"
                                error={$form.errors.name}
                            />
                        </div>

                        <div class="space-y-2">
                            <label
                                for="email"
                                class="block text-sm font-bold text-slate-700"
                                >Alamat Email <span class="text-rose-500"
                                    >*</span
                                ></label
                            >
                            <Input
                                id="email"
                                type="email"
                                bind:value={$form.email}
                                className="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold"
                                placeholder="Email Address"
                                error={$form.errors.email}
                            />
                        </div>
                    </div>

                    <div class="pt-8 border-t border-slate-100">
                        <div class="flex items-center gap-3 mb-8">
                            <div
                                class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 shadow-inner"
                            >
                                <Lock size={18} class="text-amber-600" />
                            </div>
                            <h4
                                class="text-lg font-bold text-slate-900 tracking-widest uppercase"
                            >
                                Keamanan Akun
                            </h4>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label
                                    for="password"
                                    class="block text-sm font-bold text-slate-700"
                                    >Password Baru</label
                                >
                                <Input
                                    id="password"
                                    type="password"
                                    bind:value={$form.password}
                                    className="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold"
                                    placeholder="••••••••"
                                    error={$form.errors.password}
                                />
                                <span
                                    class="text-[10px] font-bold text-slate-400"
                                    >Kosongkan jika tidak ingin mengubah
                                    password</span
                                >
                            </div>

                            <div class="space-y-2">
                                <label
                                    for="password_confirmation"
                                    class="block text-sm font-bold text-slate-700"
                                    >Konfirmasi Password</label
                                >
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    bind:value={$form.password_confirmation}
                                    className="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold"
                                    placeholder="••••••••"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 flex justify-end">
                        <Button
                            type="submit"
                            class="w-full md:w-auto px-12 py-4 bg-slate-900 text-white rounded-2xl font-bold uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-slate-200 hover:shadow-blue-200"
                            disabled={$form.processing}
                        >
                            {#if $form.processing}
                                <Loader2 size={18} class="mr-2 animate-spin" /> Menyimpan...
                            {:else}
                                <Save size={18} class="mr-2" /> Simpan Perubahan
                            {/if}
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</App>
