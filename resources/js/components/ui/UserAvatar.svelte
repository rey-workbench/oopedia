<script lang="ts">
    interface Props {
        name?: string;
        size?: 'sm' | 'md' | 'lg';
        dark?: boolean;
        class?: string;
    }

    let { name = '', size = 'md', dark = false, class: className = '' }: Props = $props();

    const sizes = {
        sm: 'w-8 h-8 text-[10px]',
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
    class="{sizeClass} {radiusClass} {bgClass} flex shrink-0 items-center justify-center font-bold border-2 border-cosmos-border {className}"
>
    {initial}
</div>
