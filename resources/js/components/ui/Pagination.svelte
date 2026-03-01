<script lang="ts">
    import { Link } from "@inertiajs/svelte";

    interface Props {
        links?: any[];
        class?: string;
    }

    let { links = [], class: className = "" }: Props = $props();
</script>

{#if links && links.length > 3}
    <div
        class="p-6 bg-slate-50/50 border-t border-slate-50 flex justify-center gap-2 {className}"
    >
        {#each links as link}
            {#if link.url}
                <Link
                    href={link.url}
                    class="px-4 py-2 rounded-xl text-[10px] font-bold transition-all {link.active
                        ? 'bg-slate-900 text-white shadow-lg'
                        : 'bg-white text-slate-400 hover:text-slate-900 border border-slate-100'}"
                >
                    {@html link.label}
                </Link>
            {:else}
                <span
                    class="px-4 py-2 rounded-xl text-[10px] font-bold bg-white text-slate-200 border border-slate-50 cursor-not-allowed"
                >
                    {@html link.label}
                </span>
            {/if}
        {/each}
    </div>
{/if}
