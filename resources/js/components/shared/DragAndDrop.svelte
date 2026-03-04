<script lang="ts">
    import { HelpCircle, List } from 'lucide-svelte';
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
                ? 'bg-primary-100 border-primary-600 scale-[1.05] ring-2 ring-primary-300'
                : 'bg-white border-primary-300 hover:bg-primary-50';

            const zoneNum = parseInt(zoneIdStr, 10);
            if (zoneNum > maxZone) maxZone = zoneNum;

            return `<span class="drop-zone inline-flex min-w-[120px] h-9 border-b-2 mx-1 items-center justify-center text-primary-600 font-bold rounded-md px-3 shadow-sm transition-all duration-200 cursor-pointer ${extraClass}" data-zone="${currentZoneId}">${currentAnswer}</span>`;
        });

        return { text, inlineFound: maxZone > 0, count: maxZone };
    });


    function handleDragStart(event: DragEvent, answerText: string) {
        if (!event.dataTransfer) return;
        event.dataTransfer.setData('text/plain', answerText);
        event.dataTransfer.effectAllowed = 'move';

        // Add ghost effect to dragged item
        if (event.target instanceof HTMLElement) {
            const el = event.target;
            setTimeout(() => el.classList.add('opacity-30', 'scale-95'), 0);
        }
    }

    function handleDragEnd(event: DragEvent) {
        if (event.target instanceof HTMLElement) {
            event.target.classList.remove('opacity-30', 'scale-95');
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

<div class="question-content space-y-8">
    <div class="space-y-4">
        <h5 class="flex items-center text-sm font-bold tracking-widest text-slate-400 uppercase">
            <HelpCircle size={14} class="mr-2" /> Pertanyaan
        </h5>

        <!-- svelte-ignore a11y_click_events_have_key_events -->
        <!-- svelte-ignore a11y_no_static_element_interactions -->
        <div
            class="question-html rounded-2xl border border-slate-200 bg-slate-50 p-6 leading-loose font-medium text-slate-800 transition-all"
            ondragover={handleDragOver}
            ondragleave={handleDragLeave}
            ondrop={handleDrop}
            onclick={handleZoneClick}
        >
            {@html parsedQuestion.text}
        </div>
    </div>

    <div class="space-y-4">
        <div class="bg-primary-50/50 flex items-center justify-between rounded-xl p-3">
            <h5
                class="text-primary-700 flex items-center text-sm font-bold tracking-widest uppercase"
            >
                <List size={16} class="mr-2" /> Pilihan Jawaban
            </h5>
            <span
                class="text-primary-500/80 bg-primary-100 rounded-full px-3 py-1 text-xs font-semibold"
            >
                Tahan & Geser (Drag Drop)
            </span>
        </div>

        <div class="drag-items flex flex-wrap gap-3 p-2">
            {#each question.answers as answer (answer.id)}
                {@const isUsed = Object.values(dragAndDropAnswers).includes(
                    answer.answer_text ?? ''
                )}
                <div
                    class="draggable flex items-center gap-2 rounded-xl border-2 px-6 py-3.5 text-sm font-bold shadow-sm transition-all duration-200 select-none
                    {isUsed
                        ? 'cursor-not-allowed border-slate-200 bg-slate-50 text-slate-300 opacity-50 shadow-none'
                        : 'border-primary-200 text-primary-700 hover:border-primary-500 cursor-grab bg-white hover:-translate-y-0.5 hover:shadow-md active:cursor-grabbing'}"
                    draggable={!isUsed}
                    role="listitem"
                    ondragstart={(e) => !isUsed && handleDragStart(e, answer.answer_text ?? '')}
                    ondragend={handleDragEnd}
                >
                    {#if !isUsed}
                        <div class="bg-primary-500 h-2 w-2 animate-pulse rounded-full"></div>
                    {/if}
                    {answer.answer_text}
                </div>
            {/each}
        </div>
    </div>
</div>
