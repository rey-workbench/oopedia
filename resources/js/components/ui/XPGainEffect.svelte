<script lang="ts">
    import { Star } from 'lucide-svelte';
    import { scale } from 'svelte/transition';
    import { elasticOut } from 'svelte/easing';
    import { xpAnimationState } from '@/states/ui/xpAnimation.svelte';

    interface StarItem {
        id: number;
        delay: number;
        duration: number;
        size: number;
        rotation: number;
        pathX: number;
        pathY: number;
    }

    let stars = $state<StarItem[]>([]);
    let targetX = $state(0);
    let targetY = $state(0);
    let showStars = $state(false);

    function updateTarget() {
        const badge = document.getElementById('xp-badge');
        if (badge) {
            const rect = badge.getBoundingClientRect();
            targetX = rect.left + rect.width / 2;
            targetY = rect.top + rect.height / 2;
            
            // Add a temporary pulse class to the badge
            badge.classList.add('animate-xp-pulse');
            setTimeout(() => badge.classList.remove('animate-xp-pulse'), 1500);
        }
    }

    $effect(() => {
        if (xpAnimationState.active) {
            updateTarget();
            
            // Generate stars
            stars = Array.from({ length: xpAnimationState.amount }).map((_, i) => ({
                id: Math.random(),
                delay: i * 80,
                duration: 800 + Math.random() * 400,
                size: 16 + Math.random() * 12,
                rotation: Math.random() * 360,
                pathX: (Math.random() - 0.5) * 100, // Initial burst spread
                pathY: (Math.random() - 0.5) * 100
            }));
            
            showStars = true;
            setTimeout(() => {
                showStars = false;
            }, 2000);
        }
    });
</script>

{#if xpAnimationState.active && showStars}
    <div class="pointer-events-none fixed inset-0 z-10002 overflow-hidden">
        {#each stars as star (star.id)}
            <div
                class="absolute"
                style="
                    left: {xpAnimationState.startX}px; 
                    top: {xpAnimationState.startY}px;
                    --tx: {targetX - xpAnimationState.startX}px;
                    --ty: {targetY - xpAnimationState.startY}px;
                    --dx: {star.pathX}px;
                    --dy: {star.pathY}px;
                    --delay: {star.delay}ms;
                    --duration: {star.duration}ms;
                    --rotation: {star.rotation}deg;
                "
            >
                <div 
                    class="flying-star text-amber-400"
                    in:scale={{ duration: 400, delay: star.delay, easing: elasticOut }}
                >
                    <Star 
                        size={star.size} 
                        fill="currentColor" 
                        class="drop-shadow-[0_0_10px_rgba(251,191,36,0.8)]"
                        style="transform: rotate({star.rotation}deg);"
                    />
                    
                    <!-- Sparkle trail -->
                    <div class="sparkle absolute inset-0"></div>
                </div>
            </div>
        {/each}
    </div>
{/if}

<style>
    .flying-star {
        animation: fly-to-target var(--duration) cubic-bezier(0.45, 0, 0.55, 1) var(--delay) forwards;
        opacity: 0;
    }

    @keyframes fly-to-target {
        0% {
            transform: translate(0, 0) scale(0) rotate(0deg);
            opacity: 0;
            filter: blur(5px);
        }
        20% {
            transform: translate(var(--dx), var(--dy)) scale(1.5) rotate(var(--rotation));
            opacity: 1;
            filter: blur(0);
        }
        80% {
            opacity: 1;
        }
        100% {
            transform: translate(var(--tx), var(--ty)) scale(0.3) rotate(calc(var(--rotation) * 3));
            opacity: 0;
        }
    }

    .sparkle {
        position: absolute;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, white 0%, transparent 70%);
        mix-blend-mode: overlay;
        animation: sparkle-flicker 0.4s infinite alternate;
    }

    @keyframes sparkle-flicker {
        from { transform: scale(0.8); opacity: 0.5; }
        to { transform: scale(1.2); opacity: 1; }
    }

    :global(.animate-xp-pulse) {
        animation: xp-badge-pop 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    @keyframes xp-badge-pop {
        0%, 100% { transform: scale(1); box-shadow: 0 0 0 rgba(251, 191, 36, 0); }
        30% { 
            transform: scale(1.25); 
            filter: brightness(1.3);
            box-shadow: 0 0 20px rgba(251, 191, 36, 0.6);
            border-color: #fbbf24;
        }
        50% { transform: scale(0.95); }
    }
</style>
