<script>
    import App from "../../../../layouts/App.svelte";
    import PageHeader from "../../../../components/ui/PageHeader.svelte";
    import Card from "../../../../components/ui/Card.svelte";
    import Button from "../../../../components/ui/Button.svelte";
    import { useForm } from "@inertiajs/svelte";
    import {
        ArrowLeft,
        FileSpreadsheet,
        Upload,
        Download,
    } from "lucide-svelte";

    const form = useForm({
        excel_file: null,
    });

    function handleSubmit() {
        $form.post("/admin/students/import");
    }

    function handleFileChange(e) {
        $form.excel_file = e.target.files[0];
    }
</script>

<App title="Impor Data Mahasiswa">
    <div class="space-y-12">
        <PageHeader
            title="Protokol Impor Mahasiswa"
            subtitle="Integrasi massal bios data mahasiswa ke dalam registry OOPedia."
        >
            <div slot="actions">
                <Button href="/admin/students" variant="ghost" icon={ArrowLeft}
                    >KEMBALI KE DAFTAR</Button
                >
            </div>
        </PageHeader>

        <div class="max-w-2xl mx-auto">
            <Card
                padding="p-0"
                class="overflow-hidden border-slate-100 shadow-2xl"
            >
                <div slot="header" class="bg-blue-600 px-8 py-6 text-white">
                    <h6
                        class="text-lg font-bold tracking-widest uppercase mb-0"
                    >
                        Impor Dataset Mahasiswa
                    </h6>
                </div>

                <div class="p-8">
                    <form
                        on:submit|preventDefault={handleSubmit}
                        class="space-y-8"
                    >
                        <div class="space-y-4">
                            <div
                                class="bg-slate-50 p-6 rounded-3xl border border-slate-100"
                            >
                                <h4
                                    class="text-xs font-bold text-slate-900 uppercase tracking-widest mb-2"
                                >
                                    Algoritma Impor
                                </h4>
                                <ul
                                    class="text-[10px] font-bold text-slate-500 space-y-2 list-disc pl-4 uppercase tracking-wider"
                                >
                                    <li>
                                        Gunakan format berkas .xlsx atau .xls
                                    </li>
                                    <li>
                                        Data mahasiswa yang sudah ada akan
                                        dilewati
                                    </li>
                                    <li>
                                        Sistem akan otomatis memberikan akses
                                        role level 3
                                    </li>
                                    <li>
                                        Sangat disarankan memakai template resmi
                                    </li>
                                </ul>
                            </div>

                            <div class="space-y-2">
                                <label
                                    for="excel_file"
                                    class="text-[10px] font-bold uppercase text-slate-400 font-poppins"
                                    >Berkas Dataset (Excel)</label
                                >
                                <div class="relative">
                                    <input
                                        type="file"
                                        id="excel_file"
                                        on:change={handleFileChange}
                                        accept=".xlsx,.xls,.csv"
                                        class="hidden"
                                        required
                                    />
                                    <label
                                        for="excel_file"
                                        class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-slate-200 rounded-[2.5rem] bg-slate-50 hover:bg-slate-100/50 hover:border-blue-400 cursor-pointer transition-all group"
                                    >
                                        <FileSpreadsheet
                                            size={48}
                                            strokeWidth={1.5}
                                            class="text-slate-200 group-hover:text-blue-500 mb-4 transition-colors"
                                        />
                                        <span
                                            class="text-xs font-bold text-slate-400 group-hover:text-slate-900"
                                        >
                                            {#if $form.excel_file}{$form
                                                    .excel_file
                                                    .name}{:else}Otorisasi
                                                Unggah Berkas{/if}
                                        </span>
                                    </label>
                                </div>
                                {#if $form.errors.excel_file}
                                    <p
                                        class="text-[10px] font-bold text-rose-500 uppercase tracking-widest"
                                    >
                                        {$form.errors.excel_file}
                                    </p>
                                {/if}
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <Button
                                type="submit"
                                variant="primary"
                                class="flex-1 py-4 shadow-xl shadow-blue-500/20"
                                icon={Upload}
                                disabled={$form.processing}
                            >
                                {#if $form.processing}SINGKRONISASI DATA...{:else}EKSEKUSI
                                    IMPOR DATASET{/if}
                            </Button>
                            <Button
                                href="/admin/students/download-template"
                                variant="outline"
                                class="py-4"
                                icon={Download}
                            >
                                TEMPLATE FORMAL
                            </Button>
                        </div>
                    </form>
                </div>
            </Card>
        </div>
    </div>
</App>
