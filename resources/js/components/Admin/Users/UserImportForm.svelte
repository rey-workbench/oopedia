<script>
    import Card from "@/ui/Card.svelte";
    import Button from "@/ui/Button.svelte";
    import { FileSpreadsheet, Upload, Download } from "lucide-svelte";
    import { UserImportState } from "@/states/Admin/UserImportState.svelte";

    const state = new UserImportState();
    const form = state.form;
</script>

<div class="max-w-2xl mx-auto">
    <Card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
        <div slot="header" class="bg-primary-600 px-8 py-6 text-white">
            <h6 class="text-lg font-bold tracking-widest uppercase mb-0">
                Impor Dataset Admin
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
                            Instruksi Impor
                        </h4>
                        <ul
                            class="text-[10px] font-bold text-slate-500 space-y-2 list-disc pl-4 uppercase tracking-wider"
                        >
                            <li>Gunakan format berkas .xlsx atau .xls</li>
                            <li>
                                Pastikan kolom Nama, Email, dan Password terisi
                            </li>
                            <li>Email harus unik di dalam sistem</li>
                            <li>Unduh template di bawah jika diperlukan</li>
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
                                class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-slate-200 rounded-[2.5rem] bg-slate-50 hover:bg-slate-100/50 hover:border-primary-400 cursor-pointer transition-all group"
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
                                            .name}{:else}Klik atau seret berkas
                                        ke sini{/if}
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
                        {#if $form.processing}PROSES UNGGAH...{:else}MULAI IMPOR
                            DATASET{/if}
                    </Button>
                    <Button
                        href="/admin/users/download-template"
                        variant="outline"
                        class="py-4"
                        icon={Download}
                    >
                        UNDUH TEMPLATE
                    </Button>
                </div>
            </form>
        </div>
    </Card>
</div>
