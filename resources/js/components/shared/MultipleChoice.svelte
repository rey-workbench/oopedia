<script lang="ts">
    import { HelpCircle, Code, CheckSquare } from 'lucide-svelte';
    import type { Question } from '@/types';

    interface Props {
        question: Question;
        selectedAnswerId?: number | null;
        onselect?: (answerId: number) => void;
    }

    let { question, selectedAnswerId = $bindable(null), onselect = () => {} }: Props = $props();

    function handleSelect(answerId: number) {
        selectedAnswerId = answerId;
        onselect(answerId);
    }
</script>

<div class="mb-8">
    <h5
        class="mb-4 flex items-center gap-2 text-sm font-bold tracking-widest text-slate-400 uppercase"
    >
        <HelpCircle size={16} class="text-primary-500" />
        Pertanyaan
    </h5>
    <div
        class="relative overflow-hidden rounded-2xl border-4 border-slate-800 bg-slate-900 p-6 font-mono text-lg leading-relaxed text-slate-100 shadow-xl"
    >
        <div class="absolute top-0 right-0 p-4 opacity-10">
            <Code size={64} />
        </div>
        <div class="relative z-10">
            {@html question.question_text}
        </div>
    </div>
</div>

<h5 class="mb-4 flex items-center gap-2 text-sm font-bold tracking-widest text-slate-400 uppercase">
    <CheckSquare size={16} class="text-primary-500" />
    Pilih Jawaban
</h5>
<div class="grid grid-cols-1 gap-4">
    {#each question.answers as answer (answer.id)}
        <label class="group relative block cursor-pointer transition-all">
            <input
                type="radio"
                name="answer"
                value={answer.id}
                class="peer hidden"
                checked={selectedAnswerId === answer.id}
                onchange={() => handleSelect(answer.id)}
            />
            <div
                class="hover:border-primary-400 hover:bg-primary-50/30 peer-checked:border-primary-600 peer-checked:bg-primary-50 peer-checked:shadow-primary-900/10 flex items-center gap-4 rounded-2xl border-2 border-slate-100 bg-white p-5 shadow-sm transition-all peer-checked:shadow-md"
            >
                <div
                    class="group-hover:border-primary-400 peer-checked:border-primary-600 peer-checked:bg-primary-600 flex h-8 w-8 shrink-0 items-center justify-center rounded-full border-2 border-slate-200 transition-all"
                >
                    <div
                        class="h-2.5 w-2.5 rounded-full bg-white opacity-0 transition-opacity peer-checked:opacity-100"
                    ></div>
                </div>
                <div
                    class="group-hover:text-primary-900 peer-checked:text-primary-900 flex-1 font-bold text-slate-700 transition-colors"
                >
                    {answer.answer_text}
                </div>
            </div>
        </label>
    {/each}
</div>
