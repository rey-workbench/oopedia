<script>
    import { createEventDispatcher } from "svelte";
    import { HelpCircle, List, ArrowRight } from "lucide-svelte";

    export let question;
    export let dragAndDropAnswers = {};

    const dispatch = createEventDispatcher();

    let formattedQuestionText = "";
    let hasInlineZones = false;
    let autoDropZones = [];

    // Parse [zone] tags in question text
    $: {
        if (question && question.question_text) {
            let zoneIndex = 0;
            hasInlineZones = false;
            let rawText = question.question_text
                .replace(/<p>/g, "")
                .replace(/<\/p>/g, "\n")
                .replace(/<br>/g, "\n");

            formattedQuestionText = rawText.replace(/\[zone\]/g, () => {
                hasInlineZones = true;
                zoneIndex++;
                const currentZoneId = zoneIndex;
                const currentAnswer =
                    dragAndDropAnswers[currentZoneId] || "...";

                return `<span class="drop-zone inline-block min-w-[120px] h-9 border-b-2 border-primary-300 mx-1 align-middle text-center text-primary-600 font-bold bg-white rounded-md px-3 shadow-sm transition-all cursor-pointer hover:bg-primary-50" data-zone="${currentZoneId}">${currentAnswer}</span>`;
            });

            // If no inline zones found, generate auto zones based on number of correct items (targets)
            if (!hasInlineZones) {
                // Ensure question.answers exists and is an array
                const zoneCount =
                    question.answers && Array.isArray(question.answers)
                        ? question.answers.length
                        : 0;
                autoDropZones = Array.from(
                    { length: zoneCount },
                    (_, i) => i + 1,
                );
            } else {
                autoDropZones = [];
            }
        }
    }

    function handleDragStart(event, answerText) {
        event.dataTransfer.setData("text/plain", answerText);
        event.dataTransfer.effectAllowed = "move";
    }

    function handleDragOver(event) {
        event.preventDefault();
        event.dataTransfer.dropEffect = "move";
        const zone = event.target.closest(".drop-zone");
        if (zone) {
            zone.classList.add(
                "bg-primary-100",
                "border-primary-600",
                "scale-105",
            );
        }
    }

    function handleDragLeave(event) {
        const zone = event.target.closest(".drop-zone");
        if (zone) {
            zone.classList.remove(
                "bg-primary-100",
                "border-primary-600",
                "scale-105",
            );
        }
    }

    function handleDrop(event) {
        event.preventDefault();
        const zone = event.target.closest(".drop-zone");
        if (zone) {
            zone.classList.remove(
                "bg-primary-100",
                "border-primary-600",
                "scale-105",
            );
            const answerText = event.dataTransfer.getData("text/plain");
            const zoneId = zone.dataset.zone;

            // Update state
            dragAndDropAnswers = {
                ...dragAndDropAnswers,
                [zoneId]: answerText,
            };

            dispatch("update", { answers: dragAndDropAnswers });
        }
    }

    function handleZoneClick(event) {
        const zone = event.target.closest(".drop-zone");
        if (zone) {
            const zoneId = zone.dataset.zone;
            if (dragAndDropAnswers[zoneId]) {
                const newAnswers = { ...dragAndDropAnswers };
                delete newAnswers[zoneId];
                dragAndDropAnswers = newAnswers;
                dispatch("update", { answers: dragAndDropAnswers });
            }
        }
    }
</script>

<div class="question-content space-y-8">
    <div class="space-y-4">
        <h5
            class="text-sm font-bold uppercase tracking-widest text-slate-400 flex items-center"
        >
            <HelpCircle size={14} class="mr-2" /> Pertanyaan
        </h5>

        <div
            class="question-html font-medium text-slate-800 bg-slate-50 p-6 rounded-2xl border border-slate-200 leading-loose"
            on:dragover={handleDragOver}
            on:dragleave={handleDragLeave}
            on:drop={handleDrop}
            on:click={handleZoneClick}
        >
            {@html formattedQuestionText}

            {#if !hasInlineZones && autoDropZones.length > 0}
                <div class="mt-8 space-y-3">
                    <p
                        class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter mb-2"
                    >
                        Urutan Jawaban:
                    </p>
                    {#each autoDropZones as zoneId}
                        <div class="flex items-center gap-4">
                            <div
                                class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-black text-slate-500"
                            >
                                {zoneId}
                            </div>
                            <div
                                class="drop-zone flex-1 min-h-[50px] bg-white border-2 border-dashed border-slate-200 rounded-xl flex items-center px-4 text-primary-600 font-bold shadow-inner transition-all cursor-pointer hover:border-primary-300"
                                data-zone={zoneId}
                            >
                                {#if dragAndDropAnswers[zoneId]}
                                    <div
                                        class="flex items-center justify-between w-full"
                                    >
                                        <span>{dragAndDropAnswers[zoneId]}</span
                                        >
                                        <span
                                            class="text-[10px] text-slate-300 italic"
                                            >Klik untuk hapus</span
                                        >
                                    </div>
                                {:else}
                                    <span
                                        class="text-slate-300 font-medium text-sm italic"
                                        >Letakkan jawaban di sini...</span
                                    >
                                {/if}
                            </div>
                        </div>
                    {/each}
                </div>
            {/if}
        </div>
    </div>

    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h5
                class="text-sm font-bold uppercase tracking-widest text-slate-400 flex items-center"
            >
                <List size={14} class="mr-2" /> Pilihan Jawaban
            </h5>
            <span class="text-[10px] font-medium text-slate-400 italic"
                >Geser item ke area di atas</span
            >
        </div>

        <div class="drag-items flex flex-wrap gap-3">
            {#each question.answers as answer (answer.id)}
                {@const isUsed = Object.values(dragAndDropAnswers).includes(
                    answer.answer_text,
                )}
                <div
                    class="draggable px-5 py-3 rounded-xl font-bold text-sm transition-all shadow-sm select-none border-2 flex items-center gap-2
                    {isUsed
                        ? 'bg-slate-50 border-slate-100 text-slate-300 cursor-not-allowed grayscale'
                        : 'bg-white border-primary-100 text-primary-600 cursor-grab active:cursor-grabbing hover:border-primary-500 hover:shadow-md active:scale-95'}"
                    draggable={!isUsed}
                    on:dragstart={(e) =>
                        !isUsed && handleDragStart(e, answer.answer_text)}
                >
                    {#if !isUsed}
                        <div
                            class="w-1.5 h-1.5 rounded-full bg-primary-500"
                        ></div>
                    {/if}
                    {answer.answer_text}
                </div>
            {/each}
        </div>
    </div>
</div>

<style>
    .drop-zone {
        transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
</style>
