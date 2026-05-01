<script lang="ts">
    import { MousePointer2, List, Terminal } from 'lucide-svelte';
    import type { Question } from '@/types';

    let {
        question,
        dragAndDropAnswers = $bindable({}),
        disabled = false,
        showResult = false,
        isOverallCorrect = false,
    }: {
        question: Question;
        dragAndDropAnswers: Record<string, string>;
        disabled?: boolean;
        showResult?: boolean;
        isOverallCorrect?: boolean;
    } = $props();

    let activeZone = $state<string | null>(null);

    let parsedQuestion = $derived.by(() => {
        if (!question?.question_text) return { text: '', count: 0 };

        let rawText = question.question_text
            .replace(/<p>/g, '')
            .replace(/<\/p>/g, '\n')
            .replace(/<br>/g, '\n');

        let maxZone = 0;
        const text = rawText.replace(/\[blank_(\d+)\]/g, (_: string, zoneIdStr: string) => {
            const currentAnswer = dragAndDropAnswers[zoneIdStr] || '···';
            const isActive = activeZone === zoneIdStr;
            const isFilled = !!dragAndDropAnswers[zoneIdStr];

            const zoneNum = parseInt(zoneIdStr, 10);
            const correctAnswer = question?.answers?.find(
                (a) => (a as any).zone_index == zoneNum
            )?.answer_text;
            const isCorrect = currentAnswer === correctAnswer;

            const baseClass =
                'drop-zone inline-flex min-w-[110px] h-9 border-2 border-b-4 mx-1.5 items-center justify-center font-black rounded-xl px-4 shadow-sm transition-all duration-200 text-sm';

            let stateClass = '';
            if (showResult) {
                if (isFilled) {
                    // Sync with overall backend status if provided, otherwise fallback to local check
                    const finalIsCorrect = isOverallCorrect || isCorrect;
                    stateClass = finalIsCorrect
                        ? 'bg-emerald-50 border-emerald-600 border-b-emerald-700 text-emerald-900'
                        : 'bg-rose-50 border-rose-600 border-b-rose-700 text-rose-900';
                } else {
                    stateClass =
                        'bg-white/10 border-white/20 border-b-white/10 text-white/50 opacity-30';
                }
            } else {
                stateClass = isActive
                    ? 'bg-primary-100 border-primary-600 border-b-primary-700 ring-4 ring-primary-200/50 shadow-lg text-primary-900 translate-y-[1px]'
                    : isFilled
                      ? 'bg-primary-50 border-primary-300 border-b-primary-400 text-primary-800 hover:border-primary-400'
                      : 'bg-white/10 border-white/20 border-b-white/10 text-white/50 hover:bg-white/20 hover:border-white/40';
            }

            if (!disabled) stateClass += ' cursor-pointer';

            if (zoneNum > maxZone) maxZone = zoneNum;

            return `<span class="${baseClass} ${stateClass}" data-zone="${zoneIdStr}">${currentAnswer}</span>`;
        });

        return { text, count: maxZone };
    });

    function handleDragStart(event: DragEvent, answerText: string) {
        if (disabled || !event.dataTransfer) return;
        event.dataTransfer.setData('text/plain', answerText);
        event.dataTransfer.effectAllowed = 'move';
        if (event.target instanceof HTMLElement) {
            const el = event.target;
            setTimeout(() => el.classList.add('opacity-40', 'grayscale', 'border-primary-200'), 0);
        }
    }

    function handleDragEnd(event: DragEvent) {
        if (event.target instanceof HTMLElement) {
            event.target.classList.remove('opacity-40', 'grayscale', 'border-primary-200');
        }
        activeZone = null;
    }

    function handleDragOver(event: DragEvent) {
        if (disabled) return;
        event.preventDefault();
        if (event.dataTransfer) event.dataTransfer.dropEffect = 'move';
        const zone = (event.target as HTMLElement).closest('.drop-zone') as HTMLElement;
        activeZone = zone ? zone.dataset['zone'] || null : null;
    }

    function handleDragLeave(event: DragEvent) {
        const zone = (event.target as HTMLElement).closest('.drop-zone') as HTMLElement;
        if (zone && activeZone === zone.dataset['zone']) activeZone = null;
    }

    function handleDrop(event: DragEvent) {
        if (disabled) return;
        event.preventDefault();
        const zone = (event.target as HTMLElement).closest('.drop-zone') as HTMLElement;
        if (zone && event.dataTransfer) {
            const zoneId = zone.dataset['zone'];
            if (zoneId) {
                dragAndDropAnswers = {
                    ...dragAndDropAnswers,
                    [zoneId]: event.dataTransfer.getData('text/plain'),
                };
            }
        }
        activeZone = null;
    }

    function handleZoneClick(event: MouseEvent) {
        if (disabled) return;
        const zone = (event.target as HTMLElement).closest('.drop-zone') as HTMLElement;
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

<div class="space-y-6">
    <!-- Question canvas: same dark terminal style as MultipleChoice -->
    <div class="relative select-none overflow-hidden rounded-3xl bg-slate-900 shadow-xl" draggable="false">
        <div
            class="via-primary-500/60 absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent to-transparent"
        ></div>
        <div class="pointer-events-none absolute -top-4 -right-4 text-white/4">
            <Terminal size={120} />
        </div>

        <!-- Header bar -->
        <div class="flex items-center justify-between border-b border-white/10 px-8 py-4">
            <div class="flex items-center gap-3">
                <div class="flex gap-1.5">
                    <div class="h-2 w-2 rounded-full bg-rose-500/60"></div>
                    <div class="h-2 w-2 rounded-full bg-amber-500/60"></div>
                    <div class="h-2 w-2 rounded-full bg-emerald-500/60"></div>
                </div>
                <span
                    class="ml-1 font-mono text-xs font-bold tracking-widest text-slate-500 uppercase"
                >
                    drag-drop.txt
                </span>
            </div>
            <div
                class="flex items-center gap-1.5 text-xs font-bold tracking-widest text-slate-600 uppercase"
            >
                <MousePointer2 size={10} /> Klik kotak untuk batal
            </div>
        </div>

        <!-- Drop canvas -->
        <!-- svelte-ignore a11y_click_events_have_key_events -->
        <!-- svelte-ignore a11y_no_static_element_interactions -->
        <div
            id="drag-drop-view"
            class="relative z-10 p-8 text-xl leading-12 font-semibold text-slate-100"
            ondragover={handleDragOver}
            ondragleave={handleDragLeave}
            ondrop={handleDrop}
            onclick={handleZoneClick}
        >
            {@html parsedQuestion.text}
        </div>
    </div>

    <!-- Answer options: clean light style -->
    <div id="drag-drop-options-area" class="space-y-3">
        <div class="flex items-center gap-2 px-1">
            <List size={13} class="text-primary-500" />
            <span class="text-xs font-black tracking-widest text-slate-400 uppercase">
                Pilihan Komponen
            </span>
            <div class="ml-2 h-px flex-1 bg-slate-100"></div>
        </div>

        <div class="flex flex-wrap gap-3">
            {#each question.answers as answer (answer.id)}
                {@const isUsed = Object.values(dragAndDropAnswers).includes(
                    answer.answer_text ?? ''
                )}
                <div
                    class="press-active inline-flex items-center gap-2.5 rounded-2xl border-2 border-b-4 px-6 py-3.5 text-sm font-black transition-all duration-200 select-none
                    {isUsed
                        ? 'cursor-not-allowed border-slate-100 bg-slate-50 text-slate-300 opacity-50'
                        : 'hover:border-primary-300 hover:bg-primary-50/30 cursor-grab border-slate-200 bg-white text-slate-800 shadow-sm active:cursor-grabbing'}"
                    draggable={!isUsed}
                    role="listitem"
                    ondragstart={(e) => !isUsed && handleDragStart(e, answer.answer_text ?? '')}
                    ondragend={handleDragEnd}
                >
                    <div
                        class="h-2 w-2 rounded-full transition-all {isUsed
                            ? 'bg-slate-300'
                            : 'bg-primary-500 shadow-primary-300 shadow-sm'}"
                    ></div>
                    {answer.answer_text}
                </div>
            {/each}
        </div>
    </div>
</div>
