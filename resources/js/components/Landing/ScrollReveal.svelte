<script lang="ts">
    import { onMount } from 'svelte';
    import { tweened } from 'svelte/motion';
    import { cubicOut, elasticOut } from 'svelte/easing';

    interface Props {
        delay?: number;
        duration?: number;
        y?: number;
        scale?: number;
        blur?: boolean;
        stagger?: boolean;
        class?: string;
    }

    let {
        delay = 0,
        duration = 800,
        y = 60,
        scale = 0.95,
        blur = false,
        stagger = false,
        class: className = '',
        children,
    }: Props & { children?: any } = $props();

    let element: HTMLElement;
    let visible = $state(false);

    const opacity = tweened(0, { duration, easing: cubicOut });
    const translateY = tweened(y, { duration, easing: cubicOut });
    const scaleValue = tweened(scale, { duration: duration * 1.2, easing: elasticOut });
    const blurValue = tweened(20, { duration, easing: cubicOut });

    onMount(() => {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        visible = true;
                        setTimeout(() => {
                            opacity.set(1);
                            translateY.set(0);
                            scaleValue.set(1);
                            blurValue.set(0);
                        }, delay);
                    }
                });
            },
            { threshold: 0.1, rootMargin: '0px 0px -50px 0px' }
        );

        observer.observe(element);

        return () => observer.disconnect();
    });

    let blurStyle = $derived(blur ? `filter: blur(${$blurValue}px);` : '');
</script>

<div
    bind:this={element}
    class={className}
    style="opacity: {$opacity}; transform: translateY({$translateY}px) scale({$scaleValue}); {blurStyle};"
>
    {@render children?.()}
</div>
