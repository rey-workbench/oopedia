<script lang="ts">
    import Input from '@/components/ui/Input.svelte';
    import { HelpCircle } from 'lucide-svelte';
    import type { Question } from '@/types';

    interface Props {
        question: Question;
        answerText?: string;
        oninput?: (text: string) => void;
    }

    let { question, answerText = $bindable(''), oninput = () => {} }: Props = $props();

    function handleInput(event: Event) {
        const text = (event.target as HTMLInputElement).value;
        answerText = text;
        oninput(text);
    }
</script>

<div class="mb-6">
    <h5 class="mb-3 flex items-center text-lg font-semibold">
        <HelpCircle size={18} class="mr-2" />Pertanyaan
    </h5>
    <div class="rounded-lg bg-slate-50 p-4 font-medium whitespace-pre-wrap text-slate-700">
        {@html question.question_text}
    </div>
</div>

<div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 p-4">
    <label for="fill_in_the_blank_answer" class="mb-2 block font-medium text-slate-700"
        >Jawaban Anda:</label
    >
    <Input
        type="text"
        id="fill_in_the_blank_answer"
        placeholder="Ketik jawaban Anda di sini..."
        value={answerText}
        oninput={handleInput}
    />
</div>
