<script lang="ts">
    import type { Snippet } from "svelte";

    interface Props {
        class?: string;
        children?: Snippet;
    }

    let { class: className = "", children }: Props = $props();
</script>

<div
    class="relative overflow-hidden rounded-[3rem] bg-slate-900 text-white {className}"
>
    <div
        class="absolute top-0 right-0 w-1/2 h-full bg-primary-600/10 pointer-events-none"
    ></div>
    <div
        class="absolute -bottom-24 -right-24 w-96 h-96 bg-primary-500/10 rounded-full blur-[100px] pointer-events-none"
    ></div>

    <div class="relative z-10">
        {@render children?.()}
    </div>
</div>
