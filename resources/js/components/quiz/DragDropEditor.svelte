<script lang="ts">
    import Alert from '@/components/ui/Alert.svelte';
    import Card from '@/components/ui/Card.svelte';
    import { GripVertical } from 'lucide-svelte';

    let { value = $bindable() }: { value: string } = $props();

    function handleInput(e: Event) {
        value = (e.target as HTMLDivElement).innerHTML;
    }
</script>

<div class="space-y-4">
    <Alert id="drag-drop-guide" variant="info" class="border-primary-100 bg-primary-50/50">
        <div class="flex flex-col gap-1 text-[11px] leading-relaxed font-medium">
            <strong class="text-primary-900 tracking-wider uppercase">Panduan Drag & Drop:</strong>
            <span class="text-primary-700/80">1. Ketik soal di dalam kotak di bawah ini.</span>
            <span class="text-primary-700/80"
                >2. Buat opsi jawaban di bagian "Konfigurasi Opsi Jawaban".</span
            >
            <span class="text-primary-700/80 flex items-center gap-1">
                3. <strong>Drag (Tarik)</strong> handle <GripVertical
                    size={14}
                    class="inline-block"
                /> pada jawaban ke dalam kotak soal ini.
            </span>
        </div>
    </Alert>

    <Card
        variant="none"
        padding="p-0"
        class="focus-within:border-primary-500 overflow-hidden border-2 border-slate-100 shadow-sm transition-all duration-300 focus-within:shadow-md"
    >
        <div
            id="drag-drop-view"
            contenteditable="true"
            oninput={handleInput}
            class="min-h-[150px] w-full bg-white px-6 py-4 text-sm font-medium tracking-wide text-slate-800 outline-none"
        >
            {@html value}
        </div>
    </Card>
</div>
