<script lang="ts">
    import { onMount } from 'svelte';
    import { tweened } from 'svelte/motion';
    import { cubicOut } from 'svelte/easing';

    interface Props {
        speed?: number;
        direction?: 'up' | 'down' | 'left' | 'right';
        class?: string;
    }

    let {
        speed = 0.5,
        direction = 'up',
        class: className = '',
        children,
    }: Props & { children?: any } = $props();

    let element: HTMLElement;
    let translateY = tweened(0, { duration: 1000, easing: cubicOut });
    let translateX = tweened(0, { duration: 1000, easing: cubicOut });

    onMount(() => {
        const handleScroll = () => {
            const rect = element.getBoundingClientRect();
            const windowHeight = window.innerHeight;
            const elementCenter = rect.top + rect.height / 2;
            const viewportCenter = windowHeight / 2;
            const distance = elementCenter - viewportCenter;

            if (direction === 'up' || direction === 'down') {
                translateY.set(distance * speed * 0.1);
            } else {
                translateX.set(distance * speed * 0.1);
            }
        };

        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll();

        return () => window.removeEventListener('scroll', handleScroll);
    });
</script>

<div
    bind:this={element}
    class={className}
    style="transform: translateY({$translateY}px) translateX({$translateX}px);"
>
    {@render children?.()}
</div>
