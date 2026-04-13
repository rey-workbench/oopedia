<script lang="ts">
    interface Props {
        variant?: 'text' | 'circular' | 'rectangular' | 'rounded';
        width?: string;
        height?: string;
        class?: string;
    }

    let {
        variant = 'text',
        width = '100%',
        height = '1rem',
        class: className = '',
    }: Props = $props();

    const variantClasses = $derived({
        text: 'rounded',
        circular: 'rounded-full',
        rectangular: 'rounded-none',
        rounded: 'rounded-xl',
    });
</script>

<div
    class="animate-pulse bg-slate-200 {variantClasses[variant]} {className}"
    style="width: {width}; height: {height};"
></div>
