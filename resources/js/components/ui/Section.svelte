<script lang="ts">
    import type { Snippet } from 'svelte';

    interface Props {
        title?: string;
        subtitle?: string;
        actions?: Snippet;
        class?: string;
        children: Snippet;
    }

    let { title, subtitle, actions, class: className = '', children }: Props = $props();
</script>

<section class="space-y-6 {className}">
    {#if title || subtitle || actions}
        <div class="flex items-center justify-between">
            <div>
                {#if title}
                    <h3 class="text-xl font-bold tracking-widest text-slate-900 uppercase">
                        {title}
                    </h3>
                {/if}
                {#if subtitle}
                    <p class="mt-1 text-sm text-slate-500">{subtitle}</p>
                {/if}
            </div>
            {#if actions}
                <div class="flex items-center gap-3">
                    {@render actions()}
                </div>
            {/if}
        </div>
    {/if}

    {@render children()}
</section>
