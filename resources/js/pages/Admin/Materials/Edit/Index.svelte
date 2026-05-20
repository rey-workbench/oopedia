<script lang="ts">
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Input from '@/components/ui/Input.svelte';
    import Alert from '@/components/ui/Alert.svelte';
    import ImageUpload from '@/components/ui/ImageUpload.svelte';
    import QuillEditor from '@/components/ui/QuillEditor.svelte';
    import ContentDisplay from '@/components/ui/ContentDisplay.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Toggle from '@/components/ui/Toggle.svelte';
    import { untrack } from 'svelte';
    import { MaterialFormState } from '@/states/Admin/MaterialState.svelte';
    import { ROUTES } from '@/utils/route';
    import { ArrowLeft, RefreshCw, CloudUpload, Eye } from '@lucide/svelte';

    let { material } = $props();

    const state = untrack(() => new MaterialFormState(material));
</script>

<App title="Edit Materi">
    <div class="space-y-12 pb-20">
        <PageHeader
            id="page-header"
            title="Pembaruan Kurikulum"
            subtitle="Modifikasi konten instruksional dan optimasi media visual."
        >
            {#snippet actions()}
                <Button href={ROUTES.ADMIN.MATERIALS.INDEX} variant="ghost" icon={ArrowLeft}
                    >BATALKAN MODIFIKASI</Button
                >
            {/snippet}
        </PageHeader>

        <form
            id="material-editor-form"
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
                    <h3 class="text-lg font-bold text-slate-800">Sinkronisasi & Konten Modul</h3>
                </div>

                <div class="space-y-10 p-6">
                    <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
                        <div class="space-y-6 lg:col-span-2">
                            <Input
                                id="material-title-input"
                                label="Revisi Judul"
                                required
                                bind:value={state.form.title}
                                placeholder=""
                                className="text-lg font-bold tracking-widest"
                                error={state.form.errors['title']}
                            />

                            <Alert variant="primary" class="bg-primary-50/50 border-primary-100">
                                <div class="flex gap-4">
                                    <RefreshCw size={16} class="text-primary-600 mt-1" />
                                    <div
                                        class="text-[10px] leading-relaxed font-bold tracking-widest text-slate-500 uppercase"
                                    >
                                        Perubahan pada modul ini akan langsung disinkronkan ke
                                        seluruh direktori belajar mahasiswa secara real-time.
                                    </div>
                                </div>
                            </Alert>

                            <div
                                id="material-final-project-toggle"
                                class="flex items-center gap-4 rounded-3xl border border-slate-100 bg-slate-50/50 p-6"
                            >
                                <Toggle
                                    bind:checked={state.form.is_final_project}
                                    label="Tandai sebagai Proyek Akhir"
                                />
                            </div>
                        </div>

                        <div id="material-cover-upload" class="lg:col-span-1">
                            <ImageUpload
                                preview={state.coverPreview}
                                label="Sinkronisasi Sampul"
                                emptyIcon="camera"
                                emptyText="Masukkan Gambar"
                                error={state.form.errors['cover_image']}
                                onchange={(e) => state.onImageChange(e)}
                            />
                        </div>
                    </div>

                    <!-- Middle Row: WYSIWYG Editor -->
                    <div class="space-y-4">
                        <span class="block text-sm font-bold text-slate-700"
                            >Basis Pengetahuan Utama (WYSIWYG)
                            <span class="text-rose-500">*</span></span
                        >
                        <div id="material-content-editor">
                            <QuillEditor bind:value={state.form.content} height="500px" />
                        </div>
                        {#if state.form.errors['content']}
                            <p class="mt-1 text-xs text-rose-500">
                                {state.form.errors['content']}
                            </p>
                        {/if}
                    </div>

                    <div
                        class="flex items-center justify-between gap-4 border-t border-slate-100 pt-6"
                    >
                        <div class="flex items-center gap-3">
                            <CloudUpload size={14} class="text-primary-600 animate-pulse" />
                            <span
                                class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                >Sinkronisasi Cloud: Terhubung & Siap</span
                            >
                        </div>

                        <div class="flex gap-4">
                            <Button
                                id="material-save-btn"
                                type="submit"
                                variant="primary"
                                size="lg"
                                class="shadow-primary-900/20 shadow-xl"
                                icon={CloudUpload}
                                disabled={state.form.processing}
                            >
                                {#if state.form.processing}
                                    Memproses...
                                {:else}
                                    SIMPAN PERUBAHAN
                                {/if}
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Preview Section -->
        <div class="space-y-6">
            <div class="flex items-center gap-4">
                <div class="bg-primary-100 text-primary-600 rounded-xl p-2">
                    <Eye size={20} />
                </div>
                <div>
                    <h3 class="text-lg font-bold tracking-widest text-slate-800 uppercase">
                        Pratinjau Hasil (Frontend View)
                    </h3>
                    <p class="text-xs font-medium text-slate-400">
                        Visualisasi materi yang akan dilihat oleh mahasiswa.
                    </p>
                </div>
            </div>

            <Card class="overflow-hidden border-slate-200 bg-white p-0">
                <div
                    class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-6 py-3"
                >
                    <div class="flex gap-1.5">
                        <div class="h-2.5 w-2.5 rounded-full bg-slate-300"></div>
                        <div class="h-2.5 w-2.5 rounded-full bg-slate-300"></div>
                        <div class="h-2.5 w-2.5 rounded-full bg-slate-300"></div>
                    </div>
                    <span class="text-[9px] font-black tracking-[0.2em] text-slate-400 uppercase"
                        >Live Rendering Engine</span
                    >
                </div>
                <div class="bg-white">
                    <ContentDisplay content={state.form.content} />
                </div>
            </Card>
        </div>
    </div>
</App>
