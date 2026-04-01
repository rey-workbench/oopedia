<script lang="ts">
    import { ChevronDown } from 'lucide-svelte';
    import type { Snippet } from 'svelte';

    interface AccordionItem {
        id: string;
        title: string;
        content?: Snippet;
        disabled?: boolean;
    }

    interface Props {
        items?: AccordionItem[];
        allowMultiple?: boolean;
        class?: string;
    }

    let { items = [], allowMultiple = false, class: className = '' }: Props = $props();

    let openItems = $state<string[]>([]);

    function toggle(id: string) {
        if (allowMultiple) {
            if (openItems.includes(id)) {
                openItems = openItems.filter((i) => i !== id);
            } else {
                openItems = [...openItems, id];
            }
        } else {
            openItems = openItems.includes(id) ? [] : [id];
        }
    }

    function isOpen(id: string) {
        return openItems.includes(id);
    }
</script>

<div class={`w-full space-y-2 ${className}`}>
    {#each items as item (item.id)}
        <div class="overflow-hidden rounded-xl border border-slate-100 bg-white">
            <button
                type="button"
                onclick={() => !item.disabled && toggle(item.id)}
                disabled={item.disabled}
                class="flex w-full items-center justify-between px-5 py-4 text-left font-bold tracking-widest uppercase transition-colors
                    {item.disabled
                    ? 'cursor-not-allowed text-slate-400'
                    : 'text-slate-700 hover:bg-slate-50'}"
                aria-expanded={isOpen(item.id)}
            >
                <span>{item.title}</span>
                <ChevronDown
                    size={18}
                    class={`text-slate-400 transition-transform ${isOpen(item.id) ? 'rotate-180' : ''}`}
                />
            </button>
            {#if isOpen(item.id)}
                <div class="border-t border-slate-100 px-5 py-4">
                    {@render item.content?.()}
                </div>
            {/if}
        </div>
    {/each}
</div>
