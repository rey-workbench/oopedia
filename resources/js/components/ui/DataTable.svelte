<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import type { Snippet } from 'svelte';
    import Card from '@/components/ui/Card.svelte';
    import EmptyState from '@/components/ui/EmptyState.svelte';
    import { Search, ChevronLeft, ChevronRight } from 'lucide-svelte';

    export interface Props {
        id?: string;
        title?: string;
        items?: any[];
        links?: any[]; // New prop for server-side pagination links
        search?: string;
        searchPlaceholder?: string;
        hideSearch?: boolean;
        hidePagination?: boolean;
        itemsPerPage?: number;
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
        links = [], // Default to empty
        search = $bindable(''),
        searchPlaceholder = 'Cari...',
        hideSearch = false,
        hidePagination = false,
        itemsPerPage = 10,
        columns = [],
        rowClass = () => '',
        emptyTitle = 'Data Kosong',
        emptyDescription = 'Belum ada data yang tersedia.',
        onsearch = () => {},
        row,
        empty,
        ...rest
    }: Props = $props();

    // Internal pagination state for client-side
    let currentPage = $state(1);

    // Check if we are using server-side pagination
    const isServerSide = $derived(links && links.length > 3);

    // Derived values for client-side pagination
    const totalPages = $derived(Math.ceil((items?.length ?? 0) / itemsPerPage));
    const paginatedItems = $derived(
        hidePagination || isServerSide
            ? items
            : (items ?? []).slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage)
    );

    // Reset to page 1 when items change (e.g. filter)
    $effect(() => {
        items; // touch for dependency
        if (!isServerSide) {
            currentPage = 1;
        }
    });
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
                {#if (paginatedItems?.length ?? 0) === 0}
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
                    {#each paginatedItems as item, i (item?.id ?? i)}
                        <tr
                            class={`group border-cosmos-border hover:bg-primary-50/30 border-b-2 transition-colors last:border-0 ${rowClass(item)}`}
                        >
                            {@render row?.(item, (currentPage - 1) * itemsPerPage + i)}
                        </tr>
                    {/each}
                {/if}
            </tbody>
        </table>
    </div>

    {#if !hidePagination && (isServerSide || totalPages > 1)}
        <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50/30 p-6">
            {#if isServerSide}
                <!-- Server-side Pagination Layout -->
                <div class="flex w-full items-center justify-center gap-2">
                    {#each links as link}
                        {#if link.url}
                            <Link
                                href={link.url}
                                class="rounded-xl border-2 px-4 py-2 text-xs font-black transition-all active:translate-y-[2px] active:border-b-2 {link.active
                                    ? 'border-b-4 border-slate-900 bg-slate-900 text-white'
                                    : 'border-cosmos-border border-b-4 bg-white text-slate-400 hover:text-slate-900'}"
                            >
                                {@html link.label}
                            </Link>
                        {:else}
                            <span
                                class="cursor-not-allowed rounded-xl border border-slate-100 bg-white/50 px-4 py-2 text-xs font-black text-slate-300"
                            >
                                {@html link.label}
                            </span>
                        {/if}
                    {/each}
                </div>
            {:else}
                <!-- Client-side Pagination Layout -->
                <div class="text-[10px] font-black tracking-widest text-slate-400 uppercase">
                    Menampilkan {(currentPage - 1) * itemsPerPage + 1} - {Math.min(
                        currentPage * itemsPerPage,
                        items?.length ?? 0
                    )} dari {items?.length ?? 0} data
                </div>
                <div class="flex items-center gap-2">
                    <button
                        onclick={() => (currentPage = Math.max(1, currentPage - 1))}
                        disabled={currentPage === 1}
                        class="border-duo hover:text-primary-500 flex h-10 w-10 items-center justify-center rounded-xl bg-white text-slate-400 transition-all active:translate-y-0.5 active:border-b-2 disabled:cursor-not-allowed disabled:opacity-30"
                    >
                        <ChevronLeft size={20} />
                    </button>

                    <div
                        class="bg-primary-500 shadow-primary-500/20 flex h-10 items-center justify-center rounded-xl px-4 text-xs font-black text-white shadow-lg"
                    >
                        {currentPage} / {totalPages}
                    </div>

                    <button
                        onclick={() => (currentPage = Math.min(totalPages, currentPage + 1))}
                        disabled={currentPage === totalPages}
                        class="border-duo hover:text-primary-500 flex h-10 w-10 items-center justify-center rounded-xl bg-white text-slate-400 transition-all active:translate-y-0.5 active:border-b-2 disabled:cursor-not-allowed disabled:opacity-30"
                    >
                        <ChevronRight size={20} />
                    </button>
                </div>
            {/if}
        </div>
    {/if}
</Card>
