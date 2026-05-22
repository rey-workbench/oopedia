<script lang="ts">
    import { MoreVertical } from '@lucide/svelte';
    import { fly } from 'svelte/transition';

    export interface ActionItem {
        label: string;
        icon?: any;
        href?: string;
        onclick?: () => void;
        variant?: 'default' | 'danger';
        disabled?: boolean;
    }

    interface Props {
        items: ActionItem[];
        id?: string | undefined;
        align?: 'left' | 'right';
    }

    let { items, id = undefined, align = 'right' }: Props = $props();

    let open = $state(false);
    let menuStyles = $state('');
    let containerRef: HTMLDivElement | undefined = $state();
    let menuRef: HTMLDivElement | undefined = $state();

    function updatePosition() {
        if (!containerRef) return;
        const rect = containerRef.getBoundingClientRect();
        const estimatedHeight = items.length * 46 + 16;
        
        let style = '';
        if (rect.bottom + estimatedHeight > window.innerHeight && rect.top > estimatedHeight) {
            style += `bottom: ${window.innerHeight - rect.top + 6}px; `;
        } else {
            style += `top: ${rect.bottom + 6}px; `;
        }

        if (align === 'right') {
            style += `right: ${window.innerWidth - rect.right}px;`;
        } else {
            style += `left: ${rect.left}px;`;
        }
        menuStyles = style;
    }

    function toggle(e: MouseEvent) {
        e.stopPropagation();
        if (!open) updatePosition();
        open = !open;
    }

    function handleScroll(e: Event) {
        // Don't close if scrolling inside the menu itself
        if (menuRef && menuRef.contains(e.target as Node)) return;
        if (open) open = false;
    }

    function handleAction(item: ActionItem, e: MouseEvent) {
        e.stopPropagation();
        if (item.disabled) return;
        item.onclick?.();
        open = false;
    }

    function handleClickOutside(e: MouseEvent) {
        if (containerRef && !containerRef.contains(e.target as Node)) {
            open = false;
        }
    }

    $effect(() => {
        if (!open) return;
        document.addEventListener('click', handleClickOutside);
        document.addEventListener('scroll', handleScroll, true);
        window.addEventListener('resize', handleScroll);
        return () => {
            document.removeEventListener('click', handleClickOutside);
            document.removeEventListener('scroll', handleScroll, true);
            window.removeEventListener('resize', handleScroll);
        };
    });
</script>

<div bind:this={containerRef} class="relative inline-flex items-center" {id}>
    <button
        type="button"
        onclick={toggle}
        aria-haspopup="true"
        aria-expanded={open}
        title="Opsi"
        class={`border-cosmos-border flex h-8 w-8 items-center justify-center rounded-xl border-2 border-b-4 bg-white text-slate-400 transition-all hover:border-slate-200 hover:bg-slate-50 hover:text-slate-700 active:translate-y-[1px] active:border-b-2 ${open ? 'border-slate-200 bg-slate-50 text-slate-700' : ''}`}
    >
        <MoreVertical size={14} strokeWidth={2.5} />
    </button>

    {#if open}
        <div
            bind:this={menuRef}
            style={menuStyles}
            class="border-cosmos-border fixed z-[9999] min-w-[200px] overflow-hidden rounded-2xl border-2 bg-white shadow-2xl"
            role="menu"
            transition:fly={{ y: menuStyles.includes('bottom') ? 8 : -8, duration: 130, opacity: 0 }}
        >
            <div class="divide-y divide-slate-50">
                {#each items as item}
                    {#if item.href && !item.disabled}
                        <a
                            href={item.href}
                            role="menuitem"
                            class={`flex items-center gap-3 px-5 py-3.5 text-xs font-bold tracking-widest uppercase transition-colors ${item.variant === 'danger' ? 'text-rose-500 hover:bg-rose-50' : 'text-slate-600 hover:bg-slate-50'}`}
                        >
                            {#if item.icon}
                                <span class={item.variant === 'danger' ? 'text-rose-400' : 'text-slate-400'}>
                                    <item.icon size={13} strokeWidth={2.5} />
                                </span>
                            {/if}
                            {item.label}
                        </a>
                    {:else}
                        <button
                            type="button"
                            role="menuitem"
                            disabled={item.disabled}
                            onclick={(e) => handleAction(item, e)}
                            class={`flex w-full items-center gap-3 px-5 py-3.5 text-left text-xs font-bold tracking-widest uppercase transition-colors ${item.disabled ? 'cursor-not-allowed text-slate-300' : item.variant === 'danger' ? 'text-rose-500 hover:bg-rose-50' : 'text-slate-600 hover:bg-slate-50'}`}
                        >
                            {#if item.icon}
                                <span class={item.variant === 'danger' ? 'text-rose-400' : 'text-slate-400'}>
                                    <item.icon size={13} strokeWidth={2.5} />
                                </span>
                            {/if}
                            {item.label}
                        </button>
                    {/if}
                {/each}
            </div>
        </div>
    {/if}
</div>
