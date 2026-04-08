<script lang="ts">
    import { Link } from '@inertiajs/svelte';

    interface Props {
        links?: any[];
        class?: string;
    }

    let { links = [], class: className = '' }: Props = $props();
</script>

{#if links && links.length > 3}
    <div class="flex justify-center gap-2 border-t border-slate-50 bg-slate-50/50 p-6 {className}">
        {#each links as link}
            {#if link.url}
                <Link
                    href={link.url}
                    class="rounded-2xl px-4 py-2 text-[10px] font-bold transition-all border-2 active:translate-y-[2px] active:border-b-2 {link.active
                        ? 'bg-slate-900 text-white border-black border-b-4'
                        : 'border-cosmos-border border-b-4 bg-white text-slate-400 hover:text-slate-900'}"
                >
                    {@html link.label}
                </Link>
            {:else}
                <span
                    class="cursor-not-allowed rounded-xl border border-slate-50 bg-white px-4 py-2 text-[10px] font-bold text-slate-200"
                >
                    {@html link.label}
                </span>
            {/if}
        {/each}
    </div>
{/if}
