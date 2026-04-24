<script lang="ts">
    import { onMount } from 'svelte';
    import { fade } from 'svelte/transition';

    interface Props {
        colorSet?: string[];
    }

    let { colorSet = [
        'rgba(210, 109, 63, 0.12)', // Warm Coral
        'rgba(174, 192, 173, 0.15)', // Sage
        'rgba(238, 202, 157, 0.2)', // Sandy
        'rgba(15, 23, 42, 0.04)', // Slate
    ] }: Props = $props();

    let canvas: HTMLCanvasElement;
    let ctx: CanvasRenderingContext2D;
    let width = $state(0);
    let height = $state(0);
    let isVisible = $state(false);

    // Mesh points
    let points: { x: number; y: number; vx: number; vy: number; colorIndex: number; size: number }[] = [];

    const init = () => {
        if (!canvas) return;
        ctx = canvas.getContext('2d')!;
        resize();

        points = Array.from({ length: 12 }, () => ({
            x: Math.random() * width,
            y: Math.random() * height,
            vx: (Math.random() - 0.5) * 0.4,
            vy: (Math.random() - 0.5) * 0.4,
            colorIndex: Math.floor(Math.random() * colorSet.length),
            size: Math.random() * 500 + 400,
        }));

        animate();
    };

    const resize = () => {
        width = window.innerWidth;
        height = window.innerHeight;
        canvas.width = width;
        canvas.height = height;
    };

    const animate = () => {
        if (!ctx) return;
        ctx.clearRect(0, 0, width, height);

        points.forEach((p) => {
            p.x += p.vx;
            p.y += p.vy;

            if (p.x < -p.size) p.x = width + p.size;
            if (p.x > width + p.size) p.x = -p.size;
            if (p.y < -p.size) p.y = height + p.size;
            if (p.y > height + p.size) p.y = -p.size;

            const color = colorSet[p.colorIndex] || colorSet[0];
            const gradient = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.size);
            gradient.addColorStop(0, color);
            gradient.addColorStop(1, 'transparent');

            ctx.fillStyle = gradient;
            ctx.fillRect(0, 0, width, height);
        });

        requestAnimationFrame(animate);
    };

    onMount(() => {
        init();
        isVisible = true;
        window.addEventListener('resize', resize);
        return () => window.removeEventListener('resize', resize);
    });
</script>

<div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden bg-[#FDFDFB]" in:fade={{ duration: 2000 }}>
    <canvas bind:this={canvas} class="h-full w-full opacity-70 filter blur-3xl"></canvas>
    
    <!-- Noise overlay -->
    <div class="absolute inset-0 opacity-[0.03] contrast-150 brightness-150" 
         style="background-image: url('https://grainy-gradients.vercel.app/noise.svg');"></div>
</div>
