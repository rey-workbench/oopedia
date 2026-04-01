<script lang="ts">
    import { FileSpreadsheet, Download } from 'lucide-svelte';

    interface Props {
        form: any;
        onFileChange: (e: Event) => void;
        label?: string;
        downloadHref?: string;
        downloadLabel?: string;
    }

    let {
        form,
        onFileChange,
        label = 'Upload File Excel',
        downloadHref = '#',
        downloadLabel = 'Download Template',
    }: Props = $props();
</script>

<div class="space-y-4">
    <span class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">{label}</span>

    <label class="group block w-full cursor-pointer">
        <input type="file" accept=".xlsx,.xls" onchange={onFileChange} class="hidden" />
        <div
            class="hover:border-primary-400 hover:bg-primary-50/50 flex flex-col items-center justify-center gap-6 rounded-[2rem] border-2 border-dashed border-slate-200 p-16 transition-all group-hover:shadow-inner"
        >
            <div
                class="group-hover:bg-primary-100 group-hover:text-primary-600 flex h-16 w-16 items-center justify-center rounded-xl bg-slate-100 text-slate-300 transition-all"
            >
                <FileSpreadsheet size={32} />
            </div>
            <div class="text-center">
                <p class="mb-1 text-xs font-bold tracking-widest text-slate-600 uppercase">
                    {form.excel_file ? form.excel_file.name : 'Klik untuk memilih file'}
                </p>
                <p class="text-[10px] font-medium text-slate-400">Format: .xlsx, .xls</p>
            </div>
        </div>
    </label>

    {#if form.errors?.excel_file}
        <p class="text-[10px] font-bold tracking-widest text-rose-500 uppercase">
            {form.errors.excel_file}
        </p>
    {/if}

    <a
        href={downloadHref}
        class="text-primary-600 inline-flex items-center gap-2 text-[10px] font-bold tracking-widest uppercase hover:underline"
    >
        <Download size={12} />
        {downloadLabel}
    </a>
</div>
