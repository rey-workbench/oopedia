<script>
    import App from "../../../../layouts/App.svelte";
    import PageHeader from "../../../../components/ui/PageHeader.svelte";
    import Card from "../../../../components/ui/Card.svelte";
    import Button from "../../../../components/ui/Button.svelte";
    import Input from "../../../../components/ui/Input.svelte";
    import Alert from "../../../../components/ui/Alert.svelte";
    import QuillEditor from "../../../../components/ui/QuillEditor.svelte";
    import { useForm } from "@inertiajs/svelte";
    import { handleImagePreview } from "../../../../utils/imagePreview";

    let coverPreview = null;

    let form = useForm({
        title: "",
        content: "",
        cover_image: null,
        created_by: null, // handled by backend usually, but blade had hidden input.
        // Actually store method validates created_by, so we might need to pass it or backend sets it.
        // Blade: <input type="hidden" name="created_by" value="{{ auth()->id() }}">
        // Controller: 'created_by' => 'required|exists:users,id'
        // So we must send it.
    });

    // We need auth user id.
    // We can get it from page props if shared.
    // For now let's assume backend helps or we use page prop.
    import { page } from "@inertiajs/svelte";
    import {
        ArrowLeft,
        Info,
        CloudUpload,
        Rocket,
        CheckCheck,
    } from "lucide-svelte";
    $: $form.created_by = $page.props.auth.user.id;

    function handleSubmit() {
        $form.post("/admin/materials", {
            forceFormData: true,
        });
    }
</script>

<App title="Tambah Materi">
    <div class="space-y-12">
        <PageHeader
            title="Arsitek Konten Kurikulum"
            subtitle="Publikasikan modul pembelajaran baru dengan visualisasi premium."
        >
            <div slot="actions">
                <Button href="/admin/materials" variant="ghost" icon={ArrowLeft}
                    >BATALKAN PUBLIKASI</Button
                >
            </div>
        </PageHeader>

        <form on:submit|preventDefault={handleSubmit} class="space-y-12">
            <Card class="border-slate-100 shadow-2xl">
                <div slot="header" class="px-6 py-4 border-b border-slate-50">
                    <h3 class="text-lg font-bold text-slate-800">
                        Identifikasi & Konten Modul
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
                                    >Judul Modul <span class="text-rose-500"
                                        >*</span
                                    ></label
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

                        <div class="lg:col-span-1 space-y-4">
                            <label
                                for="cover_image"
                                class="text-[10px] font-bold uppercase tracking-widest text-slate-400 block"
                                >Visualisasi Sampul</label
                            >
                            <div
                                class={`relative group aspect-video rounded-2xl bg-slate-50 border-2 ${coverPreview ? "border-solid border-primary-500/30" : "border-dashed border-slate-200"} flex flex-col items-center justify-center overflow-hidden transition-all hover:border-primary-500/50`}
                            >
                                {#if coverPreview}
                                    <img
                                        src={coverPreview}
                                        alt="Preview Sampul"
                                        class="absolute inset-0 w-full h-full object-cover"
                                    />
                                {:else}
                                    <div
                                        class="text-center group-hover:scale-110 transition-transform"
                                    >
                                        <CloudUpload
                                            size={24}
                                            class="text-slate-300 mb-2"
                                        />
                                        <p
                                            class="text-[9px] font-bold uppercase tracking-widest text-slate-400"
                                        >
                                            Unggah Sampul
                                        </p>
                                    </div>
                                {/if}
                                <input
                                    id="cover_image"
                                    type="file"
                                    accept="image/*"
                                    class="absolute inset-0 opacity-0 cursor-pointer"
                                    on:change={(e) =>
                                        handleImagePreview(
                                            e,
                                            $form,
                                            "cover_image",
                                            (url) => (coverPreview = url),
                                        )}
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
                            >Konten Instruksional <span class="text-rose-500"
                                >*</span
                            ></label
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

                    <!-- Bottom Row: Deployment -->
                    <div
                        class="pt-6 border-t border-slate-100 flex items-center justify-between"
                    >
                        <div class="flex items-center gap-3">
                            <Rocket size={14} class="text-primary-600" />
                            <span
                                class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
                                >Status Kesiapan: Siap Dipublikasikan</span
                            >
                        </div>
                        <Button
                            type="submit"
                            variant="primary"
                            size="lg"
                            class="shadow-xl shadow-primary-900/20"
                            icon={CheckCheck}
                            disabled={$form.processing}
                        >
                            {#if $form.processing}
                                Publikasi...
                            {:else}
                                PUBLIKASIKAN MODUL
                            {/if}
                        </Button>
                    </div>
                </div>
            </Card>
        </form>
    </div>
</App>
