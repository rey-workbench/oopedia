<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Input from '@/components/ui/Input.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Alert from '@/components/ui/Alert.svelte';
    import Panel from '@/components/ui/Panel.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import {
        ShieldCheck,
        UserCircle,
        Mail,
        User as UserIcon,
        Check,
        Lock,
        Loader2,
        Save,
    } from '@lucide/svelte';
    import { untrack } from 'svelte';
    import { AdminProfileState } from '@/states/Admin/AdminProfileState.svelte';

    const state = untrack(() => new AdminProfileState());
</script>

<App title="Profil Admin">
    <div class="space-y-12">
        <PageHeader
            id="page-header"
            title="Profil Saya"
            subtitle="Atur informasi akun dan keamanan Anda sebagai administrator sistem."
        ></PageHeader>

        <!-- Profile Hero Card -->
        <div id="profile-hero">
            <Panel rounded="full" class="mb-8 shadow-xl" padding="p-8 md:p-12">
                <div class="flex flex-col items-center gap-10 md:flex-row">
                    <div class="group relative">
                        <div
                            class="flex h-32 w-32 items-center justify-center overflow-hidden rounded-3xl bg-white shadow-xl"
                        >
                            <UserCircle size={80} class="text-slate-200" />
                        </div>
                        <div
                            class="absolute -right-2 -bottom-2 flex h-10 w-10 items-center justify-center rounded-2xl border-4 border-slate-900 bg-emerald-500 text-white shadow-xl"
                        >
                            <Check size={18} />
                        </div>
                    </div>

                    <div class="text-center md:text-left">
                        <p
                            class="text-primary-400 mb-2 text-[10px] font-bold tracking-widest uppercase"
                        >
                            MEMBER SINCE {state.user
                                ? new Date(state.user.created_at).getFullYear()
                                : 'N/A'}
                        </p>
                        <h2
                            class="mb-4 text-4xl font-bold tracking-tight text-white uppercase md:text-5xl"
                        >
                            {state.user?.name ?? 'ADMIN'}
                        </h2>
                        <div
                            class="flex flex-wrap items-center justify-center gap-4 md:justify-start"
                        >
                            <div
                                class="flex items-center gap-2 rounded-xl border-2 border-b-4 border-white/10 bg-white/10 px-4 py-2 text-xs font-bold tracking-wider uppercase shadow-sm backdrop-blur-md"
                            >
                                <Mail size={14} class="text-primary-400" />
                                {state.user?.email ?? 'admin@oopedia.com'}
                            </div>
                            <div
                                class="flex items-center gap-2 rounded-xl border-2 border-b-4 border-white/10 bg-white/10 px-4 py-2 text-xs font-bold tracking-wider uppercase shadow-sm backdrop-blur-md"
                            >
                                <UserIcon size={14} class="text-primary-400" /> Admin
                            </div>
                        </div>
                    </div>
                </div>
            </Panel>
        </div>

        <div class="grid grid-cols-1 gap-12 lg:grid-cols-3">
            <!-- Sidebar Info -->
            <div class="space-y-8">
                <h3 class="text-xl font-bold tracking-widest text-slate-900 uppercase">
                    Status Akun
                </h3>

                <Card padding="p-8" class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="bg-primary-50 text-primary-600 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                        >
                            <ShieldCheck size={20} />
                        </div>
                        <div>
                            <h4 class="text-sm font-bold tracking-wider text-slate-900 uppercase">
                                Akses Administrator
                            </h4>
                            <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                Anda memiliki hak akses untuk mengelola konten pembelajaran,
                                mahasiswa, dan aturan sistem adaptif.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 border-t border-slate-50 pt-6">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600"
                        >
                            <Lock size={20} />
                        </div>
                        <div>
                            <h4 class="text-sm font-bold tracking-wider text-slate-900 uppercase">
                                Keamanan
                            </h4>
                            <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                Update password secara berkala untuk menjaga integritas data sistem
                                e-learning.
                            </p>
                        </div>
                    </div>
                </Card>
            </div>

            <!-- Main Form -->
            <div class="space-y-8 lg:col-span-2">
                <h3 class="text-xl font-bold tracking-widest text-slate-900 uppercase">
                    Pengaturan Profil
                </h3>

                <div id="profile-settings">
                    <Card padding="p-8 md:p-12" id="profile-personal-info">
                        {#if state.flash?.success}
                            <Alert variant="success" class="mb-10"
                                >{(state.flash as any).success}</Alert
                            >
                        {/if}

                        <form
                            onsubmit={(e) => {
                                e.preventDefault();
                                state.submit();
                            }}
                            class="space-y-10"
                        >
                            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                                <div class="space-y-3">
                                    <label
                                        for="name"
                                        class="block px-1 text-xs font-bold tracking-widest text-slate-400 uppercase"
                                        >Nama Lengkap</label
                                    >
                                    <Input
                                        id="name"
                                        type="text"
                                        bind:value={state.form.name}
                                        inputClass="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold focus:bg-white"
                                        placeholder="Masukkan nama lengkap"
                                        error={state.form.errors['name']}
                                    />
                                </div>
                                <div class="space-y-3">
                                    <label
                                        for="email"
                                        class="block px-1 text-xs font-bold tracking-widest text-slate-400 uppercase"
                                        >Alamat Email</label
                                    >
                                    <Input
                                        id="email"
                                        type="email"
                                        bind:value={state.form.email}
                                        inputClass="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold focus:bg-white"
                                        placeholder="email@contoh.com"
                                        error={state.form.errors['email']}
                                    />
                                </div>
                            </div>

                            <div class="border-t border-slate-100 pt-10">
                                <h4
                                    class="mb-8 text-sm font-bold tracking-widest text-slate-900 uppercase"
                                >
                                    Update Password
                                </h4>
                                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                                    <div class="space-y-3">
                                        <label
                                            for="password"
                                            class="block px-1 text-xs font-bold tracking-widest text-slate-400 uppercase"
                                            >Password Baru</label
                                        >
                                        <Input
                                            id="password"
                                            type="password"
                                            bind:value={state.form.password}
                                            inputClass="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold focus:bg-white"
                                            placeholder="••••••••"
                                            error={state.form.errors['password']}
                                        />
                                        <p class="px-1 text-[10px] font-bold text-slate-400 italic">
                                            * Kosongkan jika tidak ingin mengubah password
                                        </p>
                                    </div>
                                    <div class="space-y-3">
                                        <label
                                            for="password_confirmation"
                                            class="block px-1 text-xs font-bold tracking-widest text-slate-400 uppercase"
                                            >Konfirmasi Password</label
                                        >
                                        <Input
                                            id="password_confirmation"
                                            type="password"
                                            bind:value={state.form.password_confirmation}
                                            inputClass="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold focus:bg-white"
                                            placeholder="••••••••"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-10">
                                <Button
                                    id="btn-save-profile"
                                    type="submit"
                                    variant="primary"
                                    size="xl"
                                    class="group w-full px-12 md:w-auto"
                                    disabled={state.form.processing}
                                >
                                    {#if state.form.processing}
                                        <Loader2 size={18} class="mr-2 animate-spin" /> Menyimpan...
                                    {:else}
                                        <Save size={18} class="mr-2" /> Simpan Perubahan
                                    {/if}
                                </Button>
                            </div>
                        </form>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</App>
