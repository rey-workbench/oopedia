<script lang="ts">
    import Alert from '@/components/ui/Alert.svelte';
    import Card from '@/components/ui/Card.svelte';
    import { GripVertical } from 'lucide-svelte';

    interface Props {
        value?: string;
        id?: string;
        class?: string;
    }

    let { value = $bindable(''), id = 'drag-drop-view', class: className = '' }: Props = $props();

    let element = $state<HTMLDivElement>();

    // Sync internal HTML with value rune
    $effect(() => {
        if (element && element.innerHTML !== value) {
            element.innerHTML = value || '';
        }
    });

    function handleInput() {
        if (element) {
            value = element.innerHTML;
        }
    }

    function handleDrop(e: DragEvent) {
        e.preventDefault();
        const data = e.dataTransfer?.getData('text/plain');
        if (data && element) {
            element.focus();
            const selection = window.getSelection();
            if (selection && selection.rangeCount > 0 && element.contains(selection.anchorNode)) {
                const range = selection.getRangeAt(0);
                range.deleteContents();
                const node = document.createTextNode(data);
                range.insertNode(node);

                // Move cursor after the inserted text
                range.setStartAfter(node);
                range.collapse(true);
                selection.removeAllRanges();
                selection.addRange(range);
            } else {
                element.innerHTML += data;
            }
            value = element.innerHTML;
        }
    }

    function handleDragOver(e: DragEvent) {
        e.preventDefault();
        if (e.dataTransfer) {
            e.dataTransfer.dropEffect = 'copy';
        }
    }
</script>

<div class="space-y-4 {className}">
    <Alert id="drag-drop-guide" variant="info" class="border-primary-100 bg-primary-50/50">
        <div class="flex flex-col gap-1 text-xs leading-relaxed font-medium">
            <strong class="text-primary-900 tracking-wider uppercase">Panduan Drag & Drop:</strong>
            <span class="text-primary-700/80">1. Ketik soal di dalam kotak di bawah ini.</span>
            <span class="text-primary-700/80"
                >2. Buat opsi jawaban di bagian "Konfigurasi Opsi Jawaban".</span
            >
            <span class="text-primary-700/80 flex items-center gap-1">
                3. <strong>Drag (Tarik)</strong> handle <GripVertical
                    size={14}
                    class="inline-block"
                /> pada jawaban ke dalam kotak soal ini untuk menyisipkan teks.
            </span>
        </div>
    </Alert>

    <Card
        variant="none"
        padding="p-0"
        class="focus-within:border-primary-500 overflow-hidden border-2 border-slate-100 shadow-sm transition-all duration-300 focus-within:shadow-md"
    >
        <div
            {id}
            bind:this={element}
            contenteditable="true"
            oninput={handleInput}
            ondrop={handleDrop}
            ondragover={handleDragOver}
            class="min-h-[150px] w-full bg-white px-6 py-4 text-sm font-medium tracking-wide text-slate-800 outline-none"
            role="textbox"
            aria-multiline="true"
            tabindex="0"
        ></div>
    </Card>
</div>
