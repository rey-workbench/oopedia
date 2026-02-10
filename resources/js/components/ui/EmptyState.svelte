<script>
    import { FolderOpen } from "lucide-svelte";
    export let title = "Data Tidak Ditemukan";
    export let description =
        "Maaf, kami tidak dapat menemukan data yang Anda cari saat ini.";
    export let icon = FolderOpen;
    let className = "";
    export { className as class };
</script>

<div
    class={`flex flex-col items-center justify-center py-20 px-6 text-center bg-white rounded-[2.5rem] border-2 border-dashed border-slate-100 ${className}`}
>
    <div
        class="w-24 h-24 bg-slate-50 rounded-[2rem] flex items-center justify-center text-slate-200 text-4xl mb-8 shadow-inner"
    >
        {#if typeof icon === "string"}
            <i class={icon}></i>
        {:else if icon}
            <svelte:component this={icon} size={48} strokeWidth={1.5} />
        {/if}
    </div>

    <h3
        class="text-2xl font-bold text-slate-900 tracking-widest uppercase mb-3"
    >
        {title}
    </h3>

    <p class="text-slate-400 font-medium max-w-sm mx-auto leading-relaxed">
        {description}
    </p>

    <div class="mt-10">
        <slot />
    </div>
</div>
