<script lang="ts">
    import { onMount } from 'svelte';
    import { stagger, timeline } from 'svelte/motion';
    import { fade, fly } from 'svelte/transition';
    import { cubicOut, elasticOut, backOut } from 'svelte/easing';

    interface Props {
        staggerDelay?: number;
        class?: string;
    }

    let {
        staggerDelay = 100,
        class: className = '',
        children,
    }: Props & { children?: any } = $props();

    let container: HTMLElement;
    let visible = $state(false);
    let childElements: HTMLElement[] = [];

    onMount(() => {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting && !visible) {
                        visible = true;
                        animateChildren();
                    }
                });
            },
            { threshold: 0.1 }
        );

        observer.observe(container);

        return () => observer.disconnect();
    });

    const animateChildren = () => {
        if (!container) return;

        const elements = container.querySelectorAll('[data-animate]');

        elements.forEach((el, i) => {
            const domEl = el as HTMLElement;
            domEl.style.opacity = '0';
            domEl.style.transform = 'translateY(40px) scale(0.9)';
            domEl.style.transition = `all 0.6s ${cubicOut} ${i * staggerDelay}ms`;
        });

        requestAnimationFrame(() => {
            elements.forEach((el) => {
                const domEl = el as HTMLElement;
                domEl.style.opacity = '1';
                domEl.style.transform = 'translateY(0) scale(1)';
            });
        });
    };
</script>

<div bind:this={container} class={className}>
    {@render children?.()}
</div>
