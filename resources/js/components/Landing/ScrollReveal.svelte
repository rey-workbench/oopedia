<script lang="ts">
    import { onMount } from 'svelte';
    import { Tween } from 'svelte/motion';
    import { cubicOut, elasticOut } from 'svelte/easing';

    interface Props {
        delay?: number;
        duration?: number;
        y?: number;
        scale?: number;
        blur?: boolean;
        class?: string;
    }

    let {
        delay = 0,
        duration = 800,
        y = 60,
        scale = 0.95,
        blur = false,
        class: className = '',
        children,
    }: Props & { children?: any } = $props();

    let element: HTMLElement;

    // Initialize Tweens with props (captures initial values intentionally)
    // svelte-ignore state_referenced_locally
    const opacity = new Tween(0, { duration, easing: cubicOut });
    // svelte-ignore state_referenced_locally
    const translateY = new Tween(y, { duration, easing: cubicOut });
    // svelte-ignore state_referenced_locally
    const scaleValue = new Tween(scale, { duration: duration * 1.2, easing: elasticOut });
    // svelte-ignore state_referenced_locally
    const blurValue = new Tween(20, { duration, easing: cubicOut });

    onMount(() => {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
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

    let blurStyle = $derived(blur ? `filter: blur(${blurValue.current}px);` : '');
</script>

<div
    bind:this={element}
    class={className}
    style="opacity: {opacity.current}; transform: translateY({translateY.current}px) scale({scaleValue.current}); {blurStyle};"
>
    {@render children?.()}
</div>
