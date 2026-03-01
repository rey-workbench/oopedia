<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/shared/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import Input from "@/components/ui/Input.svelte";
    import Alert from "@/components/ui/Alert.svelte";
    import ImageUpload from "@/components/ui/ImageUpload.svelte";
    import QuillEditor from "@/components/ui/QuillEditor.svelte";
    import { MaterialFormState } from "@/states/Admin/MaterialState.svelte";
    import { ROUTES } from "@/utils/route";
    import { ArrowLeft, Info, CloudUpload, CheckCheck } from "lucide-svelte";

    const state = new MaterialFormState(null);
    const form = state.form;
</script>

<App title="Tambah Materi">
    <div class="space-y-12 pb-20">
        <PageHeader
            title="Arsitek Konten Kurikulum"
            subtitle="Publikasikan modul pembelajaran baru dengan visualisasi premium."
        >
            <div slot="actions">
                <Button
                    href={ROUTES.ADMIN.MATERIALS.INDEX}
                    variant="ghost"
                    icon={ArrowLeft}>BATALKAN PUBLIKASI</Button
                >
            </div>
        </PageHeader>

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
                        Identifikasi & Konten Modul
                    </h3>
                </div>

                <div class="space-y-10 p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        <div class="lg:col-span-2 space-y-6">
                            <Input
                                label="Judul Modul"
                                required
                                bind:value={$form.title}
                                placeholder="e.g. Fundamental of Object Oriented Programming"
                                className="text-lg font-bold tracking-widest"
                                error={$form.errors.title}
                            />

                            <Alert
                                variant="primary"
                                class="bg-primary-50/50 border-primary-100"
                            >
                                <div class="flex gap-4">
                                    <Info
                                        size={16}
                                        class="text-primary-600 mt-1"
                                    />
                                    <div
                                        class="text-[10px] font-bold text-slate-500 leading-relaxed uppercase tracking-widest"
                                    >
                                        Pastikan judul modul mendeskripsikan isi
                                        materi dengan jelas untuk memudahkan
                                        mahasiswa.
                                    </div>
                                </div>
                            </Alert>
                        </div>

                        <div class="lg:col-span-1">
                            <ImageUpload
                                preview={state.coverPreview}
                                label="Visualisasi Sampul"
                                emptyIcon={CloudUpload}
                                emptyText="Unggah Sampul"
                                error={$form.errors.cover_image}
                                onChange={(e) => state.onImageChange(e)}
                            />
                        </div>
                    </div>

                    <!-- Middle Row: WYSIWYG Editor -->
                    <div class="space-y-4">
                        <span class="block text-sm font-bold text-slate-700"
                            >Konten Instruksional
                            <span class="text-rose-500">*</span></span
                        >
                        <div id="content-editor">
                            <QuillEditor
                                bind:value={$form.content}
                                height="500px"
                            />
                        </div>
                        {#if $form.errors.content}
                            <p class="text-rose-500 text-xs mt-1">
                                {$form.errors.content}
                            </p>
                        {/if}
                    </div>

                    <div
                        class="pt-6 border-t border-slate-100 flex items-center justify-between gap-4"
                    >
                        <div class="flex items-center gap-3">
                            <CloudUpload size={14} class="text-primary-600" />
                            <span
                                class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
                                >Status Kesiapan: Siap Dipublikasikan</span
                            >
                        </div>

                        <div class="flex gap-4">
                            <Button
                                type="submit"
                                variant="primary"
                                size="lg"
                                class="shadow-xl shadow-primary-900/20"
                                icon={CheckCheck}
                                disabled={$form.processing}
                            >
                                {#if $form.processing}
                                    Memproses...
                                {:else}
                                    PUBLIKASIKAN MODUL
                                {/if}
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</App>
