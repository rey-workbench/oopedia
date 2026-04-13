<script lang="ts">
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Input from '@/components/ui/Input.svelte';
    import QuillEditor from '@/components/ui/QuillEditor.svelte';
    import { ArrowLeft, RefreshCw } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import { untrack } from 'svelte';
    import { SubmaterialFormState } from '@/states/Admin/MaterialState.svelte';

    let { material, submaterial } = $props();

    const state = untrack(() => new SubmaterialFormState(material, submaterial));
</script>

<App title={`Edit Sub-Materi: ${submaterial.title}`}>
    <div class="space-y-12">
        <PageHeader
            id="page-header"
            title="Pembaruan Unit"
            subtitle={`Modifikasi konten pembelajaran untuk unit: ${submaterial.title}`}
        >
            {#snippet actions()}
                <Button
                    href={ROUTES.ADMIN.MATERIALS.SUBMATERIALS.INDEX(material.id)}
                    variant="ghost"
                    icon={ArrowLeft}>KEMBALI KE HIERARKI</Button
                >
            {/snippet}
        </PageHeader>

        <div class="mx-auto max-w-4xl">
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
                        <h3 class="text-lg font-bold text-slate-800">Update Konten Unit</h3>
                    </div>

                    <div class="space-y-10 p-6">
                        <!-- SubMaterial Form Fields -->
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div class="space-y-2 md:col-span-2">
                                <label
                                    for="title"
                                    class="font-poppins text-[10px] font-bold text-slate-400 uppercase"
                                >
                                    Judul Sub-Materi
                                </label>
                                <Input
                                    id="submaterial-title-input"
                                    bind:value={state.form.title}
                                    placeholder="Contoh: Pengenalan Class & Object"
                                    required
                                    error={state.form.errors['title']}
                                />
                            </div>

                            <div class="space-y-2">
                                <label
                                    for="order"
                                    class="font-poppins text-[10px] font-bold text-slate-400 uppercase"
                                >
                                    Urutan Tampil
                                </label>
                                <Input
                                    id="submaterial-order-input"
                                    type="number"
                                    bind:value={state.form.order}
                                    required
                                    error={state.form.errors['order']}
                                />
                            </div>
                        </div>

                        <div id="submaterial-type-selector" class="space-y-2">
                            <span
                                class="font-poppins text-[10px] font-bold text-slate-400 uppercase"
                                >Jenis Konten Utama</span
                            >
                            <div class="grid grid-cols-3 gap-4">
                                {#each ['teori', 'sintaks', 'mixed'] as type}
                                    <button
                                        type="button"
                                        onclick={() => state.setJenisKonten(type)}
                                        class={`rounded-2xl border-2 px-4 py-3 text-[10px] font-bold tracking-widest uppercase transition-all
                                    ${state.form.jenis_konten === type ? 'border-primary-600 bg-primary-50 text-primary-600' : 'border-slate-100 bg-slate-50 text-slate-400'}`}
                                    >
                                        {type}
                                    </button>
                                {/each}
                            </div>
                        </div>

                        <div id="submaterial-content-editor" class="space-y-2">
                            <span
                                class="font-poppins text-[10px] font-bold text-slate-400 uppercase"
                            >
                                Materi Pembelajaran (Rich Text)
                            </span>
                            <QuillEditor
                                bind:value={state.form.content}
                                placeholder="Tuliskan materi pembelajaran secara detail di sini..."
                            />
                            {#if state.form.errors['content']}
                                <p
                                    class="text-[10px] font-bold tracking-widest text-rose-500 uppercase"
                                >
                                    {state.form.errors['content']}
                                </p>
                            {/if}
                        </div>

                        <div
                            class="flex items-center justify-between gap-4 border-t border-slate-100 pt-6"
                        >
                            <div class="flex items-center gap-3"></div>

                            <div class="flex gap-4">
                                <Button
                                    href={ROUTES.ADMIN.MATERIALS.SUBMATERIALS.INDEX(material.id)}
                                    variant="ghost"
                                >
                                    <span
                                        class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                        >BATAL</span
                                    >
                                </Button>
                                <Button
                                    id="submaterial-save-btn"
                                    type="submit"
                                    variant="primary"
                                    size="lg"
                                    class="shadow-primary-900/20 shadow-xl"
                                    icon={RefreshCw}
                                    disabled={state.form.processing}
                                >
                                    {#if state.form.processing}
                                        Memproses...
                                    {:else}
                                        SIMPAN PERUBAHAN UNIT
                                    {/if}
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</App>
