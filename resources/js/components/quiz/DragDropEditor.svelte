<script lang="ts">
    import Alert from '@/components/ui/Alert.svelte';
    import Card from '@/components/ui/Card.svelte';
    import { GripVertical } from 'lucide-svelte';

    let { value = $bindable() }: { value: string } = $props();
</script>

<div class="space-y-4">
    <Alert variant="info" class="border-primary-100 bg-primary-50/50">
        <div class="flex flex-col gap-1 text-[11px] font-medium leading-relaxed">
            <strong class="text-primary-900 tracking-wider uppercase">Panduan Drag & Drop:</strong>
            <span class="text-primary-700/80">1. Ketik soal di dalam kotak di bawah ini.</span>
            <span class="text-primary-700/80">2. Buat opsi jawaban di bagian "Konfigurasi Opsi Jawaban".</span>
            <span class="text-primary-700/80 flex items-center gap-1">
                3. <strong>Drag (Tarik)</strong> handle <GripVertical size={14} class="inline-block" /> pada jawaban ke dalam kotak soal ini.
            </span>
        </div>
    </Alert>
    
    <Card variant="none" padding="p-0" class="overflow-hidden border-2 border-slate-100 focus-within:border-primary-500 transition-all duration-300 shadow-sm focus-within:shadow-md">
        <div
            contenteditable="true"
            bind:innerHTML={value}
            class="min-h-[150px] w-full resize-none bg-white px-6 py-4 text-sm font-medium tracking-wide text-slate-800 outline-none"
        ></div>
    </Card>
</div>
