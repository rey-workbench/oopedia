<script>
    import { FileSpreadsheet, Download } from "lucide-svelte";

    export let form;
    export let onFileChange;
    export let label = "Upload File Excel";
    export let downloadHref = "#";
    export let downloadLabel = "Download Template";
</script>

<div class="space-y-4">
    <label class="text-[10px] font-bold uppercase text-slate-400 tracking-widest">{label}</label>

    <label
        class="block w-full cursor-pointer group"
    >
        <input
            type="file"
            accept=".xlsx,.xls"
            on:change={onFileChange}
            class="hidden"
        />
        <div class="border-2 border-dashed border-slate-200 rounded-[2rem] p-16 flex flex-col items-center justify-center gap-6 hover:border-primary-400 hover:bg-primary-50/50 transition-all group-hover:shadow-inner">
            <div class="w-16 h-16 bg-slate-100 rounded-[1.5rem] flex items-center justify-center text-slate-300 group-hover:bg-primary-100 group-hover:text-primary-600 transition-all">
                <FileSpreadsheet size={32} />
            </div>
            <div class="text-center">
                <p class="text-xs font-bold text-slate-600 uppercase tracking-widest mb-1">
                    {$form.file ? $form.file.name : "Klik untuk memilih file"}
                </p>
                <p class="text-[10px] text-slate-400 font-medium">Format: .xlsx, .xls</p>
            </div>
        </div>
    </label>

    {#if $form.errors.file}
        <p class="text-[10px] font-bold text-rose-500 uppercase tracking-widest">{$form.errors.file}</p>
    {/if}

    <a
        href={downloadHref}
        class="inline-flex items-center gap-2 text-[10px] font-bold text-primary-600 hover:underline uppercase tracking-widest"
    >
        <Download size={12} /> {downloadLabel}
    </a>
</div>
