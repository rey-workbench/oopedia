<script lang="ts">
    interface Props {
        name?: string;
        src?: string | null;
        size?: 'sm' | 'md' | 'lg';
        dark?: boolean;
        class?: string;
    }

    let { name = '', src = null, size = 'md', dark = false, class: className = '' }: Props = $props();

    const sizes = {
        sm: 'w-8 h-8 text-xs',
        md: 'w-10 h-10 text-xs',
        lg: 'w-20 h-20 text-2xl',
    };

    const radius = {
        sm: 'rounded-lg',
        md: 'rounded-xl',
        lg: 'rounded-[1.5rem]',
    };

    const initial = $derived(name ? name.charAt(0).toUpperCase() : '?');
    const sizeClass = $derived(sizes[size] || sizes.md);
    const radiusClass = $derived(radius[size] || radius.md);
    const bgClass = $derived(dark ? 'bg-white/10 text-white' : 'bg-slate-900 text-white');
</script>

<div
    class="{sizeClass} {radiusClass} {bgClass} border-cosmos-border overflow-hidden flex shrink-0 items-center justify-center border-2 font-bold {className}"
>
    {#if src}
        <img src={src} alt={name || 'Avatar'} class="h-full w-full object-cover" />
    {:else}
        {initial}
    {/if}
</div>
