<script lang="ts">
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import Button from '@/components/ui/Button.svelte';
    import ImportInstructions from '@/components/layout/ImportInstructions.svelte';
    import FileUploadZone from '@/components/ui/FileUploadZone.svelte';
    import { ArrowLeft, Upload } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';
    import { StudentImportState } from '@/states/Admin/StudentState.svelte';

    const state = new StudentImportState();

    const items = [
        'File harus dalam format <strong>.xlsx</strong> atau <strong>.xls</strong>',
        'Mahasiswa yang sudah terdaftar akan dilewati secara otomatis',
        'Role otomatis ditentukan sebagai Mahasiswa (Level 3)',
        'Gunakan template resmi yang telah disediakan',
    ];
</script>

<App title="Impor Data Mahasiswa">
    <div class="space-y-12">
        <PageHeader
            title="Protokol Impor Mahasiswa"
            subtitle="Integrasi massal bios data mahasiswa ke dalam registry OOPedia."
        >
            {#snippet actions()}
                <Button href={ROUTES.ADMIN.STUDENTS.INDEX} variant="ghost" icon={ArrowLeft}
                    >KEMBALI KE DAFTAR</Button
                >
            {/snippet}
        </PageHeader>

        <div class="mx-auto max-w-2xl">
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
                        <h3 class="text-lg font-bold text-slate-800">Impor Dataset Mahasiswa</h3>
                    </div>

                    <div class="space-y-10 p-6">
                        <ImportInstructions {items} />
                        <FileUploadZone
                            form={state.form}
                            onFileChange={(e) => state.handleFileChange(e)}
                            label="Berkas Dataset (Excel)"
                            downloadHref="/admin/students/download-template"
                            downloadLabel="TEMPLATE FORMAL"
                        />

                        <div
                            class="flex items-center justify-between gap-4 border-t border-slate-100 pt-6"
                        >
                            <div class="flex items-center gap-3"></div>

                            <div class="flex gap-4">
                                <Button href={null} variant="ghost">
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
                                    icon={Upload}
                                    disabled={state.form.processing}
                                >
                                    {#if state.form.processing}
                                        Memproses...
                                    {:else}
                                        EKSEKUSI IMPOR DATASET
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
