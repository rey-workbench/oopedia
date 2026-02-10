<script>
    import { createEventDispatcher } from "svelte";
    import Input from "../ui/Input.svelte";
    import { HelpCircle } from "lucide-svelte";

    export let question;
    export let answerText = "";

    const dispatch = createEventDispatcher();

    function handleInput(event) {
        answerText = event.target.value;
        dispatch("input", { text: answerText });
    }
</script>

<div class="mb-6">
    <h5 class="text-lg font-semibold mb-3 flex items-center">
        <HelpCircle size={18} class="mr-2" />Pertanyaan
    </h5>
    <div
        class="whitespace-pre-wrap bg-slate-50 p-4 rounded-lg font-medium text-slate-700"
    >
        {@html question.question_text}
    </div>
</div>

<div class="p-4 mb-4 rounded-lg bg-blue-50 border border-blue-200">
    <label
        for="fill_in_the_blank_answer"
        class="block font-medium mb-2 text-slate-700">Jawaban Anda:</label
    >
    <Input
        type="text"
        id="fill_in_the_blank_answer"
        placeholder="Ketik jawaban Anda di sini..."
        value={answerText}
        on:input={handleInput}
    />
</div>
