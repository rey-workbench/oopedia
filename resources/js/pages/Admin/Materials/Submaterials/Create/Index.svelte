<script lang="ts">
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/shared/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import Input from "@/components/ui/Input.svelte";
    import QuillEditor from "@/components/ui/QuillEditor.svelte";
    import { ArrowLeft, Save } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";
    import { untrack } from 'svelte';
    import { SubmaterialFormState } from "@/states/Admin/MaterialState.svelte";

    let { material } = $props();

    const state = untrack(() => new SubmaterialFormState(material, null));
</script>

<App title={`Buat Sub-Materi untuk ${material.title}`}>
    <div class="space-y-12">
        <PageHeader
            title="Tambah Sub-Materi"
            subtitle={`Unit pembelajaran untuk modul utama: ${material.title}`}
        >
            {#snippet actions()}
                <Button
                    href={ROUTES.ADMIN.MATERIALS.SUBMATERIALS.INDEX(
                        material.id,
                    )}
                    variant="ghost"
                    icon={ArrowLeft}>KEMBALI KE HIERARKI</Button
                >
            {/snippet}
        </PageHeader>

        <div class="max-w-4xl mx-auto">
            <form
                onsubmit={(e) => {
                    e.preventDefault();
                    state.submit();
                }}
                class="space-y-12"
            >
                <div
                    class="bg-white rounded-3xl p-6 shadow-2xl border border-slate-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300"
                >
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-slate-800">
                            Konfigurasi Konten Unit
                        </h3>
                    </div>

                    <div class="space-y-10 p-6">
                        <!-- SubMaterial Form Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-2 space-y-2">
                                <label
                                    for="title"
                                    class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                                >
                                    Judul Sub-Materi
                                </label>
                                <Input
                                    id="title"
                                    bind:value={state.form.title}
                                    placeholder="Contoh: Pengenalan Class & Object"
                                    required
                                    error={state.form.errors['title']}
                                />
                            </div>

                            <div class="space-y-2">
                                <label
                                    for="order"
                                    class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                                >
                                    Urutan Tampil
                                </label>
                                <Input
                                    id="order"
                                    type="number"
                                    bind:value={state.form.order}
                                    required
                                    error={state.form.errors['order']}
                                />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span
                                class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                                >Jenis Konten Utama</span
                            >
                            <div class="grid grid-cols-3 gap-4">
                                {#each ["teori", "sintaks", "mixed"] as type}
                                    <button
                                        type="button"
                                        onclick={() =>
                                            state.setJenisKonten(type)}
                                        class={`py-3 px-4 rounded-2xl border-2 font-bold uppercase tracking-widest text-[10px] transition-all
                                    ${state.form.jenis_konten === type ? "border-primary-600 bg-primary-50 text-primary-600" : "border-slate-100 bg-slate-50 text-slate-400"}`}
                                    >
                                        {type}
                                    </button>
                                {/each}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span
                                class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                            >
                                Materi Pembelajaran (Rich Text)
                            </span>
                            <QuillEditor
                                bind:value={state.form.content}
                                placeholder="Tuliskan materi pembelajaran secara detail di sini..."
                            />
                            {#if state.form.errors['content']}
                                <p
                                    class="text-[10px] font-bold text-rose-500 uppercase tracking-widest"
                                >
                                    {state.form.errors['content']}
                                </p>
                            {/if}
                        </div>

                        <div
                            class="pt-6 border-t border-slate-100 flex items-center justify-between gap-4"
                        >
                            <div class="flex items-center gap-3"></div>

                            <div class="flex gap-4">
                                <Button
                                    href={ROUTES.ADMIN.MATERIALS.SUBMATERIALS.INDEX(
                                        material.id,
                                    )}
                                    variant="ghost"
                                >
                                    <span
                                        class="text-[10px] font-bold uppercase text-slate-400 tracking-widest"
                                        >BATAL</span
                                    >
                                </Button>
                                <Button
                                    type="submit"
                                    variant="primary"
                                    size="lg"
                                    class="shadow-xl shadow-primary-900/20"
                                    icon={Save}
                                    disabled={state.form.processing}
                                >
                                    {#if state.form.processing}
                                        Memproses...
                                    {:else}
                                        PUBLIKASIKAN SUB-MATERI
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
