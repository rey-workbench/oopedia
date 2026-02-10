<script>
    import App from "../../../layouts/App.svelte";
    import PageHeader from "../../../components/ui/PageHeader.svelte";
    import Card from "../../../components/ui/Card.svelte";
    import Button from "../../../components/ui/Button.svelte";
    import Input from "../../../components/ui/Input.svelte";
    import Alert from "../../../components/ui/Alert.svelte";
    import QuillEditor from "../../../components/ui/QuillEditor.svelte";
    import { useForm } from "@inertiajs/svelte";
    import { ArrowLeft, RefreshCw, Camera, CloudUpload } from "lucide-svelte";

    export let material;

    let coverPreview =
        material.media && material.media.length > 0
            ? `/${material.media[0].media_url}`
            : null;

    let form = useForm({
        _method: "PUT",
        title: material.title,
        content: material.content,
        cover_image: null,
    });

    function handleImageChange(e) {
        const file = e.target.files[0];
        if (file) {
            $form.cover_image = file;
            const reader = new FileReader();
            reader.onload = (e) => {
                coverPreview = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    function handleSubmit() {
        $form.post(`/admin/materials/${material.id}`, {
            forceFormData: true,
        });
    }
</script>

<App title="Edit Materi">
    <div class="space-y-12">
        <PageHeader
            title="Pembaruan Kurikulum"
            subtitle="Modifikasi konten instruksional dan optimasi media visual."
        >
            <div slot="actions">
                <Button href="/admin/materials" variant="ghost" icon={ArrowLeft}
                    >BATALKAN MODIFIKASI</Button
                >
            </div>
        </PageHeader>

        <form on:submit|preventDefault={handleSubmit} class="space-y-12">
            <Card class="border-slate-100 shadow-2xl">
                <div slot="header" class="px-6 py-4 border-b border-slate-50">
                    <h3 class="text-lg font-bold text-slate-800">
                        Sinkronisasi & Konten Modul
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
                                    >Revisi Judul <span class="text-rose-500"
                                        >*</span
                                    ></label
                                >
                                <Input
                                    id="title"
                                    bind:value={$form.title}
                                    className="text-lg font-bold tracking-widest"
                                    error={$form.errors.title}
                                />
                            </div>

                            <Alert
                                variant="primary"
                                class="bg-indigo-50/50 border-indigo-100"
                            >
                                <div class="flex gap-4">
                                    <RefreshCw
                                        size={16}
                                        class="text-indigo-500 mt-1"
                                    />
                                    <div
                                        class="text-[10px] font-bold text-slate-500 leading-relaxed uppercase tracking-widest"
                                    >
                                        Perubahan pada modul ini akan langsung
                                        disinkronkan ke seluruh direktori
                                        belajar mahasiswa secara real-time.
                                    </div>
                                </div>
                            </Alert>
                        </div>

                        <div class="lg:col-span-1 space-y-4">
                            <label
                                for="cover_image"
                                class="text-[10px] font-bold uppercase tracking-widest text-slate-400 block"
                                >Sinkronisasi Sampul</label
                            >
                            <div
                                class={`relative group aspect-video rounded-2xl bg-slate-50 border-2 ${coverPreview ? "border-solid border-blue-500/30" : "border-dashed border-slate-200"} flex flex-col items-center justify-center overflow-hidden transition-all hover:border-indigo-500/50`}
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
                                        <Camera
                                            size={24}
                                            class="text-slate-300 mb-2"
                                        />
                                        <p
                                            class="text-[9px] font-bold uppercase tracking-widest text-slate-400"
                                        >
                                            Masukkan Gambar
                                        </p>
                                    </div>
                                {/if}
                                <input
                                    id="cover_image"
                                    type="file"
                                    accept="image/*"
                                    class="absolute inset-0 opacity-0 cursor-pointer"
                                    on:change={handleImageChange}
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
                            >Basis Pengetahuan Utama (WYSIWYG) <span
                                class="text-rose-500">*</span
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

                    <!-- Bottom Row: commit -->
                    <div
                        class="pt-6 border-t border-slate-100 flex items-center justify-between"
                    >
                        <div class="flex items-center gap-3">
                            <CloudUpload
                                size={14}
                                class="text-indigo-500 animate-pulse"
                            />
                            <span
                                class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
                                >Sinkronisasi Cloud: Terhubung & Siap</span
                            >
                        </div>
                        <Button
                            type="submit"
                            variant="primary"
                            size="lg"
                            class="shadow-xl shadow-indigo-500/20 bg-indigo-600 hover:bg-indigo-700"
                            icon={CloudUpload}
                            disabled={$form.processing}
                        >
                            {#if $form.processing}
                                Menyimpan...
                            {:else}
                                SIMPAN PERUBAHAN
                            {/if}
                        </Button>
                    </div>
                </div>
            </Card>
        </form>
    </div>
</App>
