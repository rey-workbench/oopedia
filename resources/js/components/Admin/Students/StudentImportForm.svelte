<script>
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import { FileSpreadsheet, Upload, Download } from "lucide-svelte";
    import { StudentImportState } from "@/states/Admin/StudentState.svelte";

    const state = new StudentImportState();
    const form = state.form;
</script>

<div class="max-w-2xl mx-auto">
    <Card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
        <div slot="header">
            <h6 class="text-lg font-bold tracking-widest uppercase mb-0">
                Impor Dataset Mahasiswa
            </h6>
        </div>

        <div class="p-8">
            <form
                on:submit|preventDefault={() => state.submit()}
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
                            <li>Gunakan format berkas .xlsx atau .xls</li>
                            <li>Data mahasiswa yang sudah ada akan dilewati</li>
                            <li>
                                Sistem akan otomatis memberikan akses role level
                                3
                            </li>
                            <li>Sangat disarankan memakai template resmi</li>
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
                                on:change={(e) => state.handleFileChange(e)}
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
                                    class="text-slate-200 group-hover:text-primary-600 mb-4 transition-colors"
                                />
                                <span
                                    class="text-xs font-bold text-slate-400 group-hover:text-slate-900"
                                >
                                    {#if $form.excel_file}{$form.excel_file
                                            .name}{:else}Otorisasi Unggah Berkas{/if}
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
                        class="flex-1 py-4 shadow-xl shadow-primary-900/20"
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
