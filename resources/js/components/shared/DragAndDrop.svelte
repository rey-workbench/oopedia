<script lang="ts">
    import { HelpCircle, List, MousePointer2 } from 'lucide-svelte';
    import Panel from '@/components/ui/Panel.svelte';
    import type { Question } from '@/types';

    let {
        question,
        dragAndDropAnswers = $bindable({}),
    }: {
        question: Question;
        dragAndDropAnswers: Record<string, string>;
    } = $props();

    let activeZone = $state<string | null>(null);

    let parsedQuestion = $derived.by(() => {
        if (!question?.question_text) return { text: '', inlineFound: false, count: 0 };

        let rawText = question.question_text
            .replace(/<p>/g, '')
            .replace(/<\/p>/g, '\n')
            .replace(/<br>/g, '\n');

        let maxZone = 0;
        const text = rawText.replace(/\[blank_(\d+)\]/g, (_, zoneIdStr) => {
            const currentZoneId = zoneIdStr;
            const currentAnswer = dragAndDropAnswers[currentZoneId] || '...';
            const isActive = activeZone === currentZoneId;
            const extraClass = isActive
                ? 'bg-primary-100 border-primary-600 scale-[1.05] ring-4 ring-primary-300 shadow-xl'
                : 'bg-white border-primary-100 hover:bg-primary-50 hover:border-primary-300';

            const zoneNum = parseInt(zoneIdStr, 10);
            if (zoneNum > maxZone) maxZone = zoneNum;

            return `<span class="drop-zone inline-flex min-w-[120px] h-10 border-2 mx-1.5 items-center justify-center text-primary-900 font-black rounded-xl px-4 shadow-sm transition-all duration-300 cursor-pointer ${extraClass}" data-zone="${currentZoneId}">${currentAnswer}</span>`;
        });

        return { text, inlineFound: maxZone > 0, count: maxZone };
    });


    function handleDragStart(event: DragEvent, answerText: string) {
        if (!event.dataTransfer) return;
        event.dataTransfer.setData('text/plain', answerText);
        event.dataTransfer.effectAllowed = 'move';

        if (event.target instanceof HTMLElement) {
            const el = event.target;
            setTimeout(() => el.classList.add('opacity-30', 'scale-95', 'grayscale'), 0);
        }
    }

    function handleDragEnd(event: DragEvent) {
        if (event.target instanceof HTMLElement) {
            event.target.classList.remove('opacity-30', 'scale-95', 'grayscale');
        }
        activeZone = null;
    }

    function handleDragOver(event: DragEvent) {
        event.preventDefault();
        if (event.dataTransfer) event.dataTransfer.dropEffect = 'move';

        const target = event.target as HTMLElement;
        const zone = target.closest('.drop-zone') as HTMLElement;
        if (zone) {
            activeZone = zone.dataset['zone'] || null;
        } else {
            activeZone = null;
        }
    }

    function handleDragLeave(event: DragEvent) {
        const target = event.target as HTMLElement;
        const zone = target.closest('.drop-zone') as HTMLElement;
        if (zone && activeZone === zone.dataset['zone']) {
            activeZone = null;
        }
    }

    function handleDrop(event: DragEvent) {
        event.preventDefault();
        const target = event.target as HTMLElement;
        const zone = target.closest('.drop-zone') as HTMLElement;

        if (zone && event.dataTransfer) {
            const answerText = event.dataTransfer.getData('text/plain');
            const zoneId = zone.dataset['zone'];
            if (zoneId) {
                dragAndDropAnswers = {
                    ...dragAndDropAnswers,
                    [zoneId]: answerText,
                };
            }
        }
        activeZone = null;
    }

    function handleZoneClick(event: MouseEvent) {
        const target = event.target as HTMLElement;
        const zone = target.closest('.drop-zone') as HTMLElement;
        if (zone) {
            const zoneId = zone.dataset['zone'];
            if (zoneId && dragAndDropAnswers[zoneId]) {
                const newAnswers = { ...dragAndDropAnswers };
                delete newAnswers[zoneId];
                dragAndDropAnswers = newAnswers;
            }
        }
    }
</script>

<div class="space-y-10">
    <div class="space-y-4">
        <div class="flex items-center gap-2 text-[10px] font-bold tracking-widest text-slate-400 uppercase">
            <HelpCircle size={14} class="text-primary-500" /> Kanvas Pertanyaan
        </div>

        <!-- svelte-ignore a11y_click_events_have_key_events -->
        <!-- svelte-ignore a11y_no_static_element_interactions -->
        <Panel
            variant="none"
            rounded="3xl"
            padding="p-10"
            class="border-2 border-slate-100 bg-slate-50/50 leading-[3rem] font-semibold text-slate-700 shadow-inner group selection:bg-primary-100"
            ondragover={handleDragOver}
            ondragleave={handleDragLeave}
            ondrop={handleDrop}
            onclick={handleZoneClick}
        >
            <div class="text-xl sm:text-2xl tracking-normal">
                {@html parsedQuestion.text}
            </div>
            
            <div class="mt-8 flex items-center gap-2 text-[9px] font-bold text-slate-300 uppercase tracking-[0.2em] border-t border-slate-200/50 pt-4">
                <MousePointer2 size={10} /> Klik kotak untuk membatalkan pilihan
            </div>
        </Panel>
    </div>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <List size={14} class="text-primary-500" />
                <div class="text-[10px] font-black tracking-widest text-slate-400 uppercase">
                    Pilihan Komponen
                </div>
            </div>
            <div class="h-px flex-1 ml-6 bg-slate-100"></div>
        </div>

        <div class="flex flex-wrap gap-4 p-2">
            {#each question.answers as answer (answer.id)}
                {@const isUsed = Object.values(dragAndDropAnswers).includes(
                    answer.answer_text ?? ''
                )}
                <div
                    class="group relative"
                >
                    <div
                        class="draggable flex items-center gap-3 rounded-[1.25rem] border-2 px-8 py-5 text-base font-black shadow-sm transition-all duration-300 select-none
                        {isUsed
                            ? 'cursor-not-allowed border-slate-100 bg-slate-200/30 text-slate-300 shadow-none grayscale'
                            : 'border-white bg-white text-primary-950 hover:border-primary-500 cursor-grab hover:-translate-y-1 hover:shadow-2xl hover:shadow-primary-100 active:cursor-grabbing active:scale-95 ring-1 ring-slate-100'}"
                        draggable={!isUsed}
                        role="listitem"
                        ondragstart={(e) => !isUsed && handleDragStart(e, answer.answer_text ?? '')}
                        ondragend={handleDragEnd}
                    >
                        {#if !isUsed}
                            <div class="bg-primary-500 h-2.5 w-2.5 rounded-full shadow-lg shadow-primary-200 group-hover:animate-ping"></div>
                        {:else}
                            <div class="bg-slate-300 h-2.5 w-2.5 rounded-full"></div>
                        {/if}
                        {answer.answer_text}
                    </div>
                </div>
            {/each}
        </div>
    </div>
</div>
