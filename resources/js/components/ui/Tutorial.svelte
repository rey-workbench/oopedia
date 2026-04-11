<script lang="ts">
    import { onMount, onDestroy } from 'svelte';
    import { startTutorial, destroyTutorial } from '@/utils/tutorialDriver';
    import { getTutorial } from '@/utils/tutorials';

    let { showButton = true, pageKey }: { showButton?: boolean; pageKey: string } = $props();

    let hasTutorial = $derived(!!getTutorial(pageKey));

    onMount(() => {
        if (hasTutorial) {
            startTutorial(pageKey);
        }
    });

    onDestroy(() => {
        destroyTutorial();
    });
</script>

{#if showButton && hasTutorial}
    <button
        type="button"
        onclick={() => startTutorial(pageKey)}
        class="bg-primary-600 hover:bg-primary-700 fixed right-6 bottom-6 z-50 flex items-center gap-2 rounded-full px-4 py-3 text-sm font-bold text-white shadow-lg transition-all hover:scale-105"
    >
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <circle cx="12" cy="12" r="10"></circle>
            <polygon points="10 8 16 12 10 16 10 8"></polygon>
        </svg>
        Tutorial
    </button>
{/if}
