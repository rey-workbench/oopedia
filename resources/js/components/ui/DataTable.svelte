<script lang="ts">
    import type { Snippet } from 'svelte';
    import Card from '@/components/ui/Card.svelte';
    import EmptyState from '@/components/ui/EmptyState.svelte';
    import { Search } from 'lucide-svelte';

    export interface Props {
        id?: string;
        title?: string;
        items?: any[];
        search?: string;
        searchPlaceholder?: string;
        hideSearch?: boolean;
        columns?: {
            key: string;
            label: string;
            align?: 'left' | 'center' | 'right' | string;
        }[];
        rowClass?: (item: any) => string;
        emptyTitle?: string;
        emptyDescription?: string;
        onsearch?: (e: Event) => void;
        row?: Snippet<[any, number]>;
        empty?: Snippet;
        [key: string]: any;
    }

    let {
        id,
        title = 'Data',
        items = [],
        search = $bindable(''),
        searchPlaceholder = 'Cari...',
        hideSearch = false,
        columns = [],
        rowClass = () => '',
        emptyTitle = 'Data Kosong',
        emptyDescription = 'Belum ada data yang tersedia.',
        onsearch = () => {},
        row,
        empty,
        ...rest
    }: Props = $props();
</script>

<Card {id} padding="p-0" class="overflow-hidden" {...rest}>
    {#snippet header()}
        <div class="flex w-full flex-col items-center justify-between gap-6 md:flex-row">
            <p class="text-xs font-bold tracking-widest text-slate-400 uppercase">
                {title}
            </p>
            {#if !hideSearch}
                <div class="w-full md:w-auto">
                    <div class="group relative">
                        <Search
                            size={18}
                            strokeWidth={2.5}
                            class="group-focus-within:text-primary-600 absolute top-1/2 left-4 -translate-y-1/2 text-slate-400 transition-colors"
                        />
                        <input
                            type="text"
                            bind:value={search}
                            oninput={onsearch}
                            placeholder={searchPlaceholder}
                            class="border-cosmos-border focus:border-primary-500 focus:ring-primary-100 w-full rounded-2xl border-2 bg-white py-2.5 pr-4 pl-12 text-sm font-bold transition-all outline-none focus:ring-4 md:w-64"
                        />
                    </div>
                </div>
            {/if}
        </div>
    {/snippet}

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr>
                    {#each columns as column}
                        <th
                            class={`border-cosmos-border border-b-2 bg-slate-50/30 p-6 text-xs font-extrabold tracking-widest text-slate-400 uppercase ${column.align === 'center' ? 'text-center' : column.align === 'right' ? 'text-right' : 'text-left'}`}
                        >
                            {column.label}
                        </th>
                    {/each}
                </tr>
            </thead>
            <tbody>
                {#if items.length === 0}
                    <tr>
                        <td colspan={columns.length} class="p-0">
                            {#if empty}
                                {@render empty()}
                            {:else}
                                <EmptyState title={emptyTitle} description={emptyDescription} />
                            {/if}
                        </td>
                    </tr>
                {:else}
                    {#each items as item, i (item.id || item)}
                        <tr
                            class={`group border-cosmos-border hover:bg-primary-50/30 border-b-2 transition-colors last:border-0 ${rowClass(item)}`}
                        >
                            {@render row?.(item, i)}
                        </tr>
                    {/each}
                {/if}
            </tbody>
        </table>
    </div>
</Card>
