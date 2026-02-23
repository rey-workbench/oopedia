<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import Button from "@/components/ui/Button.svelte";
    import DataForm from "@/components/ui/DataForm.svelte";
    import Input from "@/components/ui/Input.svelte";
    import Alert from "@/components/ui/Alert.svelte";
    import ImageUpload from "@/components/ui/ImageUpload.svelte";
    import { MaterialFormState } from "@/states/Admin/MaterialState.svelte";

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

        <DataForm
            title="Identifikasi & Konten Modul"
            onSubmit={() => state.submit()}
            isEdit={state.isEdit}
            processing={$form.processing}
            submitLabel="PUBLIKASIKAN MODUL"
            submitIcon={CheckCheck}
        >
            <svelte:fragment slot="footer-left">
                <CloudUpload size={14} class="text-primary-600" />
                <span
                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
                    >Status Kesiapan: Siap Dipublikasikan</span
                >
            </svelte:fragment>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <div class="lg:col-span-2 space-y-6">
                    <div class="space-y-2">
                        <label
                            for="title"
                            class="block text-sm font-bold text-slate-700"
                            >Judul Modul
                            <span class="text-rose-500">*</span></label
                        >
                        <Input
                            id="title"
                            bind:value={$form.title}
                            placeholder="e.g. Fundamental of Object Oriented Programming"
                            className="text-lg font-bold tracking-widest"
                            error={$form.errors.title}
                        />
                    </div>

                    <Alert
                        variant="primary"
                        class="bg-primary-50/50 border-primary-100"
                    >
                        <div class="flex gap-4">
                            <Info size={16} class="text-primary-600 mt-1" />
                            <div
                                class="text-[10px] font-bold text-slate-500 leading-relaxed uppercase tracking-widest"
                            >
                                Pastikan judul modul mendeskripsikan isi materi
                                dengan jelas untuk memudahkan mahasiswa.
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
                <label
                    for="content-editor"
                    class="block text-sm font-bold text-slate-700"
                    >Konten Instruksional
                    <span class="text-rose-500">*</span></label
                >
                <div id="content-editor">
                    <QuillEditor bind:value={$form.content} height="500px" />
                </div>
                {#if $form.errors.content}
                    <p class="text-rose-500 text-xs mt-1">
                        {$form.errors.content}
                    </p>
                {/if}
            </div>
        </DataForm>
    </div>
</App>
