<script lang="ts">
    import { slide, fade } from 'svelte/transition';
    import { Lightbulb } from 'lucide-svelte';
    import type { QuizState } from '@/states/Mahasiswa/QuizState.svelte';
    import type { Snippet } from 'svelte';

    interface Props {
        state: QuizState;
        children: Snippet;
    }

    let { state, children }: Props = $props();

    const currentQuestion = $derived(state.currentQuestion);
</script>

<div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl">
    <!-- Question Content -->
    <div class="p-6 md:p-8">
        {#if currentQuestion}
            <div class="space-y-6" in:fade={{ duration: 400 }}>
                <!-- Hint Panel -->
                {#if state.showHint && currentQuestion.hint}
                    <div
                        transition:slide
                        class="rounded-2xl border-2 border-amber-200 bg-amber-50 p-5 shadow-sm"
                    >
                        <div class="mb-2 flex items-center gap-2 font-black text-amber-700">
                            <Lightbulb size={20} class="animate-pulse" />
                            <span class="text-sm tracking-wider uppercase">Petunjuk</span>
                        </div>
                        <p class="text-sm leading-relaxed font-medium text-amber-800/90">
                            {currentQuestion.hint}
                        </p>
                    </div>
                {/if}

                <!-- Question Type Content (injected by parent) -->
                <div class="mt-4">
                    {@render children()}
                </div>
            </div>
        {/if}
    </div>
</div>
