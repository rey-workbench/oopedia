<script lang="ts">
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Input from '@/components/ui/Input.svelte';
    import InfoPanel from '@/components/ui/InfoPanel.svelte';
    import { ArrowLeft, Save, Lock } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import { untrack } from 'svelte';
    import { UserFormState } from '@/states/Admin/UserState.svelte';

    let { user } = $props();

    const state = untrack(() => new UserFormState(user));
    const form = state.form;

    const safeUpdateItems = [
        'Kosongkan kolom password jika tidak ingin mengubahnya',
        'Password lama tidak diperlukan untuk reset',
        'Perubahan email mungkin memerlukan verifikasi ulang',
        'Semua perubahan dicatat dalam log sistem',
    ];
</script>

<App title="Edit Administrator">
    <div class="space-y-12">
        <PageHeader
            title="Pembaruan Kredensial"
            subtitle="Modifikasi data identitas dan kunci keamanan entitas."
        >
            {#snippet actions()}
                <Button href={ROUTES.ADMIN.USERS.INDEX} variant="ghost" icon={ArrowLeft}
                    >KEMBALI KE DAFTAR</Button
                >
            {/snippet}
        </PageHeader>

        <form
            onsubmit={(e) => {
                e.preventDefault();
                state.submit();
            }}
            class="space-y-12"
        >
            <Card padding="p-6">
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Modifikasi Identitas Admin</h3>
                </div>

                <div class="space-y-10 p-6">
                    <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
                        <div class="lg:col-span-2">
                            <div class="space-y-8">
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <Input
                                        label="Nama Lengkap"
                                        bind:value={form.name}
                                        placeholder="John Doe"
                                        error={form.errors['name']}
                                    />
                                    <Input
                                        label="Alamat Email"
                                        type="email"
                                        bind:value={form.email}
                                        placeholder="john@email.com"
                                        error={form.errors['email']}
                                    />
                                </div>

                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <Input
                                        label="Password Baru (Opsional)"
                                        type="password"
                                        bind:value={form.password}
                                        placeholder="••••••••"
                                        error={form.errors['password']}
                                    />
                                    <Input
                                        label="Konfirmasi (Opsional)"
                                        type="password"
                                        bind:value={form.password_confirmation}
                                        placeholder="••••••••"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="lg:col-span-1">
                            <InfoPanel icon={Lock} title="Pembaruan Aman" items={safeUpdateItems} />
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
                                type="submit"
                                variant="primary"
                                size="lg"
                                class="shadow-primary-900/20 shadow-xl"
                                icon={Save}
                                disabled={form.processing}
                            >
                                {#if form.processing}
                                    Memproses...
                                {:else}
                                    SIMPAN PERUBAHAN
                                {/if}
                            </Button>
                        </div>
                    </div>
                </div>
            </Card>
        </form>
    </div>
</App>
