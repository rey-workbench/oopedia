<script lang="ts">
    import { Terminal } from 'lucide-svelte';
    import Panel from '@/components/ui/Panel.svelte';
    import Input from '@/components/ui/Input.svelte';

    let {
        answerText = $bindable(),
        onInput,
    }: {
        answerText: string;
        onInput?: (val: string) => void;
    } = $props();

    function handleInput(e: Event) {
        const val = (e.target as HTMLInputElement).value;
        answerText = val;
        onInput?.(val);
    }
</script>

<div class="space-y-6">
    <Panel variant="dark" rounded="2xl" padding="p-8" class="border border-slate-800 shadow-2xl relative overflow-hidden">
        <!-- Decoration for terminal feel -->
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary-500 via-indigo-500 to-purple-500 opacity-50"></div>
        
        <div class="space-y-6">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div class="flex items-center gap-3">
                    <div class="flex gap-1.5">
                        <div class="h-2.5 w-2.5 rounded-full bg-rose-500/50"></div>
                        <div class="h-2.5 w-2.5 rounded-full bg-amber-500/50"></div>
                        <div class="h-2.5 w-2.5 rounded-full bg-emerald-500/50"></div>
                    </div>
                    <label for="fill_in_the_blank_answer" class="flex items-center gap-2 font-black tracking-widest text-primary-400 uppercase text-[10px] ml-2">
                        <Terminal size={14} class="text-primary-500" /> Input Jawaban Anda
                    </label>
                </div>
                <div class="text-[9px] font-mono text-slate-500">TTY: VIRTUAL-PROMPT</div>
            </div>
            
            <Input
                type="text"
                id="fill_in_the_blank_answer"
                placeholder="Ketik jawaban Anda di sini..."
                bind:value={answerText}
                oninput={handleInput}
                variant="dark"
                class="shadow-lg"
                inputClass="border-slate-700/50 bg-slate-800/50 focus:bg-slate-800 focus:border-primary-500 text-lg py-5 px-8"
            />

            <div class="flex items-center gap-2 text-[9px] font-bold text-slate-600 uppercase tracking-widest">
                <span class="text-primary-500/50">>_</span> Masukkan teks jawaban dengan tepat
            </div>
        </div>
    </Panel>
</div>
