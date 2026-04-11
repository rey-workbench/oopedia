<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Input from '@/components/ui/Input.svelte';
    import Select from '@/components/ui/Select.svelte';
    import { ArrowLeft, UserPlus } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import { UserFormState } from '@/states/Admin/UserState.svelte';

    let { roles = [] } = $props();

    const state = new UserFormState(null);
    const form = state.form;

    const roleOptions = $derived(roles.map((r) => ({ value: r.id, label: r.name })));
</script>

<App title="Pembuatan Administrator">
    <div class="space-y-12">
        <div class="mb-8">
            <h1
                class="font-display text-3xl leading-tight font-extrabold tracking-tight text-slate-900 md:text-4xl"
            >
                Pembuatan Administrator
            </h1>
            <div class="mt-3 flex items-center gap-2" role="presentation">
                <div class="bg-primary-600 h-1.5 w-12 rounded-full"></div>
                <div class="h-1.5 w-4 rounded-full bg-slate-200"></div>
                <div class="h-1.5 w-2 rounded-full bg-slate-100"></div>
            </div>
            <p class="mt-4 max-w-3xl leading-relaxed font-medium text-slate-500">
                Otorisasi entitas baru ke dalam pusat kendali sistem.
            </p>
            <div class="mt-6 flex flex-wrap gap-4">
                <div>
                    <Button href={ROUTES.ADMIN.USERS.INDEX} variant="ghost" icon={ArrowLeft}
                        >KEMBALI KE DAFTAR</Button
                    >
                </div>
            </div>
        </div>

        <form
            onsubmit={(e) => {
                e.preventDefault();
                state.submit();
            }}
            class="space-y-12"
        >
            <div
                class="group relative overflow-hidden rounded-3xl border border-slate-100 bg-white p-6 shadow-2xl transition-transform duration-300 hover:-translate-y-1"
            >
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-slate-800">
                        Arsitektur Kredensial & Identitas
                    </h3>
                </div>

                <div class="space-y-10 p-6">
                    <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
                        <div class="lg:col-span-2">
                            <div class="space-y-8">
                                <div
                                    id="user-identity-section"
                                    class="grid grid-cols-1 gap-6 md:grid-cols-2"
                                >
                                    <Input
                                        id="name"
                                        label="Nama Lengkap"
                                        required
                                        bind:value={form.name}
                                        placeholder="John Doe"
                                        error={form.errors['name']}
                                    />
                                    <Input
                                        id="email"
                                        label="Alamat Email"
                                        required
                                        type="email"
                                        bind:value={form.email}
                                        placeholder="john@email.com"
                                        error={form.errors['email']}
                                    />
                                </div>

                                <div
                                    id="user-password-section"
                                    class="grid grid-cols-1 gap-6 md:grid-cols-2"
                                >
                                    <Input
                                        id="password"
                                        label="Password"
                                        required
                                        type="password"
                                        bind:value={form.password}
                                        placeholder="••••••••"
                                        error={form.errors['password']}
                                    />
                                    <Input
                                        id="password_confirmation"
                                        label="Konfirmasi Password"
                                        required
                                        type="password"
                                        bind:value={form.password_confirmation}
                                        placeholder="••••••••"
                                    />
                                </div>

                                <div id="user-role-selector" class="space-y-2">
                                    <Select
                                        bind:value={form.role_id}
                                        label="Peran Sistem"
                                        placeholder="PILIH PERAN"
                                        options={roleOptions}
                                        error={form.errors['role_id']}
                                    />
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between gap-4 border-t border-slate-100 pt-6"
                        >
                            <div class="flex items-center gap-3"></div>

                            <div class="flex gap-4">
                                <Button href={ROUTES.ADMIN.USERS.INDEX} variant="ghost">
                                    <span
                                        class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                        >BATAL</span
                                    >
                                </Button>
                                <Button
                                    id="user-save-btn"
                                    type="submit"
                                    variant="primary"
                                    size="lg"
                                    class="shadow-primary-900/20 shadow-xl"
                                    icon={UserPlus}
                                    disabled={form.processing}
                                >
                                    {#if form.processing}
                                        Memproses...
                                    {:else}
                                        OTORISASI ENTITAS
                                    {/if}
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</App>
