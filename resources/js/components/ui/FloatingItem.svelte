<script lang="ts">
    import { onMount } from 'svelte';
    import { fade } from 'svelte/transition';

    interface Props {
        image: string;
        delay?: number;
        // Exact styling overrides for a curated, messy desk layout
        top?: string;
        bottom?: string;
        left?: string;
        right?: string;
        width?: string;
        height?: string;
        mobileWidth?: string;
        mobileHeight?: string;
        mobileZIndex?: number;
        rotation?: number;
        opacity?: number;
        blur?: string; // Tailind blur class (e.g. 'blur-sm', 'blur-md', or '')
        zIndex?: number;
        depth?: number; // How strongly this item reacts to the mouse parallax (e.g. 10 to 50)
        // Store reference passed down from parent
        parallaxX?: number;
        parallaxY?: number;
    }

    let {
        image,
        delay = 0,
        top,
        bottom,
        left,
        right,
        width = '60px',
        height = '60px',
        mobileWidth = '50px',
        mobileHeight = '50px',
        mobileZIndex = -1,
        rotation = 0,
        opacity = 0.8,
        blur = '',
        zIndex = 0,
        depth = 20,
        parallaxX = 0,
        parallaxY = 0,
    }: Props = $props();

    let isVisible = $state(false);
    let windowWidth = $state(1920);
    let isMobile = $derived(windowWidth < 640);

    // Responsive values
    let effectiveWidth = $derived(isMobile ? mobileWidth : width);
    let effectiveHeight = $derived(isMobile ? mobileHeight : height);
    let effectiveZIndex = $derived(isMobile ? mobileZIndex : zIndex);

    onMount(() => {
        setTimeout(() => {
            isVisible = true;
        }, delay);
    });
</script>

<svelte:window bind:innerWidth={windowWidth} />

{#if isVisible}
    <div
        class="pointer-events-none absolute transition-[opacity,transform] duration-1000 {!isMobile
            ? ''
            : 'scale-75'}"
        style:top
        style:bottom
        style:left
        style:right
        style:width={effectiveWidth}
        style:height={effectiveHeight}
        style:z-index={effectiveZIndex}
        style:transform={`translate(${parallaxX * (isMobile ? depth * 0.3 : depth)}px, ${parallaxY * (isMobile ? depth * 0.3 : depth)}px)`}
        in:fade={{ duration: 1500 }}
    >
        <div
            class="border-cosmos-border h-full w-full overflow-hidden rounded-2xl border-2 bg-white {blur}"
            style:opacity={isMobile ? opacity * 0.5 : opacity}
            style:transform={`rotate(${rotation}deg)`}
        >
            <img src={image} alt="decoration" class="h-full w-full object-cover" />
        </div>
    </div>
{/if}
