<script>
    export let icon;
    export let title;
    export let items = [];
</script>

<div class="p-8 rounded-[2rem] bg-slate-900 text-white relative overflow-hidden">
    <div class="absolute -top-10 -right-10 w-32 h-32 bg-primary-500/10 rounded-full blur-2xl"></div>
    <div class="flex items-start gap-4 relative z-10">
        <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center shrink-0">
            <svelte:component this={icon} size={24} class="text-primary-400" />
        </div>
        <div>
            <h4 class="text-sm font-bold tracking-widest uppercase mb-3">{title}</h4>
            <ul class="space-y-2 text-xs text-slate-400 font-medium leading-relaxed">
                {#each items as item}
                    <li>• {item}</li>
                {/each}
            </ul>
        </div>
    </div>
</div>
