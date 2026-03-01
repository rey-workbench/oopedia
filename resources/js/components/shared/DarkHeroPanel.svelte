<script lang="ts">
    import type { Snippet } from 'svelte';

    interface Props {
        class?: string;
        children?: Snippet;
    }

    let { class: className = '', children }: Props = $props();
</script>

<div class="relative overflow-hidden rounded-[3rem] bg-slate-900 text-white {className}">
    <div class="bg-primary-600/10 pointer-events-none absolute top-0 right-0 h-full w-1/2"></div>
    <div
        class="bg-primary-500/10 pointer-events-none absolute -right-24 -bottom-24 h-96 w-96 rounded-full blur-[100px]"
    ></div>

    <div class="relative z-10">
        {@render children?.()}
    </div>
</div>
