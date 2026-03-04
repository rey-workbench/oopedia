<script lang="ts">
    import Alert from '@/components/ui/Alert.svelte';
    import { GripVertical } from 'lucide-svelte';

    let { value = $bindable() }: { value: string } = $props();
</script>

<div class="space-y-3">
    <Alert variant="info" class="normal-case tracking-normal border-blue-100 bg-blue-50 text-slate-500">
        <div class="flex flex-col gap-1 text-[11px] font-medium empty:hidden leading-relaxed normal-case">
            <strong class="text-slate-700">Panduan Drag & Drop:</strong>
            <span>1. Ketik soal di dalam kotak di bawah ini.</span>
            <span>2. Buat opsi jawaban di bagian "Konfigurasi Opsi Jawaban".</span>
            <span class="flex items-center gap-1">
                3. <strong>Drag (Tarik)</strong> handle <GripVertical size={14} class="inline-block text-slate-500" /> pada jawaban ke dalam kotak soal ini.
            </span>
        </div>
    </Alert>
    <div
        contenteditable="true"
        bind:innerHTML={value}
        class="focus:border-primary-600 focus:ring-primary-50 min-h-[150px] w-full resize-none rounded-2xl border-2 border-slate-200 bg-white px-6 py-4 text-sm font-medium tracking-wide text-slate-800 shadow-sm transition-all focus:outline-none focus:ring-4"
    ></div>
</div>
