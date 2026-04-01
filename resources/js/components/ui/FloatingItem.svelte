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
        rotation?: number; 
        opacity?: number; 
        blur?: string;   // Tailind blur class (e.g. 'blur-sm', 'blur-md', or '')
        zIndex?: number;
        depth?: number;  // How strongly this item reacts to the mouse parallax (e.g. 10 to 50)
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
        rotation = 0,
        opacity = 0.8,
        blur = '',
        zIndex = 0,
        depth = 20,
        parallaxX = 0,
        parallaxY = 0
    }: Props = $props();
    
    let isVisible = $state(false);

    onMount(() => {
        setTimeout(() => {
            isVisible = true;
        }, delay);
    });
</script>

{#if isVisible}
    <div 
        class="absolute pointer-events-none transition-[opacity,transform] duration-1000"
        style:top={top}
        style:bottom={bottom}
        style:left={left}
        style:right={right}
        style:width={width}
        style:height={height}
        style:z-index={zIndex}
        style:transform={`translate(${parallaxX * depth}px, ${parallaxY * depth}px)`}
        in:fade={{ duration: 1500 }}
    >
        <div 
            class="w-full h-full rounded-[14px] bg-white shadow-[0_15px_30px_rgba(0,0,0,0.1)] border border-black/[0.04] overflow-hidden {blur}"
            style:opacity
            style:transform={`rotate(${rotation}deg)`}
        >
            <img 
                src={image} 
                alt="decoration" 
                class="w-full h-full object-cover" 
            />
        </div>
    </div>
{/if}
