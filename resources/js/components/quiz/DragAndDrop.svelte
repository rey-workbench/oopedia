<script>
    import { createEventDispatcher, onMount } from "svelte";
    import { HelpCircle, List } from "lucide-svelte";

    export let question;
    export let dragAndDropAnswers = {};

    const dispatch = createEventDispatcher();

    let formattedQuestionText = "";
    let dropZones = [];

    // Parse [zone] tags in question text
    $: {
        if (question && question.question_text) {
            let zoneIndex = 0;
            let rawText = question.question_text
                .replace(/<p>/g, "")
                .replace(/<\/p>/g, "\n")
                .replace(/<br>/g, "\n");

            formattedQuestionText = rawText.replace(/\[zone\]/g, () => {
                zoneIndex++;
                const currentZoneId = zoneIndex;
                const currentAnswer = dragAndDropAnswers[currentZoneId] || "";

                // Return placeholder for Svelte to hydrate or handle via raw HTML replacement
                // Note: Svelte @html doesn't easily bind events to inner HTML elements.
                // We might need a different approach or manual event delegation.
                // For simplicity, we'll use a data-attribute approach and delegate events on the container.
                return `<span class="drop-zone inline-block min-w-[80px] h-8 border-b-2 border-slate-400 mx-1 align-middle text-center text-blue-600 font-bold bg-slate-100 rounded px-2" data-zone="${currentZoneId}">${currentAnswer}</span>`;
            });

            // Update dropZones based on count
            dropZones = Array.from({ length: zoneIndex }, (_, i) => i + 1);
        }
    }

    function handleDragStart(event, answerText) {
        event.dataTransfer.setData("text/plain", answerText);
        event.dataTransfer.effectAllowed = "copy";
    }

    function handleDragOver(event) {
        event.preventDefault();
        event.dataTransfer.dropEffect = "copy";
        if (event.target.classList.contains("drop-zone")) {
            event.target.classList.add("bg-blue-100", "border-blue-500");
        }
    }

    function handleDragLeave(event) {
        if (event.target.classList.contains("drop-zone")) {
            event.target.classList.remove("bg-blue-100", "border-blue-500");
        }
    }

    function handleDrop(event) {
        event.preventDefault();
        if (event.target.classList.contains("drop-zone")) {
            event.target.classList.remove("bg-blue-100", "border-blue-500");
            const answerText = event.dataTransfer.getData("text/plain");
            const zoneId = event.target.dataset.zone;

            // Update state
            dragAndDropAnswers = {
                ...dragAndDropAnswers,
                [zoneId]: answerText,
            };

            // Dispatch update
            dispatch("update", { answers: dragAndDropAnswers });
        }
    }

    // For clearing a zone on click (optional user experience improvement)
    function handleZoneClick(event) {
        if (event.target.classList.contains("drop-zone")) {
            const zoneId = event.target.dataset.zone;
            if (dragAndDropAnswers[zoneId]) {
                const newAnswers = { ...dragAndDropAnswers };
                delete newAnswers[zoneId];
                dragAndDropAnswers = newAnswers;
                dispatch("update", { answers: dragAndDropAnswers });
            }
        }
    }
</script>

<div class="question-content">
    <h5 class="mb-2 font-semibold flex items-center">
        <HelpCircle size={18} class="mr-2" />Pertanyaan
    </h5>

    <!-- Container for the question text with drop zones -->
    <!-- svelte-ignore a11y-click-events-have-key-events -->
    <!-- svelte-ignore a11y-no-static-element-interactions -->
    <div
        class="question-html font-mono bg-slate-50 p-4 rounded-lg border border-slate-200 whitespace-pre-wrap leading-loose"
        on:dragover={handleDragOver}
        on:dragleave={handleDragLeave}
        on:drop={handleDrop}
        on:click={handleZoneClick}
    >
        {@html formattedQuestionText}
    </div>

    <input
        type="hidden"
        name="drag_and_drop_answers"
        value={JSON.stringify(dragAndDropAnswers)}
    />

    <h5
        class="mt-6 mb-3 text-lg font-semibold text-slate-800 flex items-center"
    >
        <List size={20} class="mr-2" />Pilihan Jawaban
    </h5>
    <p class="text-xs text-slate-500 mb-2">
        Geser jawaban ke bagian kosong yang sesuai. Klik pada jawaban yang sudah
        terisi untuk menghapus.
    </p>

    <div class="drag-items flex flex-wrap gap-3 mt-2">
        {#each question.answers as answer (answer.id)}
            <div
                class="draggable px-4 py-2 bg-white border-2 border-blue-200 text-blue-600 rounded-lg font-medium cursor-grab active:cursor-grabbing hover:bg-blue-50 hover:border-blue-400 transition-all shadow-sm select-none"
                draggable="true"
                on:dragstart={(e) => handleDragStart(e, answer.answer_text)}
            >
                {answer.answer_text}
            </div>
        {/each}
    </div>
</div>
