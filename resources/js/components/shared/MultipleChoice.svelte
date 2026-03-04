<script lang="ts">
    import { HelpCircle, CheckSquare, Terminal } from 'lucide-svelte';
    import Panel from '@/components/ui/Panel.svelte';
    import Card from '@/components/ui/Card.svelte';
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

<div class="space-y-8">
    <div class="space-y-4">
        <label
            class="flex items-center gap-2 text-[10px] font-bold tracking-widest text-slate-400 uppercase"
        >
            <HelpCircle size={14} class="text-primary-500" />
            Blok Kode Pertanyaan
        </label>
        
        <Panel variant="none" rounded="3xl" padding="p-8" class="relative overflow-hidden border-2 border-slate-800 bg-slate-900 shadow-2xl">
            <div class="absolute -top-6 -right-6 text-white/5 rotate-12">
                <Terminal size={120} />
            </div>
            <div class="relative z-10 font-mono text-lg leading-relaxed text-slate-100 selection:bg-primary-500/30">
                {@html question.question_text}
            </div>
        </Panel>
    </div>

    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-[10px] font-bold tracking-widest text-slate-400 uppercase">
                <CheckSquare size={14} class="text-primary-500" />
                Pilih Jawaban Yang Tepat
            </label>
            <div class="h-px flex-1 ml-4 bg-slate-100"></div>
        </div>

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
                    <Card 
                        variant="none" 
                        padding="p-0" 
                        class="overflow-hidden border-2 border-slate-100 bg-white transition-all duration-300
                                group-hover:border-primary-200 group-hover:bg-slate-50/50 
                                peer-checked:border-primary-600 peer-checked:bg-primary-50 peer-checked:shadow-lg peer-checked:ring-4 peer-checked:ring-primary-50/50"
                    >
                        <div class="flex items-center gap-5 p-6">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl border-2 border-slate-100 transition-all duration-300
                                       group-hover:border-primary-300 
                                       peer-checked:border-primary-600 peer-checked:bg-primary-600 peer-checked:rotate-12 peer-checked:scale-110 shadow-sm"
                            >
                                <div
                                    class="h-2.5 w-2.5 rounded-full bg-white opacity-0 transition-opacity peer-checked:opacity-100"
                                ></div>
                            </div>
                            <div
                                class="flex-1 font-black text-slate-700 transition-colors duration-300 
                                       group-hover:text-slate-900 
                                       peer-checked:text-primary-950 text-base tracking-tight"
                            >
                                {answer.answer_text}
                            </div>
                        </div>
                    </Card>
                </label>
            {/each}
        </div>
    </div>
</div>
