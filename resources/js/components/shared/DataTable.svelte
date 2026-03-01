<script lang="ts">
    import type { Snippet } from "svelte";
    import Card from "@/components/ui/Card.svelte";
    import EmptyState from "@/components/ui/EmptyState.svelte";
    import { Search } from "lucide-svelte";

    interface Props {
        title?: string;
        items?: any[];
        search?: string;
        searchPlaceholder?: string;
        hideSearch?: boolean;
        columns?: {
            key: string;
            label: string;
            align?: "left" | "center" | "right" | string;
        }[];
        rowClass?: (item: any) => string;
        emptyTitle?: string;
        emptyDescription?: string;
        onsearch?: (e: Event) => void;
        row?: Snippet<[any]>;
        empty?: Snippet;
    }

    let {
        title = "Data",
        items = [],
        search = $bindable(""),
        searchPlaceholder = "Cari...",
        hideSearch = false,
        columns = [],
        rowClass = () => "",
        emptyTitle = "Data Kosong",
        emptyDescription = "Belum ada data yang tersedia.",
        onsearch = () => {},
        row,
        empty,
    }: Props = $props();
</script>

<Card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
    {#snippet header()}
        <div
            class="flex flex-col md:flex-row justify-between items-center gap-6 w-full"
        >
            <p
                class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
            >
                {title}
            </p>
            {#if !hideSearch}
                <div class="w-full md:w-auto">
                    <div class="relative group">
                        <Search
                            size={18}
                            strokeWidth={2.5}
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary-600 transition-colors"
                        />
                        <input
                            type="text"
                            bind:value={search}
                            oninput={onsearch}
                            placeholder={searchPlaceholder}
                            class="w-full md:w-64 pl-12 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-primary-100 focus:border-primary-600 transition-all outline-none"
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
                            class={`p-6 text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50 ${column.align === "center" ? "text-center" : column.align === "right" ? "text-right" : "text-left"}`}
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
                                <EmptyState
                                    title={emptyTitle}
                                    description={emptyDescription}
                                />
                            {/if}
                        </td>
                    </tr>
                {:else}
                    {#each items as item (item.id || item)}
                        <tr
                            class={`group hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0 ${rowClass(item)}`}
                        >
                            {@render row?.(item)}
                        </tr>
                    {/each}
                {/if}
            </tbody>
        </table>
    </div>
</Card>
