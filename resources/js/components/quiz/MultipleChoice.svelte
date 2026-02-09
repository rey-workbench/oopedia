<script>
    import { createEventDispatcher } from "svelte";

    export let question;
    export let selectedAnswerId = null;

    const dispatch = createEventDispatcher();

    function handleSelect(answerId) {
        selectedAnswerId = answerId;
        dispatch("select", { answerId });
    }
</script>

<div class="mb-8">
    <h5
        class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2"
    >
        <i class="fas fa-question-circle text-blue-500"></i>
        Pertanyaan
    </h5>
    <div
        class="p-6 bg-slate-900 rounded-2xl shadow-xl border-4 border-slate-800 text-slate-100 font-mono text-lg leading-relaxed relative overflow-hidden"
    >
        <div class="absolute top-0 right-0 p-4 opacity-10">
            <i class="fas fa-code text-6xl"></i>
        </div>
        <div class="relative z-10">
            {@html question.question_text}
        </div>
    </div>
</div>

<h5
    class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2"
>
    <i class="fas fa-tasks text-indigo-500"></i>
    Pilih Jawaban
</h5>
<div class="grid grid-cols-1 gap-4">
    {#each question.answers as answer (answer.id)}
        <label class="group relative block transition-all cursor-pointer">
            <input
                type="radio"
                name="answer"
                value={answer.id}
                class="peer hidden"
                checked={selectedAnswerId === answer.id}
                on:change={() => handleSelect(answer.id)}
            />
            <div
                class="p-5 rounded-2xl border-2 border-slate-100 bg-white shadow-sm hover:border-blue-400 hover:bg-blue-50/30 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:shadow-md peer-checked:shadow-blue-100 flex items-center gap-4 transition-all"
            >
                <div
                    class="w-8 h-8 rounded-full border-2 border-slate-200 group-hover:border-blue-400 peer-checked:border-blue-600 peer-checked:bg-blue-600 flex items-center justify-center shrink-0 transition-all"
                >
                    <div
                        class="w-2.5 h-2.5 rounded-full bg-white opacity-0 peer-checked:opacity-100 transition-opacity"
                    ></div>
                </div>
                <div
                    class="flex-1 text-slate-700 font-bold group-hover:text-blue-900 peer-checked:text-blue-900 transition-colors"
                >
                    {answer.answer_text}
                </div>
            </div>
        </label>
    {/each}
</div>
