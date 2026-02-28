<script>
    import { HelpCircle, Code, CheckSquare } from "lucide-svelte";

    let {
        question,
        selectedAnswerId = $bindable(null),
        onselect = (answerId) => {},
    } = $props();

    function handleSelect(answerId) {
        selectedAnswerId = answerId;
        onselect(answerId);
    }
</script>

<div class="mb-8">
    <h5
        class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2"
    >
        <HelpCircle size={16} class="text-primary-500" />
        Pertanyaan
    </h5>
    <div
        class="p-6 bg-slate-900 rounded-2xl shadow-xl border-4 border-slate-800 text-slate-100 font-mono text-lg leading-relaxed relative overflow-hidden"
    >
        <div class="absolute top-0 right-0 p-4 opacity-10">
            <Code size={64} />
        </div>
        <div class="relative z-10">
            {@html question.question_text}
        </div>
    </div>
</div>

<h5
    class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2"
>
    <CheckSquare size={16} class="text-primary-500" />
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
                onchange={() => handleSelect(answer.id)}
            />
            <div
                class="p-5 rounded-2xl border-2 border-slate-100 bg-white shadow-sm hover:border-primary-400 hover:bg-primary-50/30 peer-checked:border-primary-600 peer-checked:bg-primary-50 peer-checked:shadow-md peer-checked:shadow-primary-900/10 flex items-center gap-4 transition-all"
            >
                <div
                    class="w-8 h-8 rounded-full border-2 border-slate-200 group-hover:border-primary-400 peer-checked:border-primary-600 peer-checked:bg-primary-600 flex items-center justify-center shrink-0 transition-all"
                >
                    <div
                        class="w-2.5 h-2.5 rounded-full bg-white opacity-0 peer-checked:opacity-100 transition-opacity"
                    ></div>
                </div>
                <div
                    class="flex-1 text-slate-700 font-bold group-hover:text-primary-900 peer-checked:text-primary-900 transition-colors"
                >
                    {answer.answer_text}
                </div>
            </div>
        </label>
    {/each}
</div>
