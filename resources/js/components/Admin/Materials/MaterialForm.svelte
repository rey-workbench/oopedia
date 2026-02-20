<script>
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import Input from "@/components/ui/Input.svelte";
    import Alert from "@/components/ui/Alert.svelte";
    import QuillEditor from "@/components/ui/QuillEditor.svelte";
    import {
        Info,
        CloudUpload,
        Rocket,
        CheckCheck,
        Camera,
        RefreshCw,
    } from "lucide-svelte";
    import { MaterialFormState } from "@/states/Admin/MaterialState.svelte";

    export let material = null; // If provided, it's Edit mode

    const state = new MaterialFormState(material);
    const form = state.form;
</script>

<form on:submit|preventDefault={() => state.submit()} class="space-y-12">
    <Card class="border-slate-100 shadow-2xl">
        <div slot="header">
            <h3 class="text-lg font-bold text-slate-800">
                {state.isEdit
                    ? "Sinkronisasi & Konten Modul"
                    : "Identifikasi & Konten Modul"}
            </h3>
        </div>

        <div class="space-y-10 p-6">
            <!-- Top Row: Title & Cover Image -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <div class="lg:col-span-2 space-y-6">
                    <div class="space-y-2">
                        <label
                            for="title"
                            class="block text-sm font-bold text-slate-700"
                            >{state.isEdit ? "Revisi Judul" : "Judul Modul"}
                            <span class="text-rose-500">*</span></label
                        >
                        <Input
                            id="title"
                            bind:value={$form.title}
                            placeholder={state.isEdit
                                ? ""
                                : "e.g. Fundamental of Object Oriented Programming"}
                            className="text-lg font-bold tracking-widest"
                            error={$form.errors.title}
                        />
                    </div>

                    <Alert
                        variant="primary"
                        class="bg-primary-50/50 border-primary-100"
                    >
                        <div class="flex gap-4">
                            <svelte:component
                                this={state.isEdit ? RefreshCw : Info}
                                size={16}
                                class="text-primary-600 mt-1"
                            />
                            <div
                                class="text-[10px] font-bold text-slate-500 leading-relaxed uppercase tracking-widest"
                            >
                                {state.isEdit
                                    ? "Perubahan pada modul ini akan langsung disinkronkan ke seluruh direktori belajar mahasiswa secara real-time."
                                    : "Pastikan judul modul mendeskripsikan isi materi dengan jelas untuk memudahkan mahasiswa."}
                            </div>
                        </div>
                    </Alert>
                </div>

                <div class="lg:col-span-1 space-y-4">
                    <label
                        for="cover_image"
                        class="text-[10px] font-bold uppercase tracking-widest text-slate-400 block"
                        >{state.isEdit
                            ? "Sinkronisasi Sampul"
                            : "Visualisasi Sampul"}</label
                    >
                    <div
                        class={`relative group aspect-video rounded-2xl bg-slate-50 border-2 ${state.coverPreview ? "border-solid border-primary-500/30" : "border-dashed border-slate-200"} flex flex-col items-center justify-center overflow-hidden transition-all hover:border-primary-500/50`}
                    >
                        {#if state.coverPreview}
                            <img
                                src={state.coverPreview}
                                alt="Preview Sampul"
                                class="absolute inset-0 w-full h-full object-cover"
                            />
                        {:else}
                            <div
                                class="text-center group-hover:scale-110 transition-transform"
                            >
                                <svelte:component
                                    this={state.isEdit ? Camera : CloudUpload}
                                    size={24}
                                    class="text-slate-300 mb-2"
                                />
                                <p
                                    class="text-[9px] font-bold uppercase tracking-widest text-slate-400"
                                >
                                    {state.isEdit
                                        ? "Masukkan Gambar"
                                        : "Unggah Sampul"}
                                </p>
                            </div>
                        {/if}
                        <input
                            id="cover_image"
                            type="file"
                            accept="image/*"
                            class="absolute inset-0 opacity-0 cursor-pointer"
                            on:change={(e) => state.onImageChange(e)}
                        />
                    </div>
                    {#if $form.errors.cover_image}
                        <p class="text-rose-500 text-xs mt-1">
                            {$form.errors.cover_image}
                        </p>
                    {/if}
                </div>
            </div>

            <!-- Middle Row: WYSIWYG Editor -->
            <div class="space-y-4">
                <label
                    for="content-editor"
                    class="block text-sm font-bold text-slate-700"
                    >{state.isEdit
                        ? "Basis Pengetahuan Utama (WYSIWYG)"
                        : "Konten Instruksional"}
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

            <!-- Bottom Row: Deployment -->
            <div
                class="pt-6 border-t border-slate-100 flex items-center justify-between"
            >
                <div class="flex items-center gap-3">
                    <svelte:component
                        this={state.isEdit ? CloudUpload : Rocket}
                        size={14}
                        class={`text-primary-600 ${state.isEdit ? "animate-pulse" : ""}`}
                    />
                    <span
                        class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
                        >{state.isEdit
                            ? "Sinkronisasi Cloud: Terhubung & Siap"
                            : "Status Kesiapan: Siap Dipublikasikan"}</span
                    >
                </div>
                <Button
                    type="submit"
                    variant="primary"
                    size="lg"
                    class={`shadow-xl shadow-primary-900/20 ${state.isEdit ? "bg-primary-600 hover:bg-primary-700" : ""}`}
                    icon={state.isEdit ? CloudUpload : CheckCheck}
                    disabled={$form.processing}
                >
                    {#if $form.processing}
                        {state.isEdit ? "Menyimpan..." : "Publikasi..."}
                    {:else}
                        {state.isEdit
                            ? "SIMPAN PERUBAHAN"
                            : "PUBLIKASIKAN MODUL"}
                    {/if}
                </Button>
            </div>
        </div>
    </Card>
</form>
